<link href="{{ asset('assets/global/css/iziToast.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/global/css/iziToast_custom.css') }}" rel="stylesheet">
<script src="{{ asset('assets/global/js/iziToast.min.js') }}"></script>

<script>
    "use strict";
    const colors = {
        success: '#28c76f',
        error: '#eb2222',
        warning: '#ff9f43',
        info: '#1e9ff2',
    }

    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-times-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-exclamation-circle',
    }

    // HEX flags keep any stray markup inside notify payloads inert inside the
    // <script> block (<, >, &, ' are always escaped)
    const notifications = @php echo json_encode(session('notify', []), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) @endphp;
    const errors = @php echo json_encode(isset($errors) ? collect($errors->all())->unique() : [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) @endphp;

    // V2.0 upgrade reward: flash a dedicated toast when an upgrade granted pins
    const pinReward = @php echo json_encode(session('pin_reward'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) @endphp;
    if (pinReward) {
        notifications.push(['success', pinReward]);
    }


    const triggerToaster = (status, message) => {
        iziToast[status]({
            title: status.charAt(0).toUpperCase() + status.slice(1),
            message: message,
            position: "topRight",
            backgroundColor: '#fff',
            icon: icons[status],
            iconColor: colors[status],
            progressBarColor: colors[status],
            titleSize: '1rem',
            messageSize: '1rem',
            titleColor: '#474747',
            messageColor: '#a2a2a2',
            transitionIn: 'obunceInLeft'
        });
    }

    if (notifications.length) {
        notifications.forEach(element => {
            triggerToaster(element[0], element[1]);
        });
    }

    if (errors.length) {
        errors.forEach(error => {
            triggerToaster('error', error);
        });
    }

    function notify(status, message) {
        if (typeof message == 'string') {
            triggerToaster(status, message);
        } else {
            $.each(message, (i, val) => triggerToaster(status, val));
        }
    }
</script>
