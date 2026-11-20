<?php
// installed guard: refuse to run once core/.env exists (prevents takeover of a live site)
// must stay enabled on any deployment; delete core/.env intentionally to re-install
if (file_exists(__DIR__ . '/../core/.env')) {
	http_response_code(403);
	exit('This application is already installed. For security, delete the install directory.');
}
$itemName = 'LeadxGPT';

error_reporting(E_ALL);
$action = isset($_GET['action']) ? $_GET['action'] : '';
function appUrl()
{
	$scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
	$current = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$exp = explode('?action', $current);
	$url = str_replace('index.php', '', $exp[0]);
	// the wizard lives under /install/  -  never let that path leak into the
	// generated APP_URL (it poisons password-reset mails and deep links)
	$url = preg_replace('#/install/?$#', '/', $url);
	return $url;
}
if ($action == 'requirements') {
	$passed = [];
	$failed = [];
	$requiredPHP = 8.3;
	$currentPHP = explode('.', PHP_VERSION)[0] . '.' . explode('.', PHP_VERSION)[1];
	if (version_compare($currentPHP, $requiredPHP, '>=')) {
		$passed[] = "PHP version $requiredPHP+ is required";
	} else {
		$failed[] = "PHP version $requiredPHP+ is required. Your current PHP version is $currentPHP";
	}
	$extensions = ['BCMath', 'Ctype', 'cURL', 'DOM', 'Fileinfo', 'GD', 'JSON', 'Mbstring', 'OpenSSL', 'PCRE', 'PDO', 'pdo_mysql', 'Tokenizer', 'XML','Filter','Hash','Session','zip'];
	foreach ($extensions as $extension) {
		if (extension_loaded($extension)) {
			$passed[] = strtoupper($extension) . ' PHP Extension is required';
		} else {
			$failed[] = strtoupper($extension) . ' PHP Extension is required';
		}
	}
	if (function_exists('curl_version')) {
		$passed[] = 'Curl via PHP is required';
	} else {
		$failed[] = 'Curl via PHP is required';
	}
	if (file_get_contents(__FILE__)) {
		$passed[] = 'file_get_contents() is required';
	} else {
		$failed[] = 'file_get_contents() is required';
	}
	if (ini_get('allow_url_fopen')) {
		$passed[] = 'allow_url_fopen() is required';
	} else {
		$failed[] = 'allow_url_fopen() is required';
	}
	// core/ itself must be writable  -  the installer writes core/.env there;
	// a read-only core/ would only fail at the LAST step (after the DB import)
	$dirs = ['../core/', '../core/bootstrap/cache/', '../core/storage/', '../core/storage/app/', '../core/storage/framework/', '../core/storage/logs/'];
	foreach ($dirs as $dir) {
		if (!is_dir($dir)) { continue; }
		$perm = substr(sprintf('%o', fileperms($dir)), -4);
		// writability is the actual requirement  -  a 0700 directory owned by
		// the web user is fine, so test is_writable() instead of the mode string
		if (is_writable($dir)) {
			$passed[] = str_replace("../", "", $dir) . ' is writable';
		} else {
			$failed[] = str_replace("../", "", $dir) . ' must be writable. Current permission is ' . $perm;
		}
	}
	if (file_exists('database.sql')) {
		$passed[] = 'database.sql should be available';
	} else {
		$failed[] = 'database.sql should be available';
	}
	if (file_exists('../.htaccess')) {
		$passed[] = '".htaccess" should be available in root directory';
	} else {
		$failed[] = '".htaccess" should be available in root directory';
	}
}

if ($action == 'result') {
	$response['error'] = 'ok';
	$response['message'] = '';
	$response['body'] = file_get_contents(__DIR__ . '/env.template');
	$response['location'] = '../core/.env';

	// mutual exclusion: two concurrent installs would both pass the
	// already-installed check and race the database import. A directory
	// marker is atomic, so a second request fails the mkdir.
	$lockDir = __DIR__ . '/install.lock';
	if (!@mkdir($lockDir)) {
		$response['error'] = 'error';
		$response['message'] = 'An installation is already in progress. Try again in a moment.';
	} else {
		register_shutdown_function(function () use ($lockDir) { @rmdir($lockDir); });
	}

	$dbPort = (isset($_POST['db_port']) && $_POST['db_port'] !== '') ? $_POST['db_port'] : '3306';
	if ($response['error'] == 'ok' && !ctype_digit($dbPort)) {
		$response['error'] = 'error';
		$response['message'] = 'Database port must be a number.';
	}
	if ($response['error'] == 'ok' && ($_POST['db_name'] === '' || strpos($_POST['db_name'], ';') !== false || strpos($_POST['db_name'], "\0") !== false)) {
		$response['error'] = 'error';
		$response['message'] = 'The database name contains characters that are not allowed.';
	}

	$db = null;
	if ($response['error'] == 'ok') {
	try {
		$db = new PDO("mysql:host={$_POST['db_host']};port={$dbPort};dbname={$_POST['db_name']}", $_POST['db_user'], $_POST['db_pass']);
		$dbinfo = $db->query('SELECT VERSION()')->fetchColumn();

		if (strpos(strtolower($dbinfo), 'mariadb') !== false) {
			$engine = 'mariadb';
			$version = implode('.', array_slice(explode('.', $dbinfo), 0, 2));
		} else {
			$engine = 'mysql';
			$version = implode('.', array_slice(explode('.', $dbinfo), 0, 2));
		}

		if (strtolower($engine) == 'mariadb') {
			if (!version_compare($version, '10.6','>=')) {
				$response['error'] = 'error';
				$response['message'] = 'MariaDB 10.6+ Or MySQL 8.0+ Required. <br> Your current version is MariaDB ' . $version;
			}
		} else {
			if (!version_compare($version, '8.0','>=')) {
				$response['error'] = 'error';
				$response['message'] = 'MariaDB 10.6+ Or MySQL 8.0+ Required. <br> Your current version is MySQL ' . $version;
			}
		}
	} catch (Exception $e) {
		$response['error'] = 'error';
		$response['message'] = 'Database Credential is Not Valid.<br>' . htmlspecialchars($e->getMessage());
	}
	}

	// the target database must be EMPTY  -  importing over existing tables
	// either silently coexists or aborts halfway, and both destroy the
	// owner's ability to retry; catch it while nothing has happened yet
	$existingTables = [];
	if ($response['error'] == 'ok' && $db) {
		$stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ?");
		$stmt->execute([$_POST['db_name']]);
		if ((int) $stmt->fetchColumn() > 0) {
			$response['error'] = 'error';
			$response['message'] = 'The database "' . htmlspecialchars($_POST['db_name']) . '" is not empty. Use an empty database for installation.';
		}
	}

	if ($response['error'] == 'ok') {
		try {
			$query = file_get_contents(__DIR__ . "/database.sql");
			$stmt = $db->prepare($query);
			$stmt->execute();
			$stmt->closeCursor();
		} catch (Exception $e) {
			// no automatic table rollback: identifiers cannot be bound safely
			// in DDL, so cleanup is a manual step. The empty-database check
			// before the import keeps the retry path clear once the partial
			// tables are dropped from the hosting database tool.
			$response['error'] = 'error';
			$response['message'] = 'Problem Occurred When Importing Database!<br>'
				. 'If any tables were created, drop them in your hosting database tool '
				. '(or choose a fresh empty database) and install again.<br>'
				. htmlspecialchars($e->getMessage());
		}
	}

	if ($response['error'] == 'ok') {
		try {
			$arr['key'] = base64_encode(random_bytes(32));
			// hex, not base64: the secret goes into a crontab URL query string,
			// and base64 '+' decodes to a space server-side (URL-unsafe)
			$cronSecret = bin2hex(random_bytes(24));
			// hidden admin path: every installation gets a fresh random prefix
			// (lowercase letters + digits, unguessable, shown once on this page)
			$adminPrefix = 'adm' . substr(bin2hex(random_bytes(4)), 0, 6);
			$arr['url'] = $_POST['url'];
			$arr['db_host'] = $_POST['db_host'];
			$arr['db_port'] = $dbPort;
			$arr['db_name'] = $_POST['db_name'];
			$arr['db_user'] = $_POST['db_user'];
			$arr['db_pass'] = $_POST['db_pass'];
			$envBody = $response['body'];
			foreach ($arr as $key => $value) {
			    // values go into dotenv double-quoted strings (see env.template)
			    //  -  escape the characters that would break out or interpolate
			    $safe = str_replace(['\\', '"', '$', "\r", "\n"], ['\\\\', '\\"', '\\$', '', ''], $value);
			    $envBody = str_replace('{{' . $key . '}}', $safe, $envBody);
			}
			$envBody = str_replace('{{app_name}}', $itemName, $envBody);
			$envBody = preg_replace('/^CRON_SECRET=$/m', 'CRON_SECRET=' . $cronSecret, $envBody);
			$envBody = preg_replace('/^ADMIN_PREFIX=\{\{admin_prefix\}\}$/m', 'ADMIN_PREFIX=' . $adminPrefix, $envBody);

			// file_put_contents creates core/.env when missing;
			// plaintext DB credentials + APP_KEY  -  owner-readable only
			file_put_contents($response['location'], $envBody);
			@chmod($response['location'], 0600);
		} catch (Exception $e) {
			// no automatic table rollback here either: same DDL-safety rule as
			// the import failure path. The .env write failing rarely leaves
			// partial tables, and the manual cleanup guidance on the import
			// error path covers the retry.
			$response['error'] = 'error';
			$response['message'] = 'Problem Occurred When Writing Environment File.<br>'
				. 'Fix the write permission on the core folder and install again.';
		}
	}

	if ($response['error'] == 'ok') {
		try {
			$stmt = $db->prepare("UPDATE admins SET email = ?, username = ?, password = ? WHERE username = 'admin'");
			$stmt->execute([$_POST['email'], $_POST['admin_user'], password_hash($_POST['admin_pass'], PASSWORD_BCRYPT, ['cost' => 12])]);
			// the shipped SQL seed contains a publicly-known admin hash  -  it
			// MUST be replaced. Zero rows here means the shipped credential
			// would stay valid: fail the install loudly instead.
			if ($stmt->rowCount() === 0) {
				$response['error'] = 'error';
				$response['message'] = 'The installer could not replace the shipped admin account (seed row missing). Installation aborted for security.';
			}
		} catch (Exception $e) {
			$response['error'] = 'error';
			$response['message'] = 'The installer was unable to set the credentials of admin.<br>' . htmlspecialchars($e->getMessage());
		}
	}

	if ($response['error'] == 'ok') {
		// the seeded demo e-pin code ships publicly  -  randomize its code so
		// it cannot be redeemed by strangers before the owner uses it (value
		// and demo purpose stay the same)
		try {
			$stmt = $db->prepare("UPDATE pins SET pin = CONCAT('LEADX-', SUBSTRING(MD5(RAND()), 1, 12), '-', FLOOR(RAND()*9000)+1000) WHERE pin = 'LEADX-INIT-50-0001'");
			$stmt->execute();
		} catch (Exception $e) { /* non-fatal: worst case the shipped code stays */ }
	}

	if ($response['error'] == 'ok' && !empty($_POST['remove_demo'])) {
		// the shipped demo member has publicly-documented credentials  -  a
		// production install must not keep it (evaluation installs can untick).
		// The demo ALSO owns sample listings, events and leads: removing only
		// the user would orphan them, so the whole demo dataset goes.
		try {
			$demoId = (int) $db->query("SELECT id FROM users WHERE username = 'demo'")->fetchColumn();
			if ($demoId) {
				foreach ([
					'listing_images' => "listing_id IN (SELECT id FROM listings WHERE user_id = $demoId)",
					'reviews' => "listing_id IN (SELECT id FROM listings WHERE user_id = $demoId)",
					'leads' => "listing_id IN (SELECT id FROM listings WHERE user_id = $demoId)",
					'favorite_listings' => "user_id = $demoId",
					'listings' => "user_id = $demoId",
					'event_attendees' => "event_id IN (SELECT id FROM events WHERE user_id = $demoId)",
					'event_participations' => "event_id IN (SELECT id FROM events WHERE user_id = $demoId)",
					'events' => "user_id = $demoId",
					'user_plans' => "user_id = $demoId",
					'transactions' => "user_id = $demoId",
					'user_extras' => "user_id = $demoId",
				] as $tbl => $where) {
					$db->exec("DELETE FROM $tbl WHERE $where");
				}
				$db->exec("DELETE FROM users WHERE id = $demoId");
			}
		} catch (Exception $e) { /* non-fatal: owner can delete from admin */ }
	}

	if ($response['error'] == 'ok') {
		// seeded demo events ship with fixed 2025 dates  -  shift them into
		// the future so a fresh install shows upcoming events
		try {
			$db->exec("UPDATE events SET start_time = DATE_ADD(NOW(), INTERVAL 10 DAY), end_time = DATE_ADD(DATE_ADD(NOW(), INTERVAL 10 DAY), INTERVAL 3 HOUR) WHERE title = 'Annual Business Networking Summit'");
			$db->exec("UPDATE events SET start_time = DATE_ADD(NOW(), INTERVAL 20 DAY), end_time = DATE_ADD(DATE_ADD(NOW(), INTERVAL 20 DAY), INTERVAL 3 HOUR) WHERE title = 'Digital Marketing Workshop'");
			$db->exec("UPDATE events SET start_time = DATE_ADD(NOW(), INTERVAL 30 DAY), end_time = DATE_ADD(DATE_ADD(NOW(), INTERVAL 30 DAY), INTERVAL 3 HOUR) WHERE title = 'Trade Show Expo'");
		} catch (Exception $e) { /* non-fatal: cosmetic dates */ }
	}

	// the mkdir install lock releases itself via register_shutdown_function
}
$sectionTitle =  empty($action) ? 'Welcome' : $action;
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>LeadxGPT Easy Installer</title>
	<link rel="stylesheet" href="../assets/global/css/bootstrap.min.css">
	<link rel="stylesheet" href="../assets/global/css/all.min.css">
	<link rel="stylesheet" href="../assets/global/css/installer.css">
	<link rel="shortcut icon" href="../assets/global/images/favicon.png" type="image/x-icon">
</head>

<body>
	<header class="py-3 border-bottom border-primary bg--dark">
		<div class="container">
			<div class="d-flex align-items-center justify-content-between header gap-3">
				<h3 class="title">LeadxGPT</h3>
				<h3 class="title">Easy Installer</h3>
			</div>
		</div>
	</header>
	<div class="installation-section padding-bottom padding-top">
		<div class="container">
			<div class="installation-wrapper">
				<div class="install-content-area">
					<div class="install-item">
						<h3 class="title text-center"><?php echo $sectionTitle; ?></h3>
						<div class="box-item">
							<?php
							if ($action == 'result') {
								echo '<div class="success-area text-center">';
								if (isset($response['error']) && $response['error'] == 'ok') {
									echo '<h2 class="text-success text-uppercase mb-3">Your system has been installed successfully!</h2>';
									if (isset($adminPrefix)) {
										echo '<p class="mb-1"><strong>Write this down:</strong> your control center lives at a hidden address
										(it does NOT appear anywhere on the site, and search engines are told not to look there):</p>';
										echo '<p><code class="user-select-all">' . htmlspecialchars(rtrim($arr['url'], '/') . '/' . $adminPrefix) . '</code></p>';
									}
									if (isset($cronSecret)) {
										echo '<p class="mb-1">Your cron secret (add <code>?key=&lt;secret&gt;</code> to scheduled /cron calls):</p>';
										echo '<p><code class="user-select-all">' . htmlspecialchars($cronSecret) . '</code></p>';
									}
									if (isset($response['message']) && $response['message']) {
										echo '<h5 class="text-warning mb-3">' . $response['message'] . '</h5>';
									}
									echo '<p class="text-danger lead my-5">Please delete the "install" folder from the server.</p>';
									echo '<div class="warning"><a href="' . appUrl() . '" class="theme-button choto">Go to website</a></div>';
								} else {
									if (isset($response['message']) && $response['message']) {
										echo '<h3 class="text-danger mb-3">' . $response['message'] . '</h3>';
									} else {
										echo '<h3 class="text-danger mb-3">Your Server is not Capable to Handle the Request.</h3>';
									}
									echo '<div class="warning mt-2"><h5 class="mb-4 fw-normal">Please try again.</h5><a href="?action=information" class="theme-button choto me-1 mb-3">Try Again</a></div>';
								}
								echo '</div>';
							} elseif ($action == 'information') {
							?>
								<form action="?action=result" method="post" class="information-form-area mb--20">
									<div class="info-item">
										<h5 class="font-weight-normal mb-2">Website URL</h5>
										<div class="row">
											<div class="information-form-group col-12">
												<input name="url" value="<?php echo appUrl(); ?>" type="text" required>
											</div>
										</div>
									</div>
									<div class="info-item">
										<h5 class="font-weight-normal mb-2">Database Details</h5>
										<div class="row">
											<div class="information-form-group col-sm-6">
												<input type="text" name="db_name" placeholder="Database Name" required>
											</div>
											<div class="information-form-group col-sm-6">
												<input type="text" name="db_host" placeholder="Database Host" required>
											</div>
											<div class="information-form-group col-sm-6">
												<input type="text" name="db_port" value="3306" placeholder="Database Port" required>
											</div>
											<div class="information-form-group col-sm-6">
												<input type="text" name="db_user" placeholder="Database User" required>
											</div>
											<div class="information-form-group col-sm-6">
												<input class="secure-password" type="text" name="db_pass" placeholder="Database Password">
											</div>
										</div>
									</div>
									<div class="info-item">
										<h5 class="font-weight-normal mb-3">Admin Credential</h5>
										<div class="row">
											<div class="information-form-group col-lg-3 col-sm-6">
												<label>Username</label>
												<input name="admin_user" type="text" placeholder="Admin Username" maxlength="40" required>
											</div>
											<div class="information-form-group col-lg-3 col-sm-6">
												<label>Password</label>
												<input name="admin_pass" type="password" placeholder="Admin Password" required>
											</div>
											<div class="information-form-group col-lg-6">
												<label>Email Address</label>
												<input name="email" placeholder="Your Email address" type="email" maxlength="40" required>
											</div>
										</div>
									</div>
									<div class="info-item">
										<div class="information-form-group form-check ms-2">
											<input class="form-check-input" type="checkbox" name="remove_demo" id="removeDemo" checked>
											<label class="form-check-label" for="removeDemo">Remove the demo member account (recommended for production)</label>
										</div>
										<div class="information-form-group text-end">
											<button type="submit" class="theme-button choto">Install Now</button>
										</div>
									</div>
								</form>
							<?php
							} elseif ($action == 'requirements') {
								$btnText = 'View Detailed Check Result';
								if (count($failed)) {
									$btnText = 'View Passed Check';
									echo '<div class="item table-area"><table class="requirment-table">';
									foreach ($failed as $fail) {
										echo "<tr><td>$fail</td><td><i class='fas fa-times'></i></td></tr>";
									}
									echo '</table></div>';
								}
								if (!count($failed)) {
									echo '<div class="text-center"><i class="far fa-check-circle success-icon text-success"></i><h5 class="my-3">Requirements Check Passed!</h5></div>';
								}
								if (count($passed)) {
									echo '<div class="text-center my-3"><button class="btn passed-btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePassed" aria-expanded="false" aria-controls="collapsePassed">' . $btnText . '</button></div>';
									echo '<div class="collapse mb-4" id="collapsePassed"><div class="item table-area"><table class="requirment-table">';
									foreach ($passed as $pass) {
										echo "<tr><td>$pass</td><td><i class='fas fa-check'></i></td></tr>";
									}
									echo '</table></div></div>';
								}
								echo '<div class="item text-end mt-3">';
								if (count($failed)) {
									echo '<a class="theme-button btn-warning choto" href="?action=requirements">ReCheck <i class="fas fa-sync-alt"></i></a>';
								} else {
									echo '<a class="theme-button choto" href="?action=information">Next Step <i class="fas fa-angle-double-right"></i></a>';
								}
								echo '</div>';
							} else {
							?>
								<div class="item">
									<h4 class="subtitle">Welcome to the LeadxGPT Easy Installer</h4>
									<p> This wizard will set up LeadxGPT on your domain: it creates the database tables, writes the environment file and creates your administrator account. No purchase code or external activation is required — LeadxGPT is fully independent and works on any domain.</p>
								</div>
								<div class="item">
									<h5 class="subtitle font-weight-bold">Before you start:</h5>
									<ul class="check-list">
										<li> Create an empty MySQL/MariaDB database (MariaDB 10.6+ or MySQL 8.0+). </li>
										<li> Keep the database credentials ready.</li>
										<li> Make sure the <code>core/.env</code> file is writable.</li>
									</ul>
								</div>
								<div class="item text-end">
									<a href="?action=requirements" class="theme-button choto">Start, Next Step</a>
								</div>
							<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<footer class="py-3 text-center bg--dark border-top border-primary">
		<div class="container">
			<p class="m-0 font-weight-bold">&copy;<?php echo Date('Y') ?> — LeadxGPT. All rights reserved.</p>
		</div>
	</footer>
	<script src="../assets/global/js/bootstrap.bundle.min.js"></script>
</body>
</html>
