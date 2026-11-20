-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 16, 2026 at 08:36 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `leadxgpt`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `username`, `email_verified_at`, `image`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admins', 'admin@site.com', 'admin', NULL, '667fde574a6e11719656023.png', '$2y$12$3xsG3YEldqrLOnTWJ58dc.VCe/OohCWGnJ8bIUJEI2kvBiiFgu/W.', 'hczwbk5d0zMHnmci0j59Vsp3SfkCg3KEOi50uq16ctP6JAMBlIXWKkQ1SwTk', NULL, '2024-06-29 04:48:13');

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL DEFAULT '0',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `click_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_password_resets`
--

CREATE TABLE `admin_password_resets` (
  `id` bigint UNSIGNED NOT NULL,
  `email` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_logs`
--
CREATE TABLE `ai_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `feature` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tokens` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'YES=1, NO->2',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` bigint UNSIGNED NOT NULL,
  `country_id` int NOT NULL DEFAULT '0',
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cron_job_logs`
--
CREATE TABLE `cron_job_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `cron_job_id` int UNSIGNED NOT NULL DEFAULT '0',
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `duration` int NOT NULL DEFAULT '0',
  `error` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cron_jobs`
--
CREATE TABLE `cron_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alias` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cron_schedule_id` int NOT NULL DEFAULT '0',
  `next_run` datetime DEFAULT NULL,
  `last_run` datetime DEFAULT NULL,
  `is_running` tinyint(1) NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cron_schedules`
--
CREATE TABLE `cron_schedules` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interval` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deposits`
--

CREATE TABLE `deposits` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL DEFAULT '0',
  `plan_id` int UNSIGNED NOT NULL DEFAULT '0',
  `method_code` int UNSIGNED NOT NULL DEFAULT '0',
  `amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `method_currency` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `charge` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `rate` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `final_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `btc_amount` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btc_wallet` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_ref` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trx` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_try` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=>success, 2=>pending, 3=>cancel',
  `from_api` tinyint(1) NOT NULL DEFAULT '0',
  `is_web` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'This will be 1 if the request is from NextJs application',
  `admin_feedback` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `success_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `failed_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_cron` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `price_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'monthly/yearly plan purchase'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `device_tokens`
--

CREATE TABLE `device_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL DEFAULT '0',
  `is_app` tinyint(1) NOT NULL DEFAULT '0',
  `token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `extensions`
--

CREATE TABLE `extensions` (
  `id` bigint UNSIGNED NOT NULL,
  `act` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `script` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `shortcode` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'object',
  `support` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'help section',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=>enable, 2=>disable',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `extensions`
--

INSERT INTO `extensions` (`id`, `act`, `name`, `description`, `image`, `script`, `shortcode`, `support`, `status`, `created_at`, `updated_at`) VALUES
(1, 'tawk-chat', 'Tawk.to', 'Key location is shown bellow', 'tawky_big.png', '<script>\r\n                        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();\r\n                        (function(){\r\n                        var s1=document.createElement(\"script\"),s0=document.getElementsByTagName(\"script\")[0];\r\n                        s1.async=true;\r\n                        s1.src=\"https://embed.tawk.to/{{app_key}}\";\r\n                        s1.charset=\"UTF-8\";\r\n                        s1.setAttribute(\"crossorigin\",\"*\");\r\n                        s0.parentNode.insertBefore(s1,s0);\r\n                        })();\r\n                    </script>', '{\"app_key\":{\"title\":\"App Key\",\"value\":\"------\"}}', 'twak.png', 0, '2019-10-18 23:16:05', '2022-03-22 05:22:24'),
(2, 'google-recaptcha2', 'Google Recaptcha 2', 'Key location is shown bellow', 'recaptcha3.png', '\n<script src=\"https://www.google.com/recaptcha/api.js\"></script>\n<div class=\"g-recaptcha\" data-sitekey=\"{{site_key}}\" data-callback=\"verifyCaptcha\"></div>\n<div id=\"g-recaptcha-error\"></div>', '{\"site_key\":{\"title\":\"Site Key\",\"value\":\"----------------------\"},\"secret_key\":{\"title\":\"Secret Key\",\"value\":\"----------------------\"}}', 'recaptcha.png', 0, '2019-10-18 23:16:05', '2024-06-29 04:24:56'),
(3, 'custom-captcha', 'Custom Captcha', 'Just put any random string', 'customcaptcha.png', NULL, '{\"random_key\":{\"title\":\"Random String\",\"value\":\"SecureString\"}}', 'na', 0, '2019-10-18 23:16:05', '2023-08-21 07:55:24'),
(4, 'google-analytics', 'Google Analytics', 'Key location is shown bellow', 'google_analytics.png', '<script async src=\"https://www.googletagmanager.com/gtag/js?id={{measurement_id}}\"></script>\n                <script>\n                  window.dataLayer = window.dataLayer || [];\n                  function gtag(){dataLayer.push(arguments);}\n                  gtag(\"js\", new Date());\n                \n                  gtag(\"config\", \"{{measurement_id}}\");\n                </script>', '{\"measurement_id\":{\"title\":\"Measurement ID\",\"value\":\"------\"}}', 'ganalytics.png', 0, NULL, '2021-05-04 10:19:12'),
(5, 'fb-comment', 'Facebook Comment ', 'Key location is shown bellow', 'Facebook.png', '<div id=\"fb-root\"></div><script async defer crossorigin=\"anonymous\" src=\"https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v4.0&appId={{app_key}}&autoLogAppEvents=1\"></script>', '{\"app_key\":{\"title\":\"App Key\",\"value\":\"----\"}}', 'fb_com.png', 0, NULL, '2022-03-22 05:18:36');

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_logs`
--

CREATE TABLE `affiliate_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `payment_trx` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payer_id` bigint UNSIGNED NOT NULL,
  `beneficiary_id` bigint UNSIGNED DEFAULT NULL,
  `level` int NOT NULL DEFAULT '0',
  `amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'referral' COMMENT 'referral/monoline/matching/company',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_categories`
--

CREATE TABLE `event_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `country_id` bigint UNSIGNED DEFAULT NULL,
  `city_id` bigint UNSIGNED DEFAULT NULL,
  `venue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `cover_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=pending, 1=approved, 2=rejected',
  `rejected_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `event_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=public, 2=internal company event',
  `price` decimal(28,8) NOT NULL DEFAULT 0 COMMENT 'internal event registration price (0=free)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorite_listings`
--

CREATE TABLE `favorite_listings` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `listing_id` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forms`
--

CREATE TABLE `forms` (
  `id` bigint UNSIGNED NOT NULL,
  `act` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `form_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `forms`
--

INSERT INTO `forms` (`id`, `act`, `form_data`, `created_at`, `updated_at`) VALUES
(7, 'kyc', '{\"full_name\":{\"name\":\"Full Name\",\"label\":\"full_name\",\"is_required\":\"required\",\"extensions\":\"\",\"options\":[],\"type\":\"text\"},\"nid_number\":{\"name\":\"NID Number\",\"label\":\"nid_number\",\"is_required\":\"required\",\"extensions\":null,\"options\":[],\"type\":\"text\"},\"gender\":{\"name\":\"Gender\",\"label\":\"gender\",\"is_required\":\"required\",\"extensions\":null,\"options\":[\"Male\",\"Female\",\"Others\"],\"type\":\"select\"},\"you_hobby\":{\"name\":\"You Hobby\",\"label\":\"you_hobby\",\"is_required\":\"required\",\"extensions\":null,\"options\":[\"Programming\",\"Gardening\",\"Traveling\",\"Others\"],\"type\":\"checkbox\"},\"nid_photo\":{\"name\":\"NID Photo\",\"label\":\"nid_photo\",\"is_required\":\"required\",\"extensions\":\"jpg,png\",\"options\":[],\"type\":\"file\"}}', '2022-03-17 02:56:14', '2022-12-17 11:14:11');

-- --------------------------------------------------------

--
-- Table structure for table `frontends`
--

CREATE TABLE `frontends` (
  `id` bigint UNSIGNED NOT NULL,
  `data_keys` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `seo_content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tempname` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `frontends`
--

INSERT INTO `frontends` (`id`, `data_keys`, `data_values`, `seo_content`, `tempname`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'seo.data', '{\"seo_image\":\"1\",\"keywords\":[\"directory listings\",\"businesses\",\"services\",\"listing\"],\"description\":\"Discover the best businesses and services with our comprehensive directory. Find restaurants, hotels, fitness centers, and more. Explore reviews, photos, and opening hours. Start your search today!\",\"social_title\":\"LeadxGPT - Business Directory & Affiliate Platform\",\"social_description\":\"Discover the best businesses and services with our comprehensive directory. Find restaurants, hotels, fitness centers, and more. Explore reviews, photos, and opening hours. Start your search today!\",\"image\":\"667fde1f093fe1719655967.png\"}', NULL, 'basic', '', '2020-07-04 23:42:52', '2024-06-29 04:13:06'),
(24,  'about.content', '{"has_image":"1","heading":"About LeadxGPT","content":"LeadxGPT is a business directory built to connect local businesses with the people looking for them. Members publish a verified business page, get discovered through search and city pages, and receive customer enquiries straight into their lead inbox.","image":"64f32a6a4022e1693657706.png"}',  NULL,  'basic',  NULL,  '2020-10-28 00:51:20',  '2023-09-02 10:58:27'),
(25,  'blog.content', '{"heading":"Latest Tips & Articles","subheading":"Guides and news for business owners using LeadxGPT."}',  NULL,  'basic',  NULL,  '2020-10-28 00:51:34',  '2023-08-12 12:07:41'),
(26, 'blog.element', '{"has_image":["1"],"title":"How to Write a Business Listing That Converts","description_nic":"<p>A great listing answers a customer\'s questions before they ask. Start with a clear first line that says what you do and who you serve, then follow with the services you offer and what makes your business different.</p><p>Use the specific words customers actually search for, such as your neighbourhood, cuisine or specialty. Keep sentences short and put the most important information first.</p><p>Finish with a simple call to action, like “Call today for a free quote”. Listings with a clear next step receive noticeably more enquiries than pages that only describe the business.</p>","image":"64f33ca3c3bb81693662371.png","slug":"how-to-write-a-business-listing-that-converts"}', NULL, 'basic', 'how-to-write-a-business-listing-that-converts', '2020-10-28 00:57:19', '2023-09-02 12:16:12'),
(27, 'contact_us.content', '{\"heading\":\"Get in Touch\"}', NULL, 'basic', NULL, '2020-10-28 00:59:19', '2023-09-02 13:28:38'),
(31, 'social_icon.element', '{\"title\":\"Facebook\",\"social_icon\":\"<i class=\\\"fab fa-facebook\\\"><\\/i>\",\"url\":\"https:\\/\\/www.google.com\\/\"}', NULL, 'basic', NULL, '2020-11-12 04:07:30', '2023-08-13 13:08:08'),
(33, 'feature.content', '{\"heading\":\"asdf\",\"sub_heading\":\"asdf\"}', NULL, 'basic', NULL, '2021-01-03 23:40:54', '2021-01-03 23:40:55'),
(34, 'feature.element', '{\"title\":\"asdf\",\"description\":\"asdf\",\"feature_icon\":\"asdf\"}', NULL, 'basic', NULL, '2021-01-03 23:41:02', '2021-01-03 23:41:02'),
(35, 'service.element', '{\"trx_type\":\"withdraw\",\"service_icon\":\"<i class=\\\"las la-highlighter\\\"><\\/i>\",\"title\":\"asdfasdf\",\"description\":\"asdfasdfasdfasdf\"}', NULL, 'basic', NULL, '2021-03-06 01:12:10', '2021-03-06 01:12:10'),
(36, 'service.content', '{\"trx_type\":\"deposit\",\"heading\":\"asdf fffff\",\"subheading\":\"555\"}', NULL, 'basic', NULL, '2021-03-06 01:27:34', '2022-03-30 08:07:06'),
(39, 'banner.content', '{\"has_image\":\"1\",\"heading\":\"Find, Explore, and Connect with Local Businesses\",\"subheading\":\"Connect with Your City\'s Vibrant Business Community\",\"image\":\"64f33e4d1f14a1693662797.jpg\"}', NULL, 'basic', NULL, '2021-05-02 06:09:30', '2023-09-03 12:51:28'),
(41, 'cookie.data', '{\"short_desc\":\"We may use cookies or any other tracking technologies when you visit our website, including any other media form, mobile website, or mobile application related or connected to help customize the Site and improve your experience.\",\"description\":\"<div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">What information do we collect?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We gather data from you when you register on our site, submit a request, buy any services, react to an overview, or round out a structure. At the point when requesting any assistance or enrolling on our site, as suitable, you might be approached to enter your: name, email address, or telephone number. You may, nonetheless, visit our site anonymously.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">How do we protect your information?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">All provided delicate\\/credit data is sent through Stripe.<br>After an exchange, your private data (credit cards, social security numbers, financials, and so on) won\'t be put away on our workers.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">Do we disclose any information to outside parties?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We don\'t sell, exchange, or in any case move to outside gatherings by and by recognizable data. This does exclude confided in outsiders who help us in working our site, leading our business, or adjusting you, since those gatherings consent to keep this data private. We may likewise deliver your data when we accept discharge is suitable to follow the law, implement our site strategies, or ensure our own or others\' rights, property, or wellbeing.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">Children\'s Online Privacy Protection Act Compliance<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We are consistent with the prerequisites of COPPA (Children\'s Online Privacy Protection Act), we don\'t gather any data from anybody under 13 years old. Our site, items, and administrations are completely coordinated to individuals who are in any event 13 years of age or more established.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">Changes to our Privacy Policy<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">If we decide to change our privacy policy, we will post those changes on this page.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">How long we retain your information?<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">At the point when you register for our site, we cycle and keep your information we have about you however long you don\'t erase the record or withdraw yourself (subject to laws and guidelines).<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\">What we don\\u2019t do with your data<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We don\'t and will never share, unveil, sell, or in any case give your information to different organizations for the promoting of their items or administrations.<\\/p><\\/div>\",\"status\":1}', NULL, 'basic', NULL, '2020-07-04 23:42:52', '2022-09-22 07:29:55'),
(44, 'maintenance.data', '{\"description\":\"<div class=\\\"mb-5\\\" style=\\\"color: rgb(111, 111, 111); font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h2 style=\\\"text-align:center;\\\"><font size=\\\"6\\\">We\'re just tuning up a few things.<\\/font><\\/h2><h3 class=\\\"mb-3\\\" style=\\\"text-align: center; font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif; color: rgb(54, 54, 54);\\\"><p>We apologize for the inconvenience but Front is currently undergoing planned maintenance. Thanks for your patience.<br><\\/p><\\/h3><\\/div>\",\"image\":\"667fb7bb3d0601719646139.png\"}', NULL, 'basic', NULL, '2020-07-04 23:42:52', '2024-06-29 01:30:34'),
(45, 'category.content', '{\"heading\":\"Explore Categories\",\"subheading\":\"Discover the best places to eat, drink and shop near you.\"}', NULL, 'basic', NULL, '2023-08-12 07:13:18', '2023-08-12 07:13:18'),
(46,  'feature_property.content', '{"heading":"Featured Listings","subheading":"Hand-picked businesses our members trust the most."}',  NULL,  'basic',  NULL,  '2023-08-12 07:29:49',  '2023-08-12 07:29:49'),
(47,  'cta.content', '{"has_image":"1","heading":"List your business on LeadxGPT","button":"Join free","button_link":"user/register","background_image":"64d76f5ba6b3a1691840347.png","left_image":"64d76f5c4b8b51691840348.png","right_image":"64d76f5c69ca31691840348.png"}',  NULL,  'basic',  NULL,  '2023-08-12 09:08:34',  '2023-08-28 06:27:11'),
(48, 'popular_city.content', '{\"heading\":\"Most Popular Locations\",\"subheading\":\"Explore the cities where LeadxGPT businesses are growing.\"}', NULL, 'basic', NULL, '2023-08-12 09:13:25', '2023-08-21 09:57:31'),
(49,  'how_work.content', '{"heading":"How It Works","subheading":"From sign-up to your first customer enquiry in three simple steps."}',  NULL,  'basic',  NULL,  '2023-08-12 10:13:35',  '2023-08-30 12:17:03'),
(50, 'how_work.element', '{\"icon\":\"<i class=\\\"las la-user-check\\\"><\\/i>\",\"title\":\"Get Registered\",\"description\":\"Begin by registering on our platform using a valid email address and secure password. After successful registration, you\'ll have access to your personalized dashboard.\"}', NULL, 'basic', NULL, '2023-08-12 10:14:10', '2023-08-30 13:34:23'),
(51, 'how_work.element', '{\"icon\":\"<i class=\\\"las la-clipboard-list\\\"><\\/i>\",\"title\":\"Choose Pricing Plan\",\"description\":\"Explore our range of pricing plans. Choose a plan, each offering different levels of property uploads, media capabilities, and amenities showcases.\"}', NULL, 'basic', NULL, '2023-08-12 10:14:57', '2023-08-30 13:35:50'),
(52, 'how_work.element', '{\"icon\":\"<i class=\\\"las la-folder-plus\\\"><\\/i>\",\"title\":\"Add Listings\",\"description\":\"After choosing a plan, create directory listings. Add details, images, and videos for an immersive buyer experience.\"}', NULL, 'basic', NULL, '2023-08-12 10:15:20', '2023-08-30 13:37:33'),
(57,  'recent_properties.content', '{"heading":"Recently Added Listings","subheading":"The latest businesses joining the LeadxGPT directory."}',  NULL,  'basic',  NULL,  '2023-08-12 11:28:12',  '2023-08-28 06:28:36'),
(58,  'testimonial.element', '{"has_image":"1","author":"Amira Kessler","designation":"Owner, Central Park Bistro","quote":"Our listing brought in steady enquiries within the first month. The lead inbox makes it easy to reply fast and keep guests coming back.","image":"64d7976c4c04f1691850604.jpg"}',  NULL,  'basic',  NULL,  '2023-08-12 11:50:54',  '2023-08-12 12:00:04'),
(59,  'testimonial.element', '{"has_image":"1","author":"Daniel Moreau","designation":"Owner, Skyline Business Hub","quote":"Setting up our business page took minutes. We now get found by companies searching the city, and the member badge gives visitors confidence.","image":"64d795b8538771691850168.jpg"}',  NULL,  'basic',  NULL,  '2023-08-12 11:51:19',  '2023-08-12 11:52:48'),
(60,  'testimonial.element', '{"has_image":"1","author":"Priya Nair","designation":"Manager, Harbor Seafood Grill","quote":"The city pages and reviews bring people through the door. Updating hours and photos from the dashboard keeps our listing current.","image":"64d797770ce9b1691850615.jpg"}',  NULL,  'basic',  NULL,  '2023-08-12 11:52:07',  '2023-08-12 12:00:15'),
(61, 'blog.element', '{"has_image":["1"],"title":"7 Photos Every Business Listing Should Have","description_nic":"<p>Photos are the first thing customers judge. Listings with clear, well-lit images get more clicks and more enquiries than pages that rely on a single exterior shot.</p><p>Aim for seven images: your storefront, your interior, your product or service in use, your team at work, a detail close-up, a customer moment and one seasonal or offer photo. Shoot in daylight where possible and keep the framing simple.</p><p>Update your gallery each season. Fresh photos tell both customers and search engines that the business is active.</p>","image":"64f33c269c1531693662246.png","slug":"7-photos-every-business-listing-should-have"}', NULL, 'basic', '7-photos-every-business-listing-should-have', '2023-08-12 12:10:51', '2023-09-02 12:14:07'),
(62, 'blog.element', '{"has_image":["1"],"title":"A Simple Local SEO Guide for Small Businesses","description_nic":"<p>Local search brings you the customers who are already looking for you. A complete listing is the fastest win: fill in your category, city, address and services so the directory can match you to the right searches.</p><p>Use the words your customers use in your description, and keep your business name, address and phone number identical everywhere they appear online.</p><p>Ask happy customers for reviews. Ratings appear next to your listing and are one of the strongest trust signals a local business can earn.</p>","image":"64f33b975ceec1693662103.png","slug":"a-simple-local-seo-guide-for-small-businesses"}', NULL, 'basic', 'a-simple-local-seo-guide-for-small-businesses', '2023-08-12 12:11:28', '2023-09-02 12:11:43');
INSERT INTO `frontends` (`id`, `data_keys`, `data_values`, `seo_content`, `tempname`, `slug`, `created_at`, `updated_at`) VALUES
(63, 'blog.element', '{"has_image":["1"],"title":"Turning Customer Enquiries Into Bookings","description_nic":"<p>An enquiry is not a booking until you reply. Speed matters: businesses that respond within a few hours win most of the jobs they quote for.</p><p>Reply with a specific answer, a price range where possible and one clear next step, such as a visit, a call or a booking link. Keep a few saved replies for common questions so you can personalise them quickly.</p><p>Mark each lead as contacted or closed in your lead inbox. Knowing where every enquiry stands makes follow-ups effortless.</p>","image":"64f33af46a1061693661940.png","slug":"turning-customer-enquiries-into-bookings"}', NULL, 'basic', 'turning-customer-enquiries-into-bookings', '2023-08-12 12:11:56', '2023-09-02 12:09:00'),
(64, 'blog.element', '{"has_image":["1"],"title":"Why Your Business Hours and Map Matter","description_nic":"<p>Out-of-date opening hours are the fastest way to lose a customer. They visit, find the door locked and buy from your competitor instead.</p><p>Check your listing whenever your hours change, around holidays and at the start of each season. The days-off field on your listing drives the open and closed badge customers see.</p><p>Add your map location if your plan includes it. Customers tap the map on their phone and navigate straight to you.</p>","image":"64f339ed78b051693661677.png","slug":"why-your-business-hours-and-map-matter"}', NULL, 'basic', 'why-your-business-hours-and-map-matter', '2023-08-12 12:12:20', '2023-09-02 12:04:37'),
(65, 'blog.element', '{"has_image":["1"],"title":"Hosting a Local Event on LeadxGPT","description_nic":"<p>Events are a simple way to bring people through the door. A launch party, a workshop or a seasonal market all give customers a reason to visit on a specific day.</p><p>Create your event from the member dashboard with a title, category, city and start and end times. Once approved it appears on the public events page, and on the homepage while it is still upcoming.</p><p>Invite people with the registration button and keep an eye on your attendee list. A short follow-up message after the event turns first-time visitors into regulars.</p>","image":"64f3389c6c2ef1693661340.png","slug":"hosting-a-local-event-on-leadxgpt"}', NULL, 'basic', 'hosting-a-local-event-on-leadxgpt', '2023-08-12 12:19:33', '2023-09-02 11:59:00'),
(66, 'breadcrumb.content', '{\"has_image\":\"1\",\"image\":\"64f33ff02567f1693663216.png\"}', NULL, 'basic', NULL, '2023-08-13 02:44:11', '2023-09-02 12:30:16'),
(67, 'footer.content', '{\"content\":\"LeadxGPT is the lead-generating business directory for growing businesses.\"}', NULL, 'basic', NULL, '2023-08-13 03:49:52', '2023-08-13 03:49:52'),
(69,  'contact_us.element', '{"title": "Office Address", "icon": "<i class=\\"las la-map-marked\\"></i>", "content": "44 Harbor Lane, Suite 300, New York, NY 10014"}',  NULL,  'basic',  NULL,  '2023-08-13 04:14:07',  '2023-08-13 04:14:34'),
(70,  'contact_us.element', '{"title": "Quick Call to Us", "icon": "<i class=\\"las la-phone-volume\\"></i>", "content": "+1 (555) 013-4567"}',  NULL,  'basic',  NULL,  '2023-08-13 04:15:12',  '2023-08-13 04:16:25'),
(71,  'contact_us.element', '{"title": "Email Address", "icon": "<i class=\\"lar la-envelope-open\\"></i>", "content": "support@leadxgpt.com"}',  NULL,  'basic',  NULL,  '2023-08-13 04:15:52',  '2023-08-13 04:16:06'),
(72,  'about_us.content', '{"has_image":"1","heading":"About LeadxGPT","content":"LeadxGPT is a business directory built to connect local businesses with the people looking for them. Members publish a verified business page, get discovered through search and city pages, and receive customer enquiries straight into their lead inbox.","image":"64d8835770b4b1691910999.png"}',  NULL,  'basic',  NULL,  '2023-08-13 04:37:50',  '2023-08-13 04:46:39'),
(73, 'about_us.element', '{\"icon\":\"<i class=\\\"las la-map-marker-alt\\\"><\\/i>\",\"title\":\"Find what you want\",\"description\":\"Compare verified profiles, ratings and reviews to choose the right business\"}', NULL, 'basic', NULL, '2023-08-13 04:38:37', '2023-08-13 04:38:37'),
(74, 'about_us.element', '{\"icon\":\"<i class=\\\"las la-map-marked-alt\\\"><\\/i>\",\"title\":\"Easy to choose your best place\",\"description\":\"Compare verified profiles, ratings and reviews to choose the right business\"}', NULL, 'basic', NULL, '2023-08-13 04:40:12', '2023-08-13 04:40:12'),
(75, 'about.element', '{\"icon\":\"<i class=\\\"las la-map-marked\\\"><\\/i>\",\"title\":\"Find What You Want\",\"description\":\"Compare verified profiles, ratings and reviews to choose the right business\"}', NULL, 'basic', NULL, '2023-08-13 06:22:57', '2023-08-13 06:22:57'),
(76, 'about.element', '{\"icon\":\"<i class=\\\"las la-map-marker\\\"><\\/i>\",\"title\":\"Easy Choose Your Place\",\"description\":\"Compare verified profiles, ratings and reviews to choose the right business\"}', NULL, 'basic', NULL, '2023-08-13 06:22:58', '2023-08-13 06:23:22'),
(77,  'choose_us.content', '{"heading":"Why businesses choose LeadxGPT","subheading":"Everything you need to get found, get leads and grow."}',  NULL,  'basic',  NULL,  '2023-08-13 06:26:33',  '2023-08-13 06:26:33'),
(78, 'choose_us.element', '{\"icon\":\"<i class=\\\"las la-user-tie\\\"><\\/i>\",\"title\":\"User Friendly\",\"description\":\"It has survived not only five centuries, but also the leap into electronic publishing software like aldus pagemaker\"}', NULL, 'basic', NULL, '2023-08-13 06:26:51', '2023-08-13 06:33:24'),
(79, 'choose_us.element', '{\"icon\":\"<i class=\\\"las la-filter\\\"><\\/i>\",\"title\":\"Quick Search\",\"description\":\"It has survived not only five centuries, but also the leap into electronic publishing software like aldus pagemaker\"}', NULL, 'basic', NULL, '2023-08-13 06:27:26', '2023-08-13 06:27:26'),
(80, 'choose_us.element', '{\"icon\":\"<i class=\\\"las la-hands-helping\\\"><\\/i>\",\"title\":\"Live Support\",\"description\":\"It has survived not only five centuries, but also the leap into electronic publishing software like aldus pagemaker\"}', NULL, 'basic', NULL, '2023-08-13 06:27:52', '2023-08-13 06:27:52'),
(86,  'faq.content', '{"heading":"FAQs","subheading":"Answers to the questions business owners ask most."}',  NULL,  'basic',  NULL,  '2023-08-13 07:54:48',  '2023-08-30 05:32:52'),
(87,  'faq.element', '{"question":"What is LeadxGPT?","answer":"LeadxGPT is an online business directory. Businesses publish a listing with photos, services and opening hours, and customers search, compare and contact them directly."}',  NULL,  'basic',  NULL,  '2023-08-13 07:55:40',  '2023-08-30 05:33:27'),
(88,  'faq.element', '{"question":"How do I add my business to the directory?","answer":"Create a free account, then post your listing from the member dashboard. Add your description, photos, opening hours and amenities, and once approved your page appears in search, category and city pages."}',  NULL,  'basic',  NULL,  '2023-08-13 07:55:55',  '2023-08-30 05:33:48'),
(89,  'faq.element', '{"question":"Is there a free plan?","answer":"Yes. The Free plan includes one listing with one image. Paid plans add more listings, more photos and videos, amenities, a Google map and event hosting."}',  NULL,  'basic',  NULL,  '2023-08-13 07:56:20',  '2023-08-30 05:34:06'),
(91, 'login.content', '{\"has_image\":\"1\",\"login_heading\":\"Access Your Account\",\"login_subheading\":\"Securely and Conveniently\",\"image\":\"64f345192fd761693664537.jpg\"}', NULL, 'basic', NULL, '2023-08-13 09:50:59', '2023-09-02 12:52:18'),
(92, 'social_icon.element', '{\"title\":\"Twitter\",\"social_icon\":\"<i class=\\\"fab fa-twitter\\\"><\\/i>\",\"url\":\"https:\\/\\/www.twitter.com\\/\"}', NULL, 'basic', NULL, '2023-08-13 13:08:49', '2023-08-13 13:08:49'),
(93, 'social_icon.element', '{\"title\":\"Linkedin\",\"social_icon\":\"<i class=\\\"fab fa-linkedin-in\\\"><\\/i>\",\"url\":\"https:\\/\\/www.linkedin.com\\/\"}', NULL, 'basic', NULL, '2023-08-13 13:09:25', '2023-08-13 13:09:25'),
(94,  'pricing.content', '{"heading":"Choose Your Best Plans","subheading":"Simple yearly plans. Start free and upgrade as your business grows.","button":"Buy Now"}',  NULL,  'basic',  NULL,  '2023-08-14 04:37:30',  '2023-08-14 06:10:37'),
(95,  'feature_listing.content', '{"heading":"Featured Listings","subheading":"Hand-picked businesses our members trust the most."}',  NULL,  'basic',  NULL,  '2023-08-28 06:57:48',  '2023-08-28 06:57:48'),
(96,  'recent_listings.content', '{"heading":"Recently Added Listings","subheading":"The latest businesses joining the LeadxGPT directory."}',  NULL,  'basic',  NULL,  '2023-08-28 07:03:36',  '2023-08-28 07:03:36'),
(97,  'faq.element', '{"question":"How do customers find my listing?","answer":"Customers search by keyword, category or city, and every listing has its own page that search engines can index. City and category pages link to your listing too."}',  NULL,  'basic',  NULL,  '2023-08-30 05:34:24',  '2023-08-30 05:34:24'),
(98,  'faq.element', '{"question":"Can I edit my listing after publishing?","answer":"Yes. Log in to your dashboard, open My Listings and edit the details any time."}',  NULL,  'basic',  NULL,  '2023-08-30 05:34:38',  '2023-08-30 05:34:38'),
(99, 'registration.content', '{\"has_image\":\"1\",\"register_heading\":\"Create Your Account\",\"register_subheading\":\"Join Our Growing Network\",\"image\":\"64f34749655cd1693665097.jpg\"}', NULL, 'basic', NULL, '2023-09-02 13:01:37', '2023-09-02 13:01:39'),
(100,  'register_disable.content', '{"has_image":"1","heading":"Registration Currently Disabled","subheading":"Registration is temporarily unavailable. Please check back soon.","button_name":"Go to Home","button_url":"/","image":"66488b6067df71716030304.png"}',  NULL,  'basic',  '',  '2024-05-18 05:05:04',  '2024-05-18 05:05:04');

-- --------------------------------------------------------

--
-- Table structure for table `gateways`
--

CREATE TABLE `gateways` (
  `id` bigint UNSIGNED NOT NULL,
  `form_id` int UNSIGNED NOT NULL DEFAULT '0',
  `code` int DEFAULT NULL,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alias` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NULL',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=>enable, 2=>disable',
  `gateway_parameters` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `supported_currencies` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `crypto` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: fiat currency, 1: crypto currency',
  `extra` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gateways`
--

INSERT INTO `gateways` (`id`, `form_id`, `code`, `name`, `alias`, `image`, `status`, `gateway_parameters`, `supported_currencies`, `crypto`, `extra`, `description`, `created_at`, `updated_at`) VALUES
(1, 0, 101, 'Paypal', 'Paypal', '663a38d7b455d1715091671.png', 1, '{\"paypal_email\":{\"title\":\"PayPal Email\",\"global\":true,\"value\":\"sb-owud61543012@business.example.com\"}}', '{\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"TWD\":\"TWD\",\"NZD\":\"NZD\",\"NOK\":\"NOK\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"GBP\":\"GBP\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"USD\":\"$\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 00:04:38'),
(2, 0, 102, 'Perfect Money', 'PerfectMoney', '663a3920e30a31715091744.png', 1, '{\"passphrase\":{\"title\":\"ALTERNATE PASSPHRASE\",\"global\":true,\"value\":\"hR26aw02Q1eEeUPSIfuwNypXX\"},\"wallet_id\":{\"title\":\"PM Wallet\",\"global\":false,\"value\":\"\"}}', '{\"USD\":\"$\",\"EUR\":\"\\u20ac\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 01:35:33'),
(3, 0, 103, 'Stripe Hosted', 'Stripe', '663a39861cb9d1715091846.png', 1, '{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"YOUR_STRIPE_TEST_SECRET_KEY\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 00:48:36'),
(4, 0, 104, 'Skrill', 'Skrill', '663a39494c4a91715091785.png', 1, '{\"pay_to_email\":{\"title\":\"Skrill Email\",\"global\":true,\"value\":\"merchant@skrill.com\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"---\"}}', '{\"AED\":\"AED\",\"AUD\":\"AUD\",\"BGN\":\"BGN\",\"BHD\":\"BHD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"HRK\":\"HRK\",\"HUF\":\"HUF\",\"ILS\":\"ILS\",\"INR\":\"INR\",\"ISK\":\"ISK\",\"JOD\":\"JOD\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"KWD\":\"KWD\",\"MAD\":\"MAD\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"OMR\":\"OMR\",\"PLN\":\"PLN\",\"QAR\":\"QAR\",\"RON\":\"RON\",\"RSD\":\"RSD\",\"SAR\":\"SAR\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TND\":\"TND\",\"TRY\":\"TRY\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\",\"COP\":\"COP\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 01:30:16'),
(5, 0, 105, 'PayTM', 'Paytm', '663a390f601191715091727.png', 1, '{\"MID\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"DIY12386817555501617\"},\"merchant_key\":{\"title\":\"Merchant Key\",\"global\":true,\"value\":\"bKMfNxPPf_QdZppa\"},\"WEBSITE\":{\"title\":\"Paytm Website\",\"global\":true,\"value\":\"DIYtestingweb\"},\"INDUSTRY_TYPE_ID\":{\"title\":\"Industry Type\",\"global\":true,\"value\":\"Retail\"},\"CHANNEL_ID\":{\"title\":\"CHANNEL ID\",\"global\":true,\"value\":\"WEB\"},\"transaction_url\":{\"title\":\"Transaction URL\",\"global\":true,\"value\":\"https:\\/\\/pguat.paytm.com\\/oltp-web\\/processTransaction\"},\"transaction_status_url\":{\"title\":\"Transaction STATUS URL\",\"global\":true,\"value\":\"https:\\/\\/pguat.paytm.com\\/paytmchecksum\\/paytmCallback.jsp\"}}', '{\"AUD\":\"AUD\",\"ARS\":\"ARS\",\"BDT\":\"BDT\",\"BRL\":\"BRL\",\"BGN\":\"BGN\",\"CAD\":\"CAD\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"COP\":\"COP\",\"HRK\":\"HRK\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EGP\":\"EGP\",\"EUR\":\"EUR\",\"GEL\":\"GEL\",\"GHS\":\"GHS\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"KES\":\"KES\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"MAD\":\"MAD\",\"NPR\":\"NPR\",\"NZD\":\"NZD\",\"NGN\":\"NGN\",\"NOK\":\"NOK\",\"PKR\":\"PKR\",\"PEN\":\"PEN\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"RON\":\"RON\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"ZAR\":\"ZAR\",\"KRW\":\"KRW\",\"LKR\":\"LKR\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"TRY\":\"TRY\",\"UGX\":\"UGX\",\"UAH\":\"UAH\",\"AED\":\"AED\",\"GBP\":\"GBP\",\"USD\":\"USD\",\"VND\":\"VND\",\"XOF\":\"XOF\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 03:00:44'),
(6, 0, 106, 'Payeer', 'Payeer', '663a38c9e2e931715091657.png', 1, '{\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"866989763\"},\"secret_key\":{\"title\":\"Secret key\",\"global\":true,\"value\":\"7575\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\",\"RUB\":\"RUB\"}', 0, '{\"status\":{\"title\": \"Status URL\",\"value\":\"ipn.Payeer\"}}', NULL, '2019-09-14 13:14:22', '2022-08-28 10:11:14'),
(7, 0, 107, 'PayStack', 'Paystack', '663a38fc814e91715091708.png', 1, '{\"public_key\":{\"title\":\"Public key\",\"global\":true,\"value\":\"pk_test_cd330608eb47970889bca397ced55c1dd5ad3783\"},\"secret_key\":{\"title\":\"Secret key\",\"global\":true,\"value\":\"YOUR_STRIPE_TEST_SECRET_KEY\"}}', '{\"USD\":\"USD\",\"NGN\":\"NGN\",\"GHS\":\"GHS\",\"KES\":\"KES\",\"ZAR\":\"ZAR\",\"XOF\":\"XOF\"}', 0, '{\"callback\":{\"title\": \"Callback URL\",\"value\":\"ipn.Paystack\"},\"webhook\":{\"title\": \"Webhook URL\",\"value\":\"ipn.Paystack\"}}\r\n', NULL, '2019-09-14 13:14:22', '2021-05-21 01:49:51'),
(9, 0, 109, 'Flutterwave', 'Flutterwave', '663a36c2c34d61715091138.png', 1, '{\"public_key\":{\"title\":\"Public Key\",\"global\":true,\"value\":\"----------------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"-----------------------\"},\"encryption_key\":{\"title\":\"Encryption Key\",\"global\":true,\"value\":\"------------------\"}}', '{\"BIF\":\"BIF\",\"CAD\":\"CAD\",\"CDF\":\"CDF\",\"CVE\":\"CVE\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"GHS\":\"GHS\",\"GMD\":\"GMD\",\"GNF\":\"GNF\",\"KES\":\"KES\",\"LRD\":\"LRD\",\"MWK\":\"MWK\",\"MZN\":\"MZN\",\"NGN\":\"NGN\",\"RWF\":\"RWF\",\"SLL\":\"SLL\",\"STD\":\"STD\",\"TZS\":\"TZS\",\"UGX\":\"UGX\",\"USD\":\"USD\",\"XAF\":\"XAF\",\"XOF\":\"XOF\",\"ZMK\":\"ZMK\",\"ZMW\":\"ZMW\",\"ZWD\":\"ZWD\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-06-05 11:37:45'),
(10, 0, 110, 'RazorPay', 'Razorpay', '663a393a527831715091770.png', 1, '{\"key_id\":{\"title\":\"Key Id\",\"global\":true,\"value\":\"rzp_test_kiOtejPbRZU90E\"},\"key_secret\":{\"title\":\"Key Secret \",\"global\":true,\"value\":\"osRDebzEqbsE1kbyQJ4y0re7\"}}', '{\"INR\":\"INR\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 02:51:32'),
(11, 0, 111, 'Stripe Storefront', 'StripeJs', '663a3995417171715091861.png', 1, '{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"YOUR_STRIPE_TEST_SECRET_KEY\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 00:53:10'),
(12, 0, 112, 'Instamojo', 'Instamojo', '663a384d54a111715091533.png', 1, '{\"api_key\":{\"title\":\"API KEY\",\"global\":true,\"value\":\"test_2241633c3bc44a3de84a3b33969\"},\"auth_token\":{\"title\":\"Auth Token\",\"global\":true,\"value\":\"test_279f083f7bebefd35217feef22d\"},\"salt\":{\"title\":\"Salt\",\"global\":true,\"value\":\"19d38908eeff4f58b2ddda2c6d86ca25\"}}', '{\"INR\":\"INR\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 02:56:20'),
(13, 0, 501, 'Blockchain', 'Blockchain', '663a35efd0c311715090927.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"55529946-05ca-48ff-8710-f279d86b1cc5\"},\"xpub_code\":{\"title\":\"XPUB CODE\",\"global\":true,\"value\":\"xpub6CKQ3xxWyBoFAF83izZCSFUorptEU9AF8TezhtWeMU5oefjX3sFSBw62Lr9iHXPkXmDQJJiHZeTRtD9Vzt8grAYRhvbz4nEvBu3QKELVzFK\"}}', '{\"BTC\":\"BTC\"}', 1, NULL, NULL, '2019-09-14 13:14:22', '2022-03-21 07:41:56'),
(15, 0, 503, 'CoinPayments', 'Coinpayments', '663a36a8d8e1d1715091112.png', 1, '{\"public_key\":{\"title\":\"Public Key\",\"global\":true,\"value\":\"---------------------\"},\"private_key\":{\"title\":\"Private Key\",\"global\":true,\"value\":\"---------------------\"},\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"---------------------\"}}', '{\"BTC\":\"Bitcoin\",\"BTC.LN\":\"Bitcoin (Lightning Network)\",\"LTC\":\"Litecoin\",\"CPS\":\"CPS Coin\",\"VLX\":\"Velas\",\"APL\":\"Apollo\",\"AYA\":\"Aryacoin\",\"BAD\":\"Badcoin\",\"BCD\":\"Bitcoin Diamond\",\"BCH\":\"Bitcoin Cash\",\"BCN\":\"Bytecoin\",\"BEAM\":\"BEAM\",\"BITB\":\"Bean Cash\",\"BLK\":\"BlackCoin\",\"BSV\":\"Bitcoin SV\",\"BTAD\":\"Bitcoin Adult\",\"BTG\":\"Bitcoin Gold\",\"BTT\":\"BitTorrent\",\"CLOAK\":\"CloakCoin\",\"CLUB\":\"ClubCoin\",\"CRW\":\"Crown\",\"CRYP\":\"CrypticCoin\",\"CRYT\":\"CryTrExCoin\",\"CURE\":\"CureCoin\",\"DASH\":\"DASH\",\"DCR\":\"Decred\",\"DEV\":\"DeviantCoin\",\"DGB\":\"DigiByte\",\"DOGE\":\"Dogecoin\",\"EBST\":\"eBoost\",\"EOS\":\"EOS\",\"ETC\":\"Ether Classic\",\"ETH\":\"Ethereum\",\"ETN\":\"Electroneum\",\"EUNO\":\"EUNO\",\"EXP\":\"EXP\",\"Expanse\":\"Expanse\",\"FLASH\":\"FLASH\",\"GAME\":\"GameCredits\",\"GLC\":\"Goldcoin\",\"GRS\":\"Groestlcoin\",\"KMD\":\"Komodo\",\"LOKI\":\"LOKI\",\"LSK\":\"LSK\",\"MAID\":\"MaidSafeCoin\",\"MUE\":\"MonetaryUnit\",\"NAV\":\"NAV Coin\",\"NEO\":\"NEO\",\"NMC\":\"Namecoin\",\"NVST\":\"NVO Token\",\"NXT\":\"NXT\",\"OMNI\":\"OMNI\",\"PINK\":\"PinkCoin\",\"PIVX\":\"PIVX\",\"POT\":\"PotCoin\",\"PPC\":\"Peercoin\",\"PROC\":\"ProCurrency\",\"PURA\":\"PURA\",\"QTUM\":\"QTUM\",\"RES\":\"Resistance\",\"RVN\":\"Ravencoin\",\"RVR\":\"RevolutionVR\",\"SBD\":\"Steem Dollars\",\"SMART\":\"SmartCash\",\"SOXAX\":\"SOXAX\",\"STEEM\":\"STEEM\",\"STRAT\":\"STRAT\",\"SYS\":\"Syscoin\",\"TPAY\":\"TokenPay\",\"TRIGGERS\":\"Triggers\",\"TRX\":\" TRON\",\"UBQ\":\"Ubiq\",\"UNIT\":\"UniversalCurrency\",\"USDT.BEP20\":\"Tether USD (BSC Chain)\",\"USDT.ERC20\":\"Tether USD (ERC20)\",\"USDT.TRC20\":\"Tether USD (Tron/TRC20)\",\"VTC\":\"Vertcoin\",\"WAVES\":\"Waves\",\"XCP\":\"Counterparty\",\"XEM\":\"NEM\",\"XMR\":\"Monero\",\"XSN\":\"Stakenet\",\"XSR\":\"SucreCoin\",\"XVG\":\"VERGE\",\"XZC\":\"ZCoin\",\"ZEC\":\"ZCash\",\"ZEN\":\"Horizen\"}', 1, NULL, NULL, '2019-09-14 13:14:22', '2023-04-08 03:17:18'),
(16, 0, 504, 'CoinPayments Fiat', 'CoinpaymentsFiat', '663a36b7b841a1715091127.png', 1, '{\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"6515561\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"ISK\":\"ISK\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"RUB\":\"RUB\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TWD\":\"TWD\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 02:07:44'),
(17, 0, 505, 'Coingate', 'Coingate', '663a368e753381715091086.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"6354mwVCEw5kHzRJ6thbGo-N\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2022-03-30 09:24:57'),
(18, 0, 506, 'Coinbase Commerce', 'CoinbaseCommerce', '663a367e46ae51715091070.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"c47cd7df-d8e8-424b-a20a\"},\"secret\":{\"title\":\"Webhook Shared Secret\",\"global\":true,\"value\":\"55871878-2c32-4f64-ab66\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\",\"JPY\":\"JPY\",\"GBP\":\"GBP\",\"AUD\":\"AUD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CNY\":\"CNY\",\"SEK\":\"SEK\",\"NZD\":\"NZD\",\"MXN\":\"MXN\",\"SGD\":\"SGD\",\"HKD\":\"HKD\",\"NOK\":\"NOK\",\"KRW\":\"KRW\",\"TRY\":\"TRY\",\"RUB\":\"RUB\",\"INR\":\"INR\",\"BRL\":\"BRL\",\"ZAR\":\"ZAR\",\"AED\":\"AED\",\"AFN\":\"AFN\",\"ALL\":\"ALL\",\"AMD\":\"AMD\",\"ANG\":\"ANG\",\"AOA\":\"AOA\",\"ARS\":\"ARS\",\"AWG\":\"AWG\",\"AZN\":\"AZN\",\"BAM\":\"BAM\",\"BBD\":\"BBD\",\"BDT\":\"BDT\",\"BGN\":\"BGN\",\"BHD\":\"BHD\",\"BIF\":\"BIF\",\"BMD\":\"BMD\",\"BND\":\"BND\",\"BOB\":\"BOB\",\"BSD\":\"BSD\",\"BTN\":\"BTN\",\"BWP\":\"BWP\",\"BYN\":\"BYN\",\"BZD\":\"BZD\",\"CDF\":\"CDF\",\"CLF\":\"CLF\",\"CLP\":\"CLP\",\"COP\":\"COP\",\"CRC\":\"CRC\",\"CUC\":\"CUC\",\"CUP\":\"CUP\",\"CVE\":\"CVE\",\"CZK\":\"CZK\",\"DJF\":\"DJF\",\"DKK\":\"DKK\",\"DOP\":\"DOP\",\"DZD\":\"DZD\",\"EGP\":\"EGP\",\"ERN\":\"ERN\",\"ETB\":\"ETB\",\"FJD\":\"FJD\",\"FKP\":\"FKP\",\"GEL\":\"GEL\",\"GGP\":\"GGP\",\"GHS\":\"GHS\",\"GIP\":\"GIP\",\"GMD\":\"GMD\",\"GNF\":\"GNF\",\"GTQ\":\"GTQ\",\"GYD\":\"GYD\",\"HNL\":\"HNL\",\"HRK\":\"HRK\",\"HTG\":\"HTG\",\"HUF\":\"HUF\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"IMP\":\"IMP\",\"IQD\":\"IQD\",\"IRR\":\"IRR\",\"ISK\":\"ISK\",\"JEP\":\"JEP\",\"JMD\":\"JMD\",\"JOD\":\"JOD\",\"KES\":\"KES\",\"KGS\":\"KGS\",\"KHR\":\"KHR\",\"KMF\":\"KMF\",\"KPW\":\"KPW\",\"KWD\":\"KWD\",\"KYD\":\"KYD\",\"KZT\":\"KZT\",\"LAK\":\"LAK\",\"LBP\":\"LBP\",\"LKR\":\"LKR\",\"LRD\":\"LRD\",\"LSL\":\"LSL\",\"LYD\":\"LYD\",\"MAD\":\"MAD\",\"MDL\":\"MDL\",\"MGA\":\"MGA\",\"MKD\":\"MKD\",\"MMK\":\"MMK\",\"MNT\":\"MNT\",\"MOP\":\"MOP\",\"MRO\":\"MRO\",\"MUR\":\"MUR\",\"MVR\":\"MVR\",\"MWK\":\"MWK\",\"MYR\":\"MYR\",\"MZN\":\"MZN\",\"NAD\":\"NAD\",\"NGN\":\"NGN\",\"NIO\":\"NIO\",\"NPR\":\"NPR\",\"OMR\":\"OMR\",\"PAB\":\"PAB\",\"PEN\":\"PEN\",\"PGK\":\"PGK\",\"PHP\":\"PHP\",\"PKR\":\"PKR\",\"PLN\":\"PLN\",\"PYG\":\"PYG\",\"QAR\":\"QAR\",\"RON\":\"RON\",\"RSD\":\"RSD\",\"RWF\":\"RWF\",\"SAR\":\"SAR\",\"SBD\":\"SBD\",\"SCR\":\"SCR\",\"SDG\":\"SDG\",\"SHP\":\"SHP\",\"SLL\":\"SLL\",\"SOS\":\"SOS\",\"SRD\":\"SRD\",\"SSP\":\"SSP\",\"STD\":\"STD\",\"SVC\":\"SVC\",\"SYP\":\"SYP\",\"SZL\":\"SZL\",\"THB\":\"THB\",\"TJS\":\"TJS\",\"TMT\":\"TMT\",\"TND\":\"TND\",\"TOP\":\"TOP\",\"TTD\":\"TTD\",\"TWD\":\"TWD\",\"TZS\":\"TZS\",\"UAH\":\"UAH\",\"UGX\":\"UGX\",\"UYU\":\"UYU\",\"UZS\":\"UZS\",\"VEF\":\"VEF\",\"VND\":\"VND\",\"VUV\":\"VUV\",\"WST\":\"WST\",\"XAF\":\"XAF\",\"XAG\":\"XAG\",\"XAU\":\"XAU\",\"XCD\":\"XCD\",\"XDR\":\"XDR\",\"XOF\":\"XOF\",\"XPD\":\"XPD\",\"XPF\":\"XPF\",\"XPT\":\"XPT\",\"YER\":\"YER\",\"ZMW\":\"ZMW\",\"ZWL\":\"ZWL\"}\r\n\r\n', 0, '{\"endpoint\":{\"title\": \"Webhook Endpoint\",\"value\":\"ipn.CoinbaseCommerce\"}}', NULL, '2019-09-14 13:14:22', '2021-05-21 02:02:47'),
(24, 0, 113, 'Paypal Express', 'PaypalSdk', '663a38ed101a61715091693.png', 1, '{\"clientId\":{\"title\":\"Paypal Client ID\",\"global\":true,\"value\":\"Ae0-tixtSV7DvLwIh3Bmu7JvHrjh5EfGdXr_cEklKAVjjezRZ747BxKILiBdzlKKyp-W8W_T7CKH1Ken\"},\"clientSecret\":{\"title\":\"Client Secret\",\"global\":true,\"value\":\"EOhbvHZgFNO21soQJT1L9Q00M3rK6PIEsdiTgXRBt2gtGtxwRer5JvKnVUGNU5oE63fFnjnYY7hq3HBA\"}}', '{\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"TWD\":\"TWD\",\"NZD\":\"NZD\",\"NOK\":\"NOK\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"GBP\":\"GBP\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"USD\":\"$\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-20 23:01:08'),
(25, 0, 114, 'Stripe Checkout', 'StripeV3', '663a39afb519f1715091887.png', 1, '{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"YOUR_STRIPE_TEST_SECRET_KEY\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"},\"end_point\":{\"title\":\"End Point Secret\",\"global\":true,\"value\":\"whsec_lUmit1gtxwKTveLnSe88xCSDdnPOt8g5\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}', 0, '{\"webhook\":{\"title\": \"Webhook Endpoint\",\"value\":\"ipn.StripeV3\"}}', NULL, '2019-09-14 13:14:22', '2021-05-21 00:58:38'),
(27, 0, 115, 'Mollie', 'Mollie', '663a387ec69371715091582.png', 1, '{\"mollie_email\":{\"title\":\"Mollie Email \",\"global\":true,\"value\":\"vi@gmail.com\"},\"api_key\":{\"title\":\"API KEY\",\"global\":true,\"value\":\"test_cucfwKTWfft9s337qsVfn5CC4vNkrn\"}}', '{\"AED\":\"AED\",\"AUD\":\"AUD\",\"BGN\":\"BGN\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"HRK\":\"HRK\",\"HUF\":\"HUF\",\"ILS\":\"ILS\",\"ISK\":\"ISK\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"RON\":\"RON\",\"RUB\":\"RUB\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\"}', 0, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 02:44:45'),
(30, 0, 116, 'Cashmaal', 'Cashmaal', '663a361b16bd11715090971.png', 1, '{\"web_id\":{\"title\":\"Web Id\",\"global\":true,\"value\":\"3748\"},\"ipn_key\":{\"title\":\"IPN Key\",\"global\":true,\"value\":\"546254628759524554647987\"}}', '{\"PKR\":\"PKR\",\"USD\":\"USD\"}', 0, '{\"webhook\":{\"title\": \"IPN URL\",\"value\":\"ipn.Cashmaal\"}}', NULL, NULL, '2021-06-22 08:05:04'),
(36, 0, 119, 'Mercado Pago', 'MercadoPago', '663a386c714a91715091564.png', 1, '{\"access_token\":{\"title\":\"Access Token\",\"global\":true,\"value\":\"APP_USR-7924565816849832-082312-21941521997fab717db925cf1ea2c190-1071840315\"}}', '{\"USD\":\"USD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"NOK\":\"NOK\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"AUD\":\"AUD\",\"NZD\":\"NZD\",\"ARS\":\"ARS\",\"BRL\":\"BRL\",\"CLP\":\"CLP\",\"COP\":\"COP\",\"MXN\":\"MXN\",\"PEN\":\"PEN\",\"UYU\":\"UYU\",\"VEF\":\"VEF\",\"BOB\":\"BOB\"}', 0, NULL, NULL, NULL, '2022-09-14 07:41:14'),
(37, 0, 120, 'Authorize.net', 'Authorize', '663a35b9ca5991715090873.png', 1, '{\"login_id\":{\"title\":\"Login ID\",\"global\":true,\"value\":\"59e4P9DBcZv\"},\"transaction_key\":{\"title\":\"Transaction Key\",\"global\":true,\"value\":\"47x47TJyLw2E7DbR\"}}', '{\"USD\":\"USD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"NOK\":\"NOK\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"AUD\":\"AUD\",\"NZD\":\"NZD\"}', 0, NULL, NULL, NULL, '2022-08-28 09:33:06'),
(46, 0, 121, 'NMI', 'NMI', '663a3897754cf1715091607.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"2F822Rw39fx762MaV7Yy86jXGTC7sCDy\"}}', '{\"AED\":\"AED\",\"ARS\":\"ARS\",\"AUD\":\"AUD\",\"BOB\":\"BOB\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"COP\":\"COP\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PEN\":\"PEN\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"PYG\":\"PYG\",\"RUB\":\"RUB\",\"SEC\":\"SEC\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TRY\":\"TRY\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\"}', 0, NULL, NULL, NULL, '2022-08-28 10:32:31'),
(50, 0, 507, 'BTCPay', 'BTCPay', '663a35cd25a8d1715090893.png', 1, '{\"store_id\":{\"title\":\"Store Id\",\"global\":true,\"value\":\"HsqFVTXSeUFJu7caoYZc3CTnP8g5LErVdHhEXPVTheHf\"},\"api_key\":{\"title\":\"Api Key\",\"global\":true,\"value\":\"4436bd706f99efae69305e7c4eff4780de1335ce\"},\"server_name\":{\"title\":\"Server Name\",\"global\":true,\"value\":\"https:\\/\\/testnet.demo.btcpayserver.org\"},\"secret_code\":{\"title\":\"Secret Code\",\"global\":true,\"value\":\"SUCdqPn9CDkY7RmJHfpQVHP2Lf2\"}}', '{\"BTC\":\"Bitcoin\",\"LTC\":\"Litecoin\"}', 1, '{\"webhook\":{\"title\": \"IPN URL\",\"value\":\"ipn.BTCPay\"}}', NULL, NULL, '2023-02-14 04:42:09'),
(51, 0, 508, 'Now payments hosted', 'NowPaymentsHosted', '663a38b8d57a81715091640.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"--------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"------------\"}}', '{\"BTG\":\"BTG\",\"ETH\":\"ETH\",\"XMR\":\"XMR\",\"ZEC\":\"ZEC\",\"XVG\":\"XVG\",\"ADA\":\"ADA\",\"LTC\":\"LTC\",\"BCH\":\"BCH\",\"QTUM\":\"QTUM\",\"DASH\":\"DASH\",\"XLM\":\"XLM\",\"XRP\":\"XRP\",\"XEM\":\"XEM\",\"DGB\":\"DGB\",\"LSK\":\"LSK\",\"DOGE\":\"DOGE\",\"TRX\":\"TRX\",\"KMD\":\"KMD\",\"REP\":\"REP\",\"BAT\":\"BAT\",\"ARK\":\"ARK\",\"WAVES\":\"WAVES\",\"BNB\":\"BNB\",\"XZC\":\"XZC\",\"NANO\":\"NANO\",\"TUSD\":\"TUSD\",\"VET\":\"VET\",\"ZEN\":\"ZEN\",\"GRS\":\"GRS\",\"FUN\":\"FUN\",\"NEO\":\"NEO\",\"GAS\":\"GAS\",\"PAX\":\"PAX\",\"USDC\":\"USDC\",\"ONT\":\"ONT\",\"XTZ\":\"XTZ\",\"LINK\":\"LINK\",\"RVN\":\"RVN\",\"BNBMAINNET\":\"BNBMAINNET\",\"ZIL\":\"ZIL\",\"BCD\":\"BCD\",\"USDT\":\"USDT\",\"USDTERC20\":\"USDTERC20\",\"CRO\":\"CRO\",\"DAI\":\"DAI\",\"HT\":\"HT\",\"WABI\":\"WABI\",\"BUSD\":\"BUSD\",\"ALGO\":\"ALGO\",\"USDTTRC20\":\"USDTTRC20\",\"GT\":\"GT\",\"STPT\":\"STPT\",\"AVA\":\"AVA\",\"SXP\":\"SXP\",\"UNI\":\"UNI\",\"OKB\":\"OKB\",\"BTC\":\"BTC\"}', 1, '', NULL, NULL, '2023-02-14 05:08:23'),
(52, 0, 509, 'Now payments checkout', 'NowPaymentsCheckout', '663a38a59d2541715091621.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"---------------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"-----------\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\"}', 1, '', NULL, NULL, '2023-02-14 05:08:04'),
(53, 0, 122, '2Checkout', 'TwoCheckout', '663a39b8e64b91715091896.png', 1, '{\"merchant_code\":{\"title\":\"Merchant Code\",\"global\":true,\"value\":\"253248016872\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"eQM)ID@&vG84u!O*g[p+\"}}', '{\"AFN\": \"AFN\",\"ALL\": \"ALL\",\"DZD\": \"DZD\",\"ARS\": \"ARS\",\"AUD\": \"AUD\",\"AZN\": \"AZN\",\"BSD\": \"BSD\",\"BDT\": \"BDT\",\"BBD\": \"BBD\",\"BZD\": \"BZD\",\"BMD\": \"BMD\",\"BOB\": \"BOB\",\"BWP\": \"BWP\",\"BRL\": \"BRL\",\"GBP\": \"GBP\",\"BND\": \"BND\",\"BGN\": \"BGN\",\"CAD\": \"CAD\",\"CLP\": \"CLP\",\"CNY\": \"CNY\",\"COP\": \"COP\",\"CRC\": \"CRC\",\"HRK\": \"HRK\",\"CZK\": \"CZK\",\"DKK\": \"DKK\",\"DOP\": \"DOP\",\"XCD\": \"XCD\",\"EGP\": \"EGP\",\"EUR\": \"EUR\",\"FJD\": \"FJD\",\"GTQ\": \"GTQ\",\"HKD\": \"HKD\",\"HNL\": \"HNL\",\"HUF\": \"HUF\",\"INR\": \"INR\",\"IDR\": \"IDR\",\"ILS\": \"ILS\",\"JMD\": \"JMD\",\"JPY\": \"JPY\",\"KZT\": \"KZT\",\"KES\": \"KES\",\"LAK\": \"LAK\",\"MMK\": \"MMK\",\"LBP\": \"LBP\",\"LRD\": \"LRD\",\"MOP\": \"MOP\",\"MYR\": \"MYR\",\"MVR\": \"MVR\",\"MRO\": \"MRO\",\"MUR\": \"MUR\",\"MXN\": \"MXN\",\"MAD\": \"MAD\",\"NPR\": \"NPR\",\"TWD\": \"TWD\",\"NZD\": \"NZD\",\"NIO\": \"NIO\",\"NOK\": \"NOK\",\"PKR\": \"PKR\",\"PGK\": \"PGK\",\"PEN\": \"PEN\",\"PHP\": \"PHP\",\"PLN\": \"PLN\",\"QAR\": \"QAR\",\"RON\": \"RON\",\"RUB\": \"RUB\",\"WST\": \"WST\",\"SAR\": \"SAR\",\"SCR\": \"SCR\",\"SGD\": \"SGD\",\"SBD\": \"SBD\",\"ZAR\": \"ZAR\",\"KRW\": \"KRW\",\"LKR\": \"LKR\",\"SEK\": \"SEK\",\"CHF\": \"CHF\",\"SYP\": \"SYP\",\"THB\": \"THB\",\"TOP\": \"TOP\",\"TTD\": \"TTD\",\"TRY\": \"TRY\",\"UAH\": \"UAH\",\"AED\": \"AED\",\"USD\": \"USD\",\"VUV\": \"VUV\",\"VND\": \"VND\",\"XOF\": \"XOF\",\"YER\": \"YER\"}', 0, '{\"approved_url\":{\"title\": \"Approved URL\",\"value\":\"ipn.TwoCheckout\"}}', NULL, NULL, '2023-04-29 09:21:58'),
(54, 0, 123, 'Checkout', 'Checkout', '663a3628733351715090984.png', 1, '{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"------\"},\"public_key\":{\"title\":\"PUBLIC KEY\",\"global\":true,\"value\":\"------\"},\"processing_channel_id\":{\"title\":\"PROCESSING CHANNEL\",\"global\":true,\"value\":\"------\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"AUD\":\"AUD\",\"CAN\":\"CAN\",\"CHF\":\"CHF\",\"SGD\":\"SGD\",\"JPY\":\"JPY\",\"NZD\":\"NZD\"}', 0, NULL, NULL, NULL, '2023-05-06 07:43:01'),
(56, 0, 510, 'Binance', 'Binance', '663a35db4fd621715090907.png', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"tsu3tjiq0oqfbtmlbevoeraxhfbp3brejnm9txhjxcp4to29ujvakvfl1ibsn3ja\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"jzngq4t04ltw8d4iqpi7admfl8tvnpehxnmi34id1zvfaenbwwvsvw7llw3zdko8\"},\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"231129033\"}}', '{\"BTC\":\"Bitcoin\",\"USD\":\"USD\",\"BNB\":\"BNB\"}', 1, '{\"cron\":{\"title\": \"Cron Job URL\",\"value\":\"ipn.Binance\"}}', NULL, NULL, '2023-02-14 05:08:04'),
(57, 0, 124, 'SslCommerz', 'SslCommerz', '663a397a70c571715091834.png', 1, '{\"store_id\": {\"title\": \"Store ID\",\"global\": true,\"value\": \"---------\"},\"store_password\": {\"title\": \"Store Password\",\"global\": true,\"value\": \"----------\"}}', '{\"BDT\":\"BDT\",\"USD\":\"USD\",\"EUR\":\"EUR\",\"SGD\":\"SGD\",\"INR\":\"INR\",\"MYR\":\"MYR\"}', 0, NULL, NULL, NULL, '2023-05-06 07:43:01'),
(58, 0, 125, 'Aamarpay', 'Aamarpay', '663a34d5d1dfc1715090645.png', 1, '{\"store_id\": {\"title\": \"Store ID\",\"global\": true,\"value\": \"---------\"},\"signature_key\": {\"title\": \"Signature Key\",\"global\": true,\"value\": \"----------\"}}', '{\"BDT\":\"BDT\"}', 0, NULL, NULL, NULL, '2023-05-06 07:43:01'),
(59, 0, 126, 'bKash', 'BKash', '67e1432683b5a1742816038.png', 1, '{\"username\":{\"title\":\"Username\",\"global\":true,\"value\":\"----------------\"},\"password\":{\"title\":\"Password\",\"global\":true,\"value\":\"----------------\"},\"app_key\":{\"title\":\"App Key\",\"global\":true,\"value\":\"----------------\"},\"app_secret\":{\"title\":\"App Secret\",\"global\":true,\"value\":\"----------------\"}}', '{\"BDT\":\"BDT\"}', 0, NULL, NULL, NULL, '2025-03-15 21:58:31');

-- --------------------------------------------------------

--
-- Table structure for table `gateway_currencies`
--

CREATE TABLE `gateway_currencies` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `symbol` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method_code` int DEFAULT NULL,
  `gateway_alias` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `max_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `percent_charge` decimal(5,2) NOT NULL DEFAULT '0.00',
  `fixed_charge` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `rate` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `gateway_parameter` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `general_settings`
--

CREATE TABLE `general_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `site_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cur_text` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'currency text',
  `cur_sym` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'currency symbol',
  `email_from` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_from_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_template` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `sms_template` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sms_from` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `push_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `push_template` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `base_color` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_config` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'email configuration',
  `sms_config` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `firebase_config` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `global_shortcodes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ev` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'email verification, 0 - dont check, 1 - check',
  `en` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'email notification, 0 - dont send, 1 - send',
  `sv` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'mobile verication, 0 - dont check, 1 - check',
  `sn` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'sms notification, 0 - dont send, 1 - send',
  `pn` tinyint(1) NOT NULL DEFAULT '1',
  `force_ssl` tinyint(1) NOT NULL DEFAULT '0',
  `maintenance_mode` tinyint(1) NOT NULL DEFAULT '0',
  `secure_password` tinyint(1) NOT NULL DEFAULT '0',
  `agree` tinyint(1) NOT NULL DEFAULT '0',
  `multi_language` tinyint(1) NOT NULL DEFAULT '1',
  `registration` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: Off	, 1: On',
  `active_template` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `system_customized` tinyint(1) NOT NULL DEFAULT '0',
  `paginate_number` int NOT NULL DEFAULT '0',
  `currency_format` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=>Both\r\n2=>Text Only\r\n3=>Symbol Only',
  `config_progress` text COLLATE utf8mb4_unicode_ci,
  `available_version` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_plan` int NOT NULL DEFAULT '0' COMMENT 'if select No free /Default plan then all plan are paid',
  `default_plan_duration` int NOT NULL DEFAULT '0',
  `default_plan_status` tinyint(1) NOT NULL DEFAULT '0',
  `auto_refer_enable` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'global auto referral on/off',
  `auto_refer_require_active_plan` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'eligibility: active plan required',
  `auto_refer_require_email` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'eligibility: email verified required',
  `auto_refer_method` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'round_robin' COMMENT 'round_robin / random / fixed',
  `auto_refer_fixed_user_id` int UNSIGNED NOT NULL DEFAULT '0',
  `auto_refer_last_user_id` int UNSIGNED NOT NULL DEFAULT '0',
  `level_credit_depth` int NOT NULL DEFAULT '10' COMMENT 'how far team sales credit travels up the chain',
  `renewal_sales_percent` int NOT NULL DEFAULT '100',
  `withdraw_grace_days` int NOT NULL DEFAULT '0',
  `activation_rule` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'receiving paid commissions requires an active plan',
  `monoline_enabled` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'monoline earning stream on/off',
  `monoline_percents` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '10,9,8,7,6,5,4,3,2,1' COMMENT 'comma-separated percent per monoline level',
  `monoline_levels` int NOT NULL DEFAULT '10' COMMENT 'how deep monoline payouts go',
  `company_profit_percent` decimal(5,2) NOT NULL DEFAULT '45.00' COMMENT 'platform keeps this % of every payment',
  `matching_bonus_percent` decimal(5,2) NOT NULL DEFAULT '10.00' COMMENT 'leaders earn % of direct referrals earnings',
  `ai_enable` tinyint(1) NOT NULL DEFAULT '0',
  `ai_api_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ai_base_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'https://api.openai.com/v1',
  `ai_model` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'gpt-4o-mini',
  `ai_features` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ai_monthly_cap` int NOT NULL DEFAULT '0' COMMENT '0 = unlimited',
  `epin` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'e-pin module on/off',
  `epin_charge` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'percent charge to generate a pin',
  `epin_seller_bonus_percent` decimal(5,2) NOT NULL DEFAULT 3.00 COMMENT 'percent of a pin-funded plan purchase paid to the pin issuer (e-pin agent)',
  `epin_agent_discounts` varchar(255) NOT NULL DEFAULT '50:3,100:5,500:8' COMMENT 'agent volume discount tiers, format qty:percent comma separated, ascending qty',
  `balance_transfer` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'member to member transfer on/off',
  `balance_transfer_fixed_charge` decimal(28,8) NOT NULL DEFAULT '0',
  `balance_transfer_per_charge` decimal(5,2) NOT NULL DEFAULT '0',
  `balance_transfer_min` decimal(28,8) NOT NULL DEFAULT '0',
  `balance_transfer_max` decimal(28,8) NOT NULL DEFAULT '0',
  `user_ranking` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'ranking module on/off',
  `affiliate_company_percent` decimal(5,2) NOT NULL DEFAULT '60.00' COMMENT 'company share of an eligible purchase',
  `affiliate_pool_percent` decimal(5,2) NOT NULL DEFAULT '40.00' COMMENT 'total affiliate commission pool',
  `affiliate_royalty_percent` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'extended-team royalty (retired at 0)',
  `affiliate_level_weights` varchar(100) NOT NULL DEFAULT '10,9,8,7,6,5,4,3,2,1' COMMENT 'ten-level weights, total 55',
  `withdraw_kyc` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'require KYC for withdrawal',
  `last_cron` datetime DEFAULT NULL COMMENT 'last cron execution time',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `general_settings`
--

INSERT INTO `general_settings` (`id`, `site_name`, `cur_text`, `cur_sym`, `email_from`, `email_from_name`, `email_template`, `sms_template`, `sms_from`, `push_title`, `push_template`, `base_color`, `mail_config`, `sms_config`, `firebase_config`, `global_shortcodes`, `ev`, `en`, `sv`, `sn`, `pn`, `force_ssl`, `maintenance_mode`, `secure_password`, `agree`, `multi_language`, `registration`, `active_template`, `system_customized`, `paginate_number`, `currency_format`, `config_progress`, `available_version`, `default_plan`, `default_plan_duration`, `default_plan_status`, `auto_refer_enable`, `auto_refer_require_active_plan`, `auto_refer_require_email`, `auto_refer_method`, `auto_refer_fixed_user_id`, `auto_refer_last_user_id`, `level_credit_depth`, `renewal_sales_percent`, `withdraw_grace_days`, `activation_rule`, `monoline_enabled`, `monoline_percents`, `monoline_levels`, `company_profit_percent`, `matching_bonus_percent`, `ai_enable`, `ai_api_key`, `ai_base_url`, `ai_model`, `ai_features`, `ai_monthly_cap`, `epin`, `epin_charge`, `epin_seller_bonus_percent`, `epin_agent_discounts`, `balance_transfer`, `balance_transfer_fixed_charge`, `balance_transfer_per_charge`, `balance_transfer_min`, `balance_transfer_max`, `user_ranking`, `affiliate_company_percent`, `affiliate_pool_percent`, `affiliate_royalty_percent`, `affiliate_level_weights`, `withdraw_kyc`, `last_cron`, `created_at`, `updated_at`) VALUES (1, 'LeadxGPT', 'USD', '$', 'info@leadxgpt.com', 'LeadxGPT', '<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\r\n  <!--[if !mso]><!-->\r\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\r\n  <!--<![endif]-->\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title></title>\r\n  <style type=\"text/css\">\r\n.ReadMsgBody { width: 100%; background-color: #ffffff; }\r\n.ExternalClass { width: 100%; background-color: #ffffff; }\r\n.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }\r\nhtml { width: 100%; }\r\nbody { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }\r\ntable { border-spacing: 0; table-layout: fixed; margin: 0 auto;border-collapse: collapse; }\r\ntable table table { table-layout: auto; }\r\n.yshortcuts a { border-bottom: none !important; }\r\nimg:hover { opacity: 0.9 !important; }\r\na { color: #0087ff; text-decoration: none; }\r\n.textbutton a { font-family: \'open sans\', arial, sans-serif !important;}\r\n.btn-link a { color:#FFFFFF !important;}\r\n\r\n@media only screen and (max-width: 480px) {\r\nbody { width: auto !important; }\r\n*[class=\"table-inner\"] { width: 90% !important; text-align: center !important; }\r\n*[class=\"table-full\"] { width: 100% !important; text-align: center !important; }\r\n/* image */\r\nimg[class=\"img1\"] { width: 100% !important; height: auto !important; }\r\n}\r\n</style>\r\n\r\n\r\n\r\n  <table bgcolor=\"#414a51\" width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n    <tbody><tr>\r\n      <td height=\"50\"></td>\r\n    </tr>\r\n    <tr>\r\n      <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n        <table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n          <tbody><tr>\r\n            <td align=\"center\" width=\"600\">\r\n              <!--header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#0087ff\" style=\"border-top-left-radius:6px; border-top-right-radius:6px;text-align:center;vertical-align:top;font-size:0;\" align=\"center\">\r\n                    <table width=\"90%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#FFFFFF; font-size:16px; font-weight: bold;\">This is a System Generated Email</td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n              <!--end header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#FFFFFF\" align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"35\"></td>\r\n                      </tr>\r\n                      <!--logo-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"vertical-align:top;font-size:0;\">\r\n                          <a href=\"#\">\r\n                            <img style=\"display:block; line-height:0px; font-size:0px; border:0px;\" src=\"https://i.imgur.com/Z1qtvtV.png\" alt=\"img\">\r\n                          </a>\r\n                        </td>\r\n                      </tr>\r\n                      <!--end logo-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n                      <!--headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open Sans\', Arial, sans-serif; font-size: 22px;color:#414a51;font-weight: bold;\">Hello {{fullname}} ({{username}})</td>\r\n                      </tr>\r\n                      <!--end headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                          <table width=\"40\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                            <tbody><tr>\r\n                              <td height=\"20\" style=\" border-bottom:3px solid #0087ff;\"></td>\r\n                            </tr>\r\n                          </tbody></table>\r\n                        </td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <!--content-->\r\n                      <tr>\r\n                        <td align=\"left\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px;\">{{message}}</td>\r\n                      </tr>\r\n                      <!--end content-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n              \r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td height=\"45\" align=\"center\" bgcolor=\"#f4f4f4\" style=\"border-bottom-left-radius:6px;border-bottom-right-radius:6px;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                      <!--preference-->\r\n                      <tr>\r\n                        <td class=\"preference-link\" align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#95a5a6; font-size:14px;\">\r\n                          © 2024 <a href=\"#\">{{site_name}}</a>&nbsp;. All Rights Reserved. \r\n                        </td>\r\n                      </tr>\r\n                      <!--end preference-->\r\n                      <tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n            </td>\r\n          </tr>\r\n        </tbody></table>\r\n      </td>\r\n    </tr>\r\n    <tr>\r\n      <td height=\"60\"></td>\r\n    </tr>\r\n  </tbody></table>', 'hi {{fullname}} ({{username}}), {{message}}', 'LeadxGPT', NULL, NULL, '4F46E5', '{\"name\":\"php\"}', '{\"name\":\"nexmo\",\"clickatell\":{\"api_key\":\"----------------\"},\"infobip\":{\"username\":\"------------8888888\",\"password\":\"-----------------\"},\"message_bird\":{\"api_key\":\"-------------------\"},\"nexmo\":{\"api_key\":\"----------------------\",\"api_secret\":\"----------------------\"},\"sms_broadcast\":{\"username\":\"----------------------\",\"password\":\"-----------------------------\"},\"twilio\":{\"account_sid\":\"-----------------------\",\"auth_token\":\"---------------------------\",\"from\":\"----------------------\"},\"text_magic\":{\"username\":\"-----------------------\",\"apiv2_key\":\"-------------------------------\"},\"custom\":{\"method\":\"get\",\"url\":\"https:\\/\\/hostname\\/demo-api-v1\",\"headers\":{\"name\":[\"api_key\"],\"value\":[\"test_api 555\"]},\"body\":{\"name\":[\"from_number\"],\"value\":[\"5657545757\"]}}}', NULL, '{\n    \"site_name\":\"Name of your site\",\n    \"site_currency\":\"Currency of your site\",\n    \"currency_symbol\":\"Symbol of currency\"\n}', 0, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 'basic', 0, 20, 1, NULL, '2.0', 1, 10, 1, 1, 0, 0, 'round_robin', 0, 0, 10, 100, 0, 1, 1, '10,9,8,7,6,5,4,3,2,1', 10, 45.00, 10.00, 0, '', 'https://api.openai.com/v1', 'gpt-4o-mini', '{}', 0, 1, 0.00, 3.00, '50:3,100:5,500:8', 0, 0, 0.00, 0, 0, 1, 60.00, 40.00, 0.00, '10,9,8,7,6,5,4,3,2,1', 1, NULL, NULL, '2024-06-29 04:14:39');

-- --------------------------------------------------------

--
-- Table structure for table `held_commission_logs`
--
CREATE TABLE `held_commission_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'receiver of the held commission',
  `from_user_id` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'member whose purchase produced it',
  `purchase_trx` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` int NOT NULL DEFAULT '1',
  `amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `reason` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'depth / no_plan',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1: held, 2: released',
  `released_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------



--
-- Dumping data for table `event_categories`
--

INSERT INTO `event_categories` (`id`, `name`, `slug`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Business Conference', 'business-conference', 1, NULL, '2024-06-29 04:14:39'),
(2, 'Networking Meetup', 'networking-meetup', 1, NULL, '2024-06-29 04:14:39'),
(3, 'Workshop', 'workshop', 1, NULL, '2024-06-29 04:14:39'),
(4, 'Trade Show', 'trade-show', 1, NULL, '2024-06-29 04:14:39'),
(5, 'Social Gathering', 'social-gathering', 1, NULL, '2024-06-29 04:14:39');

--
-- Seed sample events
--

INSERT INTO `events` (`id`, `user_id`, `title`, `description`, `category_id`, `country_id`, `city_id`, `venue`, `start_time`, `end_time`, `cover_image`, `status`, `is_featured`, `created_at`, `updated_at`) VALUES
(1, 900, 'Annual Business Networking Summit', 'Join entrepreneurs, business owners and affiliates for a day of networking, workshops and growth strategies.', 1, 1, 1, 'Grand Convention Center', '2026-11-20 09:00:00', '2026-11-20 18:00:00', NULL, 1, 1, NULL, '2024-06-29 04:14:39'),
(2, 900, 'Digital Marketing Workshop', 'Hands-on workshop covering SEO, social media marketing and lead generation strategies for local businesses.', 3, 1, 2, 'Tech Hub Los Angeles', '2026-11-27 10:00:00', '2026-11-27 16:00:00', NULL, 1, 0, NULL, '2024-06-29 04:14:39'),
(3, 900, 'Trade Show Expo', 'Exhibition of local businesses, products and services.', 4, 3, 4, 'Dubai World Trade Centre', '2026-12-04 09:00:00', '2026-12-06 18:00:00', NULL, 1, 1, NULL, '2024-06-29 04:14:39');

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: not default language, 1: default language',
  `image` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`, `code`, `is_default`, `image`, `created_at`, `updated_at`) VALUES
(1, 'English', 'en', 1, '667fe1aa2c6cd1719656874.png', '2020-07-06 03:47:55', '2024-06-29 04:27:54');

-- --------------------------------------------------------

--
-- Table structure for table `listings`
--

CREATE TABLE `listings` (
  `id` bigint NOT NULL,
  `listing_id` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `user_id` int NOT NULL COMMENT 'agent/broker',
  `category_id` int NOT NULL DEFAULT '0',
  `city_id` int NOT NULL DEFAULT '0' COMMENT 'Location of PROPERTY',
  `country_id` int NOT NULL DEFAULT '0',
  `display_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `open_time` time DEFAULT NULL,
  `close_time` time DEFAULT NULL,
  `day_off` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amenities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'List of amenities',
  `map` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Exact location by map',
  `videos` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0->Nofeatured, 1->Featured',
  `rejected_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1⇾"Approved",  \r\n0⇾"pending", \r\n2⇾"Rejected", \r\n3⇾"Unavailable"',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ai_review_summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'cached AI review summary'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--
CREATE TABLE `leads` (
  `id` bigint UNSIGNED NOT NULL,
  `listing_id` int UNSIGNED DEFAULT NULL,
  `user_id` int UNSIGNED DEFAULT NULL COMMENT 'owner receiving the lead',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `match_result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'JSON matched listing ids',
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new' COMMENT 'new / contacted / closed',
  `source` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public' COMMENT 'public form or import',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `listing_images`
--

CREATE TABLE `listing_images` (
  `id` bigint NOT NULL,
  `listing_id` int NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_logs`
--

CREATE TABLE `notification_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL DEFAULT '0',
  `sender` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sent_from` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sent_to` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `notification_type` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_read` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_templates`
--

CREATE TABLE `notification_templates` (
  `id` bigint UNSIGNED NOT NULL,
  `act` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `push_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `sms_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `push_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `shortcodes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `email_status` tinyint(1) NOT NULL DEFAULT '1',
  `email_sent_from_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_sent_from_address` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sms_status` tinyint(1) NOT NULL DEFAULT '1',
  `sms_sent_from` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `push_status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_templates`
--

INSERT INTO `notification_templates` (`id`, `act`, `name`, `subject`, `push_title`, `email_body`, `sms_body`, `push_body`, `shortcodes`, `email_status`, `email_sent_from_name`, `email_sent_from_address`, `sms_status`, `sms_sent_from`, `push_status`, `created_at`, `updated_at`) VALUES
(3, 'PAYMENT_COMPLETE', 'Payment - Automated - Successful', 'Deposit Completed Successfully', NULL, '<div>Your deposit of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}}&nbsp;</span>has been completed Successfully.<span style=\"font-weight: bolder;\"><br></span></div><div><span style=\"font-weight: bolder;\"><br></span></div><div><span style=\"font-weight: bolder;\">Details of your Deposit :<br></span></div><div><br></div><div>Amount : {{amount}} {{site_currency}}</div><div>Charge:&nbsp;<font color=\"#000000\">{{charge}} {{site_currency}}</font></div><div><br></div><div>Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div>Received : {{method_amount}} {{method_currency}}<br></div><div>Paid via :&nbsp; {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div>', '{{amount}} {{site_currency}} Deposit successfully by {{method_name}}', NULL, '{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2022-04-03 02:25:43'),
(4, 'PAYMENT_APPROVE', 'Payment - Manual - Approved', 'Your Deposit is Approved', NULL, '<div style=\"font-family: Montserrat, sans-serif;\">Your deposit request of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}}&nbsp;</span>is Approved .<span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\">Details of your Deposit :<br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Amount : {{amount}} {{site_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">Charge:&nbsp;<font color=\"#FF0000\">{{charge}} {{site_currency}}</font></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">Received : {{method_amount}} {{method_currency}}<br></div><div style=\"font-family: Montserrat, sans-serif;\">Paid via :&nbsp; {{method_name}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Transaction Number : {{trx}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div>', 'Admin Approve Your {{amount}} {{site_currency}} payment request by {{method_name}} transaction : {{trx}}', NULL, '{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2022-04-03 02:26:07'),
(5, 'PAYMENT_REJECT', 'Payment - Manual - Rejected', 'Your Deposit Request is Rejected', NULL, '<div style=\"font-family: Montserrat, sans-serif;\">Your deposit request of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}} has been rejected</span>.<span style=\"font-weight: bolder;\"><br></span></div><div><br></div><div><br></div><div style=\"font-family: Montserrat, sans-serif;\">Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div style=\"font-family: Montserrat, sans-serif;\">Received : {{method_amount}} {{method_currency}}<br></div><div style=\"font-family: Montserrat, sans-serif;\">Paid via :&nbsp; {{method_name}}</div><div style=\"font-family: Montserrat, sans-serif;\">Charge: {{charge}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Transaction Number was : {{trx}}</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">if you have any queries, feel free to contact us.<br></div><br style=\"font-family: Montserrat, sans-serif;\"><div style=\"font-family: Montserrat, sans-serif;\"><br><br></div><span style=\"color: rgb(33, 37, 41); font-family: Montserrat, sans-serif;\">{{rejection_message}}</span><br>', 'Admin Rejected Your {{amount}} {{site_currency}} payment request by {{method_name}}\r\n\r\n{{rejection_message}}', NULL, '{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"rejection_message\":\"Rejection message by the admin\"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2022-04-05 03:45:27'),
(6, 'PAYMENT_REQUEST', 'Payment - Manual - Requested', 'Deposit Request Submitted Successfully', NULL, '<div>Your deposit request of&nbsp;<span style=\"font-weight: bolder;\">{{amount}} {{site_currency}}</span>&nbsp;is via&nbsp;&nbsp;<span style=\"font-weight: bolder;\">{{method_name}}&nbsp;</span>submitted successfully<span style=\"font-weight: bolder;\">&nbsp;.<br></span></div><div><span style=\"font-weight: bolder;\"><br></span></div><div><span style=\"font-weight: bolder;\">Details of your Deposit :<br></span></div><div><br></div><div>Amount : {{amount}} {{site_currency}}</div><div>Charge:&nbsp;<font color=\"#FF0000\">{{charge}} {{site_currency}}</font></div><div><br></div><div>Conversion Rate : 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div>Payable : {{method_amount}} {{method_currency}}<br></div><div>Pay via :&nbsp; {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div><div><br></div><div><br style=\"font-family: Montserrat, sans-serif;\"></div>', '{{amount}} {{site_currency}} Deposit requested by {{method_name}}. Charge: {{charge}} . Trx: {{trx}}', NULL, '{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2022-04-03 02:29:19'),
(7, 'PASS_RESET_CODE', 'Password - Reset - Code', 'Password Reset', NULL, '<div style=\"font-family: Montserrat, sans-serif;\">We have received a request to reset the password for your account on&nbsp;<span style=\"font-weight: bolder;\">{{time}} .<br></span></div><div style=\"font-family: Montserrat, sans-serif;\">Requested From IP:&nbsp;<span style=\"font-weight: bolder;\">{{ip}}</span>&nbsp;using&nbsp;<span style=\"font-weight: bolder;\">{{browser}}</span>&nbsp;on&nbsp;<span style=\"font-weight: bolder;\">{{operating_system}}&nbsp;</span>.</div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><br style=\"font-family: Montserrat, sans-serif;\"><div style=\"font-family: Montserrat, sans-serif;\"><div>Your account recovery code is:&nbsp;&nbsp;&nbsp;<font size=\"6\"><span style=\"font-weight: bolder;\">{{code}}</span></font></div><div><br></div></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\"><font size=\"4\" color=\"#CC0000\">If you do not wish to reset your password, please disregard this message.&nbsp;</font><br></div><div><font size=\"4\" color=\"#CC0000\"><br></font></div>', 'Your account recovery code is: {{code}}', NULL, '{\"code\":\"Verification code for password reset\",\"ip\":\"IP address of the user\",\"browser\":\"Browser of the user\",\"operating_system\":\"Operating system of the user\",\"time\":\"Time of the request\"}', 1, NULL, NULL, 0, NULL, 0, '2021-11-03 12:00:00', '2022-03-20 20:47:05'),
(8, 'PASS_RESET_DONE', 'Password - Reset - Confirmation', 'You have reset your password', NULL, '<p style=\"font-family: Montserrat, sans-serif;\">You have successfully reset your password.</p><p style=\"font-family: Montserrat, sans-serif;\">You changed from&nbsp; IP:&nbsp;<span style=\"font-weight: bolder;\">{{ip}}</span>&nbsp;using&nbsp;<span style=\"font-weight: bolder;\">{{browser}}</span>&nbsp;on&nbsp;<span style=\"font-weight: bolder;\">{{operating_system}}&nbsp;</span>&nbsp;on&nbsp;<span style=\"font-weight: bolder;\">{{time}}</span></p><p style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></p><p style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><font color=\"#ff0000\">If you did not change that, please contact us as soon as possible.</font></span></p>', 'Your password has been changed successfully', NULL, '{\"ip\":\"IP address of the user\",\"browser\":\"Browser of the user\",\"operating_system\":\"Operating system of the user\",\"time\":\"Time of the request\"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2022-04-05 03:46:35'),
(9, 'ADMIN_SUPPORT_REPLY', 'Support - Reply', 'Reply Support Ticket', NULL, '<div><p><span data-mce-style=\"font-size: 11pt;\" style=\"font-size: 11pt;\"><span style=\"font-weight: bolder;\">A member from our support team has replied to the following ticket:</span></span></p><p><span style=\"font-weight: bolder;\"><span data-mce-style=\"font-size: 11pt;\" style=\"font-size: 11pt;\"><span style=\"font-weight: bolder;\"><br></span></span></span></p><p><span style=\"font-weight: bolder;\">[Ticket#{{ticket_id}}] {{ticket_subject}}<br><br>Click here to reply:&nbsp; {{link}}</span></p><p>----------------------------------------------</p><p>Here is the reply :<br></p><p>{{reply}}<br></p></div><div><br style=\"font-family: Montserrat, sans-serif;\"></div>', 'Your Ticket#{{ticket_id}} :  {{ticket_subject}} has been replied.', NULL, '{\"ticket_id\":\"ID of the support ticket\",\"ticket_subject\":\"Subject  of the support ticket\",\"reply\":\"Reply made by the admin\",\"link\":\"URL to view the support ticket\"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2022-03-20 20:47:51'),
(10, 'EVER_CODE', 'Verification - Email', 'Please verify your email address', NULL, '<br><div><div style=\"font-family: Montserrat, sans-serif;\">Thanks For joining us.<br></div><div style=\"font-family: Montserrat, sans-serif;\">Please use the below code to verify your email address.<br></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Your email verification code is:<font size=\"6\"><span style=\"font-weight: bolder;\">&nbsp;{{code}}</span></font></div></div>', '---', NULL, '{\"code\":\"Email verification code\"}', 1, NULL, NULL, 0, NULL, 0, '2021-11-03 12:00:00', '2022-04-03 02:32:07'),
(11, 'SVER_CODE', 'Verification - SMS', 'Verify Your Mobile Number', NULL, '---', 'Your phone verification code is: {{code}}', NULL, '{\"code\":\"SMS Verification Code\"}', 0, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2022-03-20 19:24:37'),
(12, 'BUY_PLAN', 'Buy- Plan', 'You have successfully bought the plan', NULL, '<div style=\"font-family: Montserrat, sans-serif;\">Your <span style=\"font-weight: bolder;\">{{name}} </span> plan &nbsp;<span style=\"font-weight: bolder;\">{{price}} {{site_currency}} &nbsp;</span>has been bought successfully.<span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\">Details of your Plan:<br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Plan : {{name}}</div><div style=\"font-family: Montserrat, sans-serif;\">Price:&nbsp;<font>{{price}} {{site_currency}}</font></div><div style=\"font-family: Montserrat, sans-serif;\">Property Create : {{property}}</div><br><div style=\"font-family: Montserrat, sans-serif;\">Enjoy! And create your property.</div>', 'Plan has been bought successfully. Plan:{{name}}, price:{{price}} {{site_currency}}.\r\n', NULL, '{\r\n\"name\":\"Name of purchase plan\",\r\n\"price\":\"Price of purchase plan\"\r\n}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2022-03-20 20:50:16'),
(15, 'DEFAULT', 'Default Template', '{{subject}}', '{{subject}}', '{{message}}', '{{message}}', '{{subject}}', '{\"subject\":\"Subject\",\"message\":\"Message\"}', 1, NULL, NULL, 1, NULL, 1, '2019-09-14 13:14:22', '2024-06-29 04:16:49'),
(18, 'DEFAULT_PLAN', 'Default/Free- Plan', 'You have successfully got a default/free plan.', NULL, '<div style=\"font-family: Montserrat, sans-serif;\">You have successfully got a default/free plan.<span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\">Details of your Plan:<br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><div style=\"font-family: Montserrat, sans-serif;\">Plan : {{name}}</div><div style=\"font-family: Montserrat, sans-serif;\">Expired:&nbsp;<font>{{expired}} </font></div><div style=\"font-family: Montserrat, sans-serif;\">Property Create : {{property}}</div><br><div style=\"font-family: Montserrat, sans-serif;\">Enjoy! And create your property.</div>', 'You have successfully got a default/free plan. Plan:{{name}}, Create Listing: {{listing}}, Expired:{{expired}}.\r\n', NULL, '{\"name\":\"Name of purchase plan\",\r\n\"expired\":\"Plan duration\",\r\n\"listing\":\"Total number of create listing\"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2022-03-20 20:50:16'),
(19, 'LISTING_APPROVED', 'Listing Approved', 'Listing have been approved successfully.', NULL, '<div style=\"font-family: Montserrat, sans-serif;\">The listing has&nbsp;been approved successfully.<span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\">Details of your Listing:<br></span></div><div style=\"font-family: Montserrat, sans-serif;\"></div><div style=\"font-family: Montserrat, sans-serif;\">Title of Listing: {{title}}</div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"color: rgb(33, 37, 41);\">Listing Location: {{city}}</span></div><div style=\"font-family: Montserrat, sans-serif;\"><br></div><br><div style=\"font-family: Montserrat, sans-serif;\">Enjoy! Good luck for your business.</div>', 'The listing has been approved successfully. Listing:{{title}}, Location: {{city}}.', NULL, '{\"title\":\"Name of Property\",\r\n\"city\":\"Property Location\"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2023-08-30 04:28:28'),
(20, 'LISTING_REJECTED', 'Property Rejected', 'Listing have been rejected successfully.', NULL, '<div style=\"font-family: Montserrat, sans-serif;\">The listing has been rejected.<span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\"><br></span></div><div style=\"font-family: Montserrat, sans-serif;\"><span style=\"font-weight: bolder;\">Details of your Listing:<br></span></div><div style=\"font-family: Montserrat, sans-serif;\"></div><div style=\"font-family: Montserrat, sans-serif;\">Title of Listing: {{title}}</div><div style=\"font-family: Montserrat, sans-serif;\">Listing Location : {{city}}</div><br><div style=\"font-family: Montserrat, sans-serif;\">If any queries about it,  please feel free to contact us.</div>', 'The listing has been rejected successfully. Listing:{{title}}, Location: {{city}}.', NULL, '{\"title\":\"Name of Property\",\r\n\"city\":\"Property Location\"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2023-08-29 06:52:24');

-- --------------------------------------------------------

--
-- Dumping data for table `notification_templates` (affiliate engine)
--

INSERT INTO `notification_templates` (`id`, `act`, `name`, `subject`, `push_title`, `email_body`, `sms_body`, `push_body`, `shortcodes`, `email_status`, `email_sent_from_name`, `email_sent_from_address`, `sms_status`, `sms_sent_from`, `push_status`, `created_at`, `updated_at`) VALUES
(21, 'REFERRAL_COMMISSION', 'Affiliate - Direct Commission', 'You Have Received a Direct Referral Commission', NULL, '<div>Your direct referral commission of <b>{{amount}} {{site_currency}}</b> has been credited for the plan purchase by <b>{{username}}</b>.</div><div><br></div><div>Transaction Number: {{trx}}</div><div>Current Balance: {{post_balance}} {{site_currency}}</div>', 'You have received {{amount}} {{site_currency}} direct referral commission from {{username}}', NULL, '{"amount":"Commission amount","username":"Member who purchased the plan","post_balance":"Balance after commission","trx":"Purchase transaction number"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2024-06-29 04:16:49'),
(22, 'LEVEL_COMMISSION', 'Affiliate - Level Commission', 'You Have Received a Level Commission', NULL, '<div>Your level {{level}} commission of <b>{{amount}} {{site_currency}}</b> has been credited for the plan purchase by <b>{{username}}</b>.</div><div><br></div><div>Transaction Number: {{trx}}</div><div>Current Balance: {{post_balance}} {{site_currency}}</div>', 'You have received {{amount}} {{site_currency}} level commission from {{username}}', NULL, '{"amount":"Commission amount","level":"Commission level","username":"Member who purchased the plan","post_balance":"Balance after commission","trx":"Purchase transaction number"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2024-06-29 04:16:49'),
(23, 'HELD_COMMISSION', 'Affiliate - Held Earnings', 'A Commission Has Been Added to Your Held Earnings', NULL, '<div>A commission of <b>{{amount}} {{site_currency}}</b> (level {{level}}) from <b>{{username}}</b> has been added to your held earnings because: {{reason}}.</div><div><br></div><div>Held earnings unlock automatically when you upgrade your plan.</div>', 'You have {{amount}} {{site_currency}} in held earnings. Upgrade your plan to unlock.', NULL, '{"amount":"Held amount","level":"Commission level","username":"Member who purchased the plan","reason":"Why the commission is held"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2024-06-29 04:16:49'),
(24, 'COMMISSION_RELEASED', 'Affiliate - Commission Released', 'Your Held Earnings Have Been Released', NULL, '<div><b>{{count}}</b> held commission line(s) totaling <b>{{amount}} {{site_currency}}</b> have been released to your balance.</div><div><br></div><div>Current Balance: {{post_balance}} {{site_currency}}</div>', '{{amount}} {{site_currency}} held earnings released to your balance', NULL, '{"amount":"Total released amount","count":"Number of released lines","post_balance":"Balance after release"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2024-06-29 04:16:49'),
(25, 'RANK_ACHIEVED', 'Affiliate - Rank Achieved', 'Congratulations! You Achieved a New Rank', NULL, '<div>Congratulations! You have achieved the <b>{{rank}}</b> rank.</div>', 'Congratulations! You achieved the {{rank}} rank', NULL, '{\"rank\":\"Name of the achieved rank\"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2024-06-29 04:16:49'),
(26, 'WITHDRAW_REQUEST', 'Withdraw - Request', 'Withdraw Request Submitted', NULL, '<div>Your withdraw request of <b>{{amount}} {{site_currency}}</b> via <b>{{method_name}}</b> has been submitted successfully.</div><div><br></div><div>Transaction Number: {{trx}}</div>', 'Withdraw request of {{amount}} {{site_currency}} submitted', NULL, '{"amount":"Requested amount","method_name":"Withdraw method name","trx":"Withdraw transaction number"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2024-06-29 04:16:49'),
(27, 'WITHDRAW_APPROVE', 'Withdraw - Approved', 'Your Withdraw Request Has Been Approved', NULL, '<div>Your withdraw request of <b>{{amount}} {{site_currency}}</b> via <b>{{method_name}}</b> has been approved.</div><div><br></div><div>Transaction Number: {{trx}}</div>', 'Your withdraw request of {{amount}} {{site_currency}} is approved', NULL, '{"amount":"Withdraw amount","method_name":"Withdraw method name","trx":"Withdraw transaction number"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2024-06-29 04:16:49'),
(28, 'WITHDRAW_REJECT', 'Withdraw - Rejected', 'Your Withdraw Request Has Been Rejected', NULL, '<div>Your withdraw request of <b>{{amount}} {{site_currency}}</b> via <b>{{method_name}}</b> has been rejected.</div><div><br></div><div>Admin Feedback: {{rejection_message}}</div>', 'Your withdraw request of {{amount}} {{site_currency}} is rejected', NULL, '{"amount":"Withdraw amount","method_name":"Withdraw method name","rejection_message":"Rejection reason by the admin"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2024-06-29 04:16:49'),
(29, 'PIN_RECHARGE', 'E-Pin - Recharged', 'Balance Recharged Successfully', NULL, '<div>Your balance has been recharged with <b>{{amount}} {{site_currency}}</b> using e-pin <b>{{pin}}</b>.</div><div><br></div><div>Current Balance: {{post_balance}} {{site_currency}}</div>', 'Balance recharged {{amount}} {{site_currency}} via e-pin', NULL, '{"amount":"Recharged amount","pin":"E-pin used","post_balance":"Balance after recharge"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2024-06-29 04:16:49'),
(30, 'AUTO_REFERRAL_RECEIVED', 'Affiliate - Auto Referral', 'You Have Received an Auto-Assigned Referral', NULL, '<div>Great news! Member <b>{{fullname}}</b> ({{username}}) has been assigned to your team through the Active Affiliate Bonus program.</div><div><br></div><div>Any plan they purchase will pay you a direct referral commission.</div>', 'New auto-assigned referral: {{username}}', NULL, '{"username":"Username of the assigned member","fullname":"Full name of the assigned member"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2024-06-29 04:16:49'),
(31, 'BAL_ADD', 'Balance - Added', 'Balance Added by Administrator', NULL, '<div>An amount of <b>{{amount}} {{site_currency}}</b> has been added to your balance by the administrator.</div><div><br></div><div>Current Balance: {{post_balance}} {{site_currency}}</div>', '{{amount}} {{site_currency}} added to your balance', NULL, '{"amount":"Added amount","post_balance":"Balance after update"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2024-06-29 04:16:49'),
(32, 'BAL_SUB', 'Balance - Subtracted', 'Balance Subtracted by Administrator', NULL, '<div>An amount of <b>{{amount}} {{site_currency}}</b> has been subtracted from your balance by the administrator.</div><div><br></div><div>Current Balance: {{post_balance}} {{site_currency}}</div>', '{{amount}} {{site_currency}} subtracted from your balance', NULL, '{"amount":"Subtracted amount","post_balance":"Balance after update"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2024-06-29 04:16:49'),
(33, 'KYC_APPROVE', 'KYC - Approved', 'KYC Verification Approved', NULL, '<div>Your KYC (identity verification) data has been reviewed and approved.</div><div><br></div><div>You can now use every feature that requires verified identity.</div>', 'Your KYC verification is approved', NULL, '{}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2024-06-29 04:16:49'),
(34, 'KYC_REJECT', 'KYC - Rejected', 'KYC Verification Rejected', NULL, '<div>Your KYC (identity verification) data has been reviewed and rejected.</div><div><br></div><div>Reason: {{rejection_message}}</div><div><br></div><div>Please resubmit with correct information.</div>', 'Your KYC verification is rejected', NULL, '{"rejection_message":"Rejection reason by the admin"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2024-06-29 04:16:49');

--
-- Dumping data for table `notification_templates` (deposit manual gateway)
--

INSERT INTO `notification_templates` (`id`, `act`, `name`, `subject`, `push_title`, `email_body`, `sms_body`, `push_body`, `shortcodes`, `email_status`, `email_sent_from_name`, `email_sent_from_address`, `sms_status`, `sms_sent_from`, `push_status`, `created_at`, `updated_at`) VALUES
(35, 'DEPOSIT_REQUEST', 'Deposit - Manual - Requested', 'Deposit Request Submitted Successfully', NULL, '<div>Your deposit request of <b>{{amount}} {{site_currency}}</b> via <b>{{method_name}}</b> has been submitted successfully and is awaiting administrator approval.</div><div><br></div><div>Amount: {{amount}} {{site_currency}}</div><div>Charge: {{charge}} {{site_currency}}</div><div>Payable: {{method_amount}} {{method_currency}}</div><div>Transaction Number: {{trx}}</div>', 'Deposit request of {{amount}} {{site_currency}} submitted via {{method_name}}', NULL, '{"trx":"Transaction number for the deposit","amount":"Amount inserted by the user","charge":"Gateway charge set by the admin","rate":"Conversion rate","method_name":"Name of the deposit method","method_currency":"Currency of the deposit method","method_amount":"Amount after conversion"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2024-06-29 04:16:49'),
(36, 'DEPOSIT_REJECT', 'Deposit - Manual - Rejected', 'Your Deposit Request is Rejected', NULL, '<div>Your deposit request of <b>{{amount}} {{site_currency}}</b> via <b>{{method_name}}</b> has been rejected.</div><div><br></div><div>Admin Feedback: {{rejection_message}}</div>', 'Your deposit request of {{amount}} {{site_currency}} is rejected', NULL, '{"trx":"Transaction number for the deposit","amount":"Amount inserted by the user","charge":"Gateway charge","rejection_message":"Rejection reason by the admin"}', 1, NULL, NULL, 1, NULL, 0, '2021-11-03 12:00:00', '2024-06-29 04:16:49'),
(37, 'EVENT_REGISTER', 'Event Registration Confirmed', 'Your event registration is confirmed', NULL, '<div>Your registration is confirmed. See you at the event!</div><div><br></div><div>Event: {{event_name}}</div><div>Starts: {{start_time}}</div>', 'Your event registration is confirmed. Event: {{event_name}}, Starts: {{start_time}}.', NULL, '{\"event_name\": \"Event title\", \"start_time\": \"Event start time\"}', 1, NULL, NULL, 1, NULL, 0, '2026-11-14 00:00:00', '2026-11-14 00:00:00'),
(38, 'EVENT_APPROVED', 'Event Approved', 'Your submitted event has been approved', NULL, '<div>Your event has been approved and is now live.</div><div><br></div><div>Event: {{event_name}}</div><div>Starts: {{start_time}}</div>', 'Your event has been approved. Event: {{event_name}}, Starts: {{start_time}}.', NULL, '{\"event_name\": \"Event title\", \"start_time\": \"Event start time\"}', 1, NULL, NULL, 1, NULL, 0, '2026-11-14 00:00:00', '2026-11-14 00:00:00'),
(39, 'DEPOSIT_REFUND', 'Deposit Refunded', 'Your deposit has been refunded', NULL, '<div>Your deposit has been refunded and its effects were reversed.</div><div><br></div><div>Transaction: {{trx}}</div><div>Amount: {{amount}}</div>', 'Your deposit has been refunded. Transaction: {{trx}}, Amount: {{amount}}.', NULL, '{\"trx\": \"Transaction ID\", \"amount\": \"Refunded amount\"}', 1, NULL, NULL, 1, NULL, 0, '2026-11-14 00:00:00', '2026-11-14 00:00:00');

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempname` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'template name',
  `secs` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `seo_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `name`, `slug`, `tempname`, `secs`, `seo_content`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'HOME', '/', 'templates.basic.', '[\"category\",\"feature_listing\",\"popular_city\",\"cta\",\"recent_listings\",\"events\",\"testimonial\",\"how_work\",\"blog\"]', NULL, 1, '2020-07-11 06:23:58', '2023-09-03 08:58:31'),
(4, 'Blog', 'blog', 'templates.basic.', NULL, NULL, 1, '2020-10-22 01:14:43', '2020-10-22 01:14:43'),
(5, 'Contact', 'contact', 'templates.basic.', '[\"faq\"]', NULL, 1, '2020-10-22 01:14:53', '2023-08-30 03:50:35'),
(19, 'About', 'about', 'templates.basic.', '[\"about\",\"how_work\",\"cta\",\"choose_us\"]', NULL, 1, '2023-08-13 04:02:18', '2023-08-21 10:58:04'),
(21, 'FAQ', 'faq', 'templates.basic.', '[\"category\"]', NULL, 1, '2020-10-22 01:14:53', '2023-08-14 04:32:51'),
(22, 'Terms & Conditions', 'terms', 'templates.basic.', NULL, NULL, 1, '2024-06-29 04:14:39', '2024-06-29 04:14:39'),
(23, 'Privacy Policy', 'privacy', 'templates.basic.', NULL, NULL, 1, '2024-06-29 04:14:39', '2024-06-29 04:14:39'),
(24, 'Refund Policy', 'refund', 'templates.basic.', NULL, NULL, 1, '2024-06-29 04:14:39', '2024-06-29 04:14:39'),
(25, 'Earnings Disclaimer', 'earnings-disclaimer', 'templates.basic.', NULL, NULL, 1, '2024-06-29 04:14:39', '2024-06-29 04:14:39');

-- --------------------------------------------------------

--
-- Table structure for table `pins`
--
CREATE TABLE `pins` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL COMMENT 'owner of the pin (recharge channel)',
  `generate_user_id` int UNSIGNED DEFAULT NULL COMMENT 'member who generated it from balance',
  `amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `pin` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Unused: 0, Used: 1',
  `expires_at` datetime DEFAULT NULL COMMENT 'NULL = never expires',
  `details` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` bigint NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monthly_price` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `yearly_price` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `listings` int NOT NULL DEFAULT '0',
  `amenities` int DEFAULT NULL COMMENT 'Quantities of amenities',
  `images` int DEFAULT NULL COMMENT 'Quantities of images',
  `videos` int NOT NULL COMMENT 'Quantities of videos',
  `events` int NOT NULL DEFAULT '0' COMMENT 'Events the member can host per year (Free 0, tier N = 5N)',
  `map` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=>"NO", 1=>"Yes"',
  `ref_com` decimal(28,8) NOT NULL DEFAULT '0.00000000' COMMENT 'Direct referral commission (fixed amount)',
  `level_com` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'JSON map of level to commission percent, e.g. {"2":10,"3":5}',
  `commission_levels` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'How many commission levels the plan holder may receive',
  `is_commission_eligible` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 = purchase fires the 40% affiliate pool (D2); 0 = 100% company revenue',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL DEFAULT '0',
  `listing_id` int UNSIGNED NOT NULL,
  `review` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `rating` tinyint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_attachments`
--

CREATE TABLE `support_attachments` (
  `id` bigint UNSIGNED NOT NULL,
  `support_message_id` int UNSIGNED NOT NULL DEFAULT '0',
  `attachment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_messages`
--

CREATE TABLE `support_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `support_ticket_id` int UNSIGNED NOT NULL DEFAULT '0',
  `admin_id` int UNSIGNED NOT NULL DEFAULT '0',
  `message` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int DEFAULT '0',
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ticket` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: Open, 1: Answered, 2: Replied, 3: Closed',
  `priority` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = Low, 2 = medium, 3 = heigh',
  `last_reply` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--
CREATE TABLE `transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL DEFAULT '0',
  `amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `charge` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `post_balance` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `trx_type` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trx` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remark` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `update_logs`
--

CREATE TABLE `update_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `version` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `update_log` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `firstname` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dial_code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'contains full address',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0: banned, 1: active',
  `ev` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: email unverified, 1: email verified',
  `sv` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: mobile unverified, 1: mobile verified',
  `profile_complete` tinyint(1) NOT NULL DEFAULT '0',
  `ver_code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'stores verification code',
  `ver_code_send_at` datetime DEFAULT NULL COMMENT 'verification send time',
  `ban_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ref_by` bigint UNSIGNED NOT NULL DEFAULT '0',
  `ref_source` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: referral link, 1: auto assigned',
  `balance` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `total_invest` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `total_ref_com` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `total_level_com` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `user_ranking_id` int UNSIGNED NOT NULL DEFAULT '0',
  `kyc_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `kyc_rejection_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kv` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: KYC unverified, 2: KYC pending, 1: KYC verified',
  `ts` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: 2fa off, 1: 2fa on',
  `tv` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0: 2fa unverified, 1: 2fa verified',
  `tsc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_rank_update` datetime DEFAULT NULL,
  `monoline_position` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'global monoline queue position, minted at registration',
  `is_epin_agent` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'e-pin agent: volume discount on pin generation, seller bonus on pin-funded plan purchases',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_extras`
--
CREATE TABLE `user_extras` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `direct_active` int NOT NULL DEFAULT '0' COMMENT 'direct referrals holding an active plan',
  `direct_total` int NOT NULL DEFAULT '0' COMMENT 'total direct referrals',
  `team_sales` decimal(28,8) NOT NULL DEFAULT '0.00000000' COMMENT 'GAGV - cumulative real plan sales of the team',
  `total_team_sales` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `held_commission` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_logins`
--

CREATE TABLE `user_logins` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL DEFAULT '0',
  `user_ip` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `os` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_rankings`
--
CREATE TABLE `user_rankings` (
  `id` bigint UNSIGNED NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` int DEFAULT '0',
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `team_sales` decimal(28,8) NOT NULL DEFAULT '0.00000000' COMMENT 'GAGV threshold',
  `direct_active` int NOT NULL DEFAULT '0' COMMENT 'active direct referrals threshold',
  `min_plan_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000' COMMENT 'rank tier wall: minimum ACTIVE plan price to withdraw at this rank',
  `bonus` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_plans`
--

CREATE TABLE `user_plans` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `plan_id` int UNSIGNED NOT NULL,
  `plan_price` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `listings` int UNSIGNED NOT NULL DEFAULT '0',
  `amenities` int UNSIGNED NOT NULL DEFAULT '0',
  `images` int UNSIGNED NOT NULL DEFAULT '0',
  `videos` int UNSIGNED NOT NULL DEFAULT '0',
  `events` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Events quota snapshot from the purchased plan',
  `allow_map` tinyint(1) NOT NULL DEFAULT '0',
  `expired_on` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--
CREATE TABLE `withdrawals` (
  `id` bigint UNSIGNED NOT NULL,
  `method_id` int UNSIGNED NOT NULL DEFAULT '0',
  `user_id` int UNSIGNED NOT NULL DEFAULT '0',
  `amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `currency` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rate` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `charge` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `trx` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `final_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `after_charge` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `withdraw_information` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=>success, 2=>pending, 3=>cancel',
  `admin_feedback` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdraw_methods`
--
CREATE TABLE `withdraw_methods` (
  `id` bigint UNSIGNED NOT NULL,
  `form_id` int UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_limit` decimal(28,8) DEFAULT '0.00000000',
  `max_limit` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `fixed_charge` decimal(28,8) DEFAULT '0.00000000',
  `rate` decimal(28,8) DEFAULT '0.00000000',
  `percent_charge` decimal(5,2) DEFAULT NULL,
  `currency` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
--
-- Indexes for table `cron_job_logs`
--
ALTER TABLE `cron_job_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cron_jobs`
--
ALTER TABLE `cron_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cron_schedules`
--
ALTER TABLE `cron_schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `held_commission_logs`
--
ALTER TABLE `held_commission_logs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `held_unique` (`purchase_trx`,`user_id`,`level`),
  ADD KEY `held_commission_logs_user_id_index` (`user_id`);

--
-- Indexes for table `pins`
--
ALTER TABLE `pins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pins_pin_unique` (`pin`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_trx` (`trx`),
  ADD KEY `transactions_user_remark` (`user_id`, `remark`);

--
-- Indexes for table `user_extras`
--
ALTER TABLE `user_extras`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_extras_user_id_unique` (`user_id`);

--
-- Indexes for table `user_rankings`
--
ALTER TABLE `user_rankings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdraw_methods`
--
ALTER TABLE `withdraw_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ai_logs`
--
ALTER TABLE `ai_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`);

-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`,`username`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_password_resets`
--
ALTER TABLE `admin_password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

-- AUTO_INCREMENT for table `cities`
ALTER TABLE `cities`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `device_tokens`
--
ALTER TABLE `device_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `extensions`
--
ALTER TABLE `extensions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favorite_listings`
--
ALTER TABLE `favorite_listings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `affiliate_logs`
--
ALTER TABLE `affiliate_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `affiliate_logs_payment_trx` (`payment_trx`),
  ADD KEY `affiliate_logs_payer_type` (`payment_trx`,`type`),
  ADD KEY `affiliate_logs_beneficiary` (`beneficiary_id`),
  ADD KEY `affiliate_logs_payer` (`payer_id`);

--
-- Indexes for table `event_categories`
--

CREATE TABLE `event_attendees` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL COMMENT 'null = guest registration',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interest` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'what the attendee is looking for',
  `checked_in` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_attendees_event_id_index` (`event_id`),
  KEY `event_attendees_user_id_index` (`user_id`),
  UNIQUE KEY `event_attendees_event_email_unique` (`event_id`, `email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `event_participations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'follow' COMMENT 'follow / interested / joined',
  `paid_amount` decimal(28,8) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_participations_event_user_unique` (`event_id`, `user_id`),
  KEY `event_participations_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `event_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `events_user_id` (`user_id`),
  ADD KEY `events_category_id` (`category_id`);

--
-- Indexes for table `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `frontends`
--
ALTER TABLE `frontends`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gateways`
--
ALTER TABLE `gateways`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--

-- Demo payment methods (admin-configurable in Admin > Gateways)
INSERT INTO `gateway_currencies` (`id`, `name`, `currency`, `symbol`, `method_code`, `gateway_alias`, `min_amount`, `max_amount`, `percent_charge`, `fixed_charge`, `rate`, `created_at`, `updated_at`) VALUES
  (10000, 'PayPal USD', 'USD', '$', 101, 'Paypal', 1.00000000, 50000.00000000, 0, 0, 1, NOW(), NOW()),
  (10001, 'Stripe USD', 'USD', '$', 103, 'Stripe', 1.00000000, 50000.00000000, 0, 0, 1, NOW(), NOW()),
  (10002, 'Skrill USD', 'USD', '$', 104, 'Skrill', 1.00000000, 50000.00000000, 0, 0, 1, NOW(), NOW()),
  (10003, 'Perfect Money USD', 'USD', '$', 102, 'PerfectMoney', 1.00000000, 50000.00000000, 0, 0, 1, NOW(), NOW()),
  (10004, 'Mollie EUR', 'EUR', '€', 108, 'Mollie', 1.00000000, 50000.00000000, 0, 0, 1, NOW(), NOW()),
  (10005, 'Razorpay INR', 'INR', '₹', 110, 'Razorpay', 100.00000000, 5000000.00000000, 0, 0, 1, NOW(), NOW()),
  (10006, 'Paystack NGN', 'NGN', '₦', 118, 'Paystack', 500.00000000, 50000000.00000000, 0, 0, 1, NOW(), NOW()),
  (10007, 'Bank Transfer Manual', 'USD', '$', 1001, 'bank-manual', 1.00000000, 100000.00000000, 0, 0, 1, NOW(), NOW());

-- Full gateway list, copied as-is from the working MLM reference: one USD
-- row per automatic gateway (rate 1, charges 0) so every enabled gateway is
-- listed on Add Funds. Credentials stay empty until the admin saves them.
INSERT INTO `gateway_currencies` (`id`, `name`, `currency`, `symbol`, `method_code`, `gateway_alias`, `min_amount`, `max_amount`, `percent_charge`, `fixed_charge`, `rate`, `created_at`, `updated_at`) VALUES
  (10008, 'PayTM', 'USD', '$', 105, 'Paytm', 1.00000000, 5000.00000000, 0, 0, 1, NOW(), NOW()),
  (10009, 'Payeer', 'USD', '$', 106, 'Payeer', 1.00000000, 5000.00000000, 0, 0, 1, NOW(), NOW()),
  (10010, 'PayStack', 'USD', '$', 107, 'Paystack', 1.00000000, 5000.00000000, 0, 0, 1, NOW(), NOW()),
  (10011, 'Flutterwave', 'USD', '$', 109, 'Flutterwave', 1.00000000, 5000.00000000, 0, 0, 1, NOW(), NOW()),
  (10012, 'Stripe Storefront', 'USD', '$', 111, 'StripeJs', 1.00000000, 5000.00000000, 0, 0, 1, NOW(), NOW()),
  (10013, 'Instamojo', 'USD', '$', 112, 'Instamojo', 1.00000000, 5000.00000000, 0, 0, 1, NOW(), NOW()),
  (10014, 'Paypal Express', 'USD', '$', 113, 'PaypalSdk', 1.00000000, 5000.00000000, 0, 0, 1, NOW(), NOW()),
  (10015, 'Stripe Checkout', 'USD', '$', 114, 'StripeV3', 1.00000000, 5000.00000000, 0, 0, 1, NOW(), NOW()),
  (10016, 'Mollie', 'USD', '$', 115, 'Mollie', 1.00000000, 5000.00000000, 0, 0, 1, NOW(), NOW()),
  (10017, 'Cashmaal', 'USD', '$', 116, 'Cashmaal', 1.00000000, 5000.00000000, 0, 0, 1, NOW(), NOW()),
  (10018, 'Mercado Pago', 'USD', '$', 119, 'MercadoPago', 1.00000000, 5000.00000000, 0, 0, 1, NOW(), NOW()),
  (10019, 'Authorize.net', 'USD', '$', 120, 'Authorize', 1.00000000, 5000.00000000, 0, 0, 1, NOW(), NOW()),
  (10020, 'NMI', 'USD', '$', 121, 'NMI', 1.00000000, 5000.00000000, 0, 0, 1, NOW(), NOW()),
  (10021, '2Checkout', 'USD', '$', 122, 'TwoCheckout', 1.00000000, 5000.00000000, 0, 0, 1, NOW(), NOW()),
  (10022, 'Checkout', 'USD', '$', 123, 'Checkout', 1.00000000, 5000.00000000, 0, 0, 1, NOW(), NOW()),
  (10023, 'SslCommerz', 'USD', '$', 124, 'SslCommerz', 1.00000000, 5000.00000000, 0, 0, 1, NOW(), NOW()),
  (10024, 'Aamarpay', 'USD', '$', 125, 'Aamarpay', 1.00000000, 5000.00000000, 0, 0, 1, NOW(), NOW()),
  (10025, 'bKash', 'USD', '$', 126, 'BKash', 1.00000000, 5000.00000000, 0, 0, 1, NOW(), NOW()),
  (10026, 'Blockchain', 'USDT', '₮', 501, 'Blockchain', 5.00000000, 100000.00000000, 0, 0, 1, NOW(), NOW()),
  (10027, 'CoinPayments', 'USDT', '₮', 503, 'Coinpayments', 5.00000000, 100000.00000000, 0, 0, 1, NOW(), NOW()),
  (10028, 'CoinPayments Fiat', 'USDT', '₮', 504, 'CoinpaymentsFiat', 5.00000000, 100000.00000000, 0, 0, 1, NOW(), NOW()),
  (10029, 'Coingate', 'USDT', '₮', 505, 'Coingate', 5.00000000, 100000.00000000, 0, 0, 1, NOW(), NOW()),
  (10030, 'Coinbase Commerce', 'USDT', '₮', 506, 'CoinbaseCommerce', 5.00000000, 100000.00000000, 0, 0, 1, NOW(), NOW()),
  (10031, 'BTCPay', 'USDT', '₮', 507, 'BTCPay', 5.00000000, 100000.00000000, 0, 0, 1, NOW(), NOW()),
  (10032, 'Now payments hosted', 'USDT', '₮', 508, 'NowPaymentsHosted', 5.00000000, 100000.00000000, 0, 0, 1, NOW(), NOW()),
  (10033, 'Now payments checkout', 'USDT', '₮', 509, 'NowPaymentsCheckout', 5.00000000, 100000.00000000, 0, 0, 1, NOW(), NOW()),
  (10034, 'Binance', 'USDT', '₮', 510, 'Binance', 5.00000000, 100000.00000000, 0, 0, 1, NOW(), NOW());


-- Manual bank-transfer gateway for demo
INSERT INTO `forms` (`id`, `act`, `form_data`, `created_at`, `updated_at`) VALUES
  (9001, 'gateway_form_1001', '{"account_number":{"name":"Account Number","label":"account_number","is_required":"required","extensions":null,"options":[],"type":"text"},"account_holder":{"name":"Account Holder","label":"account_holder","is_required":"required","extensions":null,"options":[],"type":"text"}}', NOW(), NOW());

INSERT INTO `gateways` (`form_id`, `code`, `name`, `alias`, `image`, `status`, `gateway_parameters`, `supported_currencies`, `crypto`, `extra`, `description`, `created_at`, `updated_at`) VALUES
  (9001, 1001, 'Bank Transfer', 'bank-manual', NULL, 1, '{}', '{"USD":"USD"}', 0, NULL, 'Pay by bank transfer. Submit the transfer reference and the admin will confirm your payment.', NOW(), NOW());

-- Indexes for table `gateway_currencies`
--
ALTER TABLE `gateway_currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `general_settings`
--
ALTER TABLE `general_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `listings`
--
ALTER TABLE `listings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `listing_images`
--
ALTER TABLE `listing_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_logs`
--
ALTER TABLE `notification_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_templates`
--
ALTER TABLE `notification_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_attachments`
--
ALTER TABLE `support_attachments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_messages`
--
ALTER TABLE `support_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `update_logs`
--
ALTER TABLE `update_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`,`email`),
  ADD UNIQUE KEY `users_monoline_position_unique` (`monoline_position`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_ref_by_index` (`ref_by`);

--
-- Indexes for table `user_logins`
--
ALTER TABLE `user_logins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_plans`
--
ALTER TABLE `user_plans`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
--
-- AUTO_INCREMENT for table `ai_logs`
--
ALTER TABLE `ai_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_password_resets`
--
ALTER TABLE `admin_password_resets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `device_tokens`
--
ALTER TABLE `device_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `extensions`
--
ALTER TABLE `extensions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `favorite_listings`
--
ALTER TABLE `favorite_listings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `affiliate_logs`
--
ALTER TABLE `affiliate_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
ALTER TABLE `event_attendees`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `event_participations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT for table `event_categories`
--
ALTER TABLE `event_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `forms`
--
ALTER TABLE `forms`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `frontends`
--
ALTER TABLE `frontends`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `gateways`
--
ALTER TABLE `gateways`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `gateway_currencies`
--
ALTER TABLE `gateway_currencies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `general_settings`
--
ALTER TABLE `general_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `listings`
--
ALTER TABLE `listings`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `listing_images`
--
ALTER TABLE `listing_images`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_logs`
--
ALTER TABLE `notification_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_templates`
--
ALTER TABLE `notification_templates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_attachments`
--
ALTER TABLE `support_attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_messages`
--
ALTER TABLE `support_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `update_logs`
--
ALTER TABLE `update_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
--
-- AUTO_INCREMENT for table `cron_job_logs`
--
ALTER TABLE `cron_job_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cron_jobs`
--
ALTER TABLE `cron_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cron_schedules`
--
ALTER TABLE `cron_schedules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `held_commission_logs`
--
ALTER TABLE `held_commission_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pins`
--
ALTER TABLE `pins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_extras`
--
ALTER TABLE `user_extras`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_rankings`
--
ALTER TABLE `user_rankings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdraw_methods`
--
ALTER TABLE `withdraw_methods`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=901;

--
-- AUTO_INCREMENT for table `user_logins`
--
ALTER TABLE `user_logins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_plans`
--
ALTER TABLE `user_plans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `title`, `monthly_price`, `yearly_price`, `listings`, `amenities`, `images`, `videos`, `events`, `map`, `ref_com`, `level_com`, `commission_levels`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Free', 0.00000000, 0.00000000, 1, 1, 1, 0, 0, 0, 0, NULL, 1, 1, NULL, NULL),
(2, '', 0.00000000, 10.00000000, 5, 5, 5, 5, 5, 1, 5, '{"2":2}', 2, 1, NULL, NULL),
(3, '', 0.00000000, 20.00000000, 10, 10, 10, 10, 10, 1, 8, '{"2":4}', 2, 1, NULL, NULL),
(4, '', 0.00000000, 30.00000000, 15, 15, 15, 15, 15, 1, 10, '{"2":5}', 3, 1, NULL, NULL),
(5, '', 0.00000000, 40.00000000, 20, 20, 20, 20, 20, 1, 12, '{"2":6,"3":2}', 3, 1, NULL, NULL),
(6, '', 0.00000000, 50.00000000, 25, 25, 25, 25, 25, 1, 14, '{"2":7,"3":3}', 4, 1, NULL, NULL),
(7, '', 0.00000000, 60.00000000, 30, 30, 30, 30, 30, 1, 16, '{"2":8,"3":4,"4":1}', 4, 1, NULL, NULL),
(8, '', 0.00000000, 70.00000000, 35, 35, 35, 35, 35, 1, 18, '{"2":9,"3":5,"4":2}', 5, 1, NULL, NULL),
(9, '', 0.00000000, 80.00000000, 40, 40, 40, 40, 40, 1, 20, '{"2":10,"3":6,"4":3}', 5, 1, NULL, NULL),
(10, '', 0.00000000, 90.00000000, 45, 45, 45, 45, 45, 1, 22, '{"2":10,"3":7,"4":4,"5":2}', 6, 1, NULL, NULL),
(11, '', 0.00000000, 100.00000000, 50, 50, 50, 50, 50, 1, 25, '{"2":10,"3":8,"4":5,"5":3,"6":2}', 7, 1, NULL, NULL);

--
-- Dumping data for table `user_rankings`
--

INSERT INTO `user_rankings` (`id`, `level`, `name`, `icon`, `team_sales`, `direct_active`, `min_plan_amount`, `bonus`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Affiliate', NULL, 0.00000000, 0, 0.00000000, 0.00000000, 1, NULL, NULL),
(2, 2, 'Senior Affiliate', NULL, 0.00000000, 3, 10.00000000, 0.00000000, 1, NULL, NULL),
(3, 3, 'Team Leader', NULL, 0.00000000, 6, 20.00000000, 0.00000000, 1, NULL, NULL),
(4, 4, 'Senior Leader', NULL, 0.00000000, 12, 30.00000000, 0.00000000, 1, NULL, NULL),
(5, 5, 'Manager', NULL, 0.00000000, 24, 40.00000000, 0.00000000, 1, NULL, NULL),
(6, 6, 'Senior Manager', NULL, 0.00000000, 48, 50.00000000, 0.00000000, 1, NULL, NULL),
(7, 7, 'General Manager', NULL, 0.00000000, 96, 60.00000000, 0.00000000, 1, NULL, NULL),
(8, 8, 'Director', NULL, 0.00000000, 192, 70.00000000, 0.00000000, 1, NULL, NULL),
(9, 9, 'Senior Director', NULL, 0.00000000, 384, 80.00000000, 0.00000000, 1, NULL, NULL),
(10, 10, 'Crown Ambassador', NULL, 0.00000000, 768, 100.00000000, 0.00000000, 1, NULL, NULL);

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `status`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'United States', 1, 'united-states', NULL, '2024-06-29 04:14:39'),
(2, 'United Kingdom', 1, 'united-kingdom', NULL, '2024-06-29 04:14:39'),
(3, 'United Arab Emirates', 1, 'united-arab-emirates', NULL, '2024-06-29 04:14:39');

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `country_id`, `slug`, `name`, `image`, `status`, `is_featured`, `created_at`, `updated_at`) VALUES
(1, 1, 'new-york', 'New York', NULL, 1, 1, NULL, '2024-06-29 04:14:39'),
(2, 1, 'los-angeles', 'Los Angeles', NULL, 1, 0, NULL, '2024-06-29 04:14:39'),
(3, 2, 'london', 'London', NULL, 1, 1, NULL, '2024-06-29 04:14:39'),
(4, 3, 'dubai', 'Dubai', NULL, 1, 1, NULL, '2024-06-29 04:14:39');

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `image`, `slug`, `status`, `is_featured`, `created_at`, `updated_at`) VALUES
(1, 'Restaurants', NULL, 'restaurants', 1, 1, NULL, '2024-06-29 04:14:39'),
(2, 'Hotels', NULL, 'hotels', 1, 1, NULL, '2024-06-29 04:14:39'),
(3, 'Fitness', NULL, 'fitness', 1, 0, NULL, '2024-06-29 04:14:39'),
(4, 'Health & Medical', NULL, 'health-medical', 1, 0, NULL, '2024-06-29 04:14:39'),
(5, 'Professional Services', NULL, 'professional-services', 1, 0, NULL, '2024-06-29 04:14:39');

--
-- Dumping data for table `cron_schedules`
--

INSERT INTO `cron_schedules` (`id`, `name`, `interval`, `status`, `created_at`, `updated_at`) VALUES
(1, '5 Minutes', 300, 1, '2023-06-21 12:11:02', '2023-06-21 12:11:02'),
(2, '1 Hour', 3600, 1, '2023-06-21 12:11:10', '2023-06-21 12:11:10');

--
-- Dumping data for table `cron_jobs`
--

INSERT INTO `cron_jobs` (`id`, `name`, `alias`, `action`, `url`, `cron_schedule_id`, `next_run`, `last_run`, `is_running`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'Plan Expiry Sweep', 'plan_expiry_sweep', '["App\\\\Http\\\\Controllers\\\\CronController", "planExpirySweep"]', NULL, 2, '2020-01-01 10:45:01', '2020-01-01 10:40:01', 1, 1, NULL, '2024-06-29 04:40:01'),
(2, 'Rank Re-check', 'rank_recheck', '["App\\\\Http\\\\Controllers\\\\CronController", "cronRank"]', NULL, 1, '2020-01-01 10:45:02', '2020-01-01 10:35:02', 1, 1, NULL, '2024-06-29 04:35:02');

--
-- Dumping data for table `withdraw_methods`
--

INSERT INTO `withdraw_methods` (`id`, `form_id`, `name`, `image`, `min_limit`, `max_limit`, `fixed_charge`, `rate`, `percent_charge`, `currency`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 0, 'Bank Transfer', NULL, 10.00000000, 10000.00000000, 1.00000000, 1.00000000, 2.00, 'USD', 'Withdraw directly to your bank account', 1, NULL, '2024-06-29 04:14:39');

--
-- Dumping data for table `pins`
--

INSERT INTO `pins` (`id`, `user_id`, `generate_user_id`, `amount`, `pin`, `status`, `details`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 50.00000000, 'LEADX-INIT-50-0001', 0, 'Sample starter e-pin created at install', NULL, '2024-06-29 04:14:39');

--
-- Demo accounts (change or delete after first login)
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `email`, `password`, `status`, `ev`, `sv`, `profile_complete`, `ref_by`, `ref_source`, `country_code`, `mobile`, `dial_code`, `country_name`, `city`, `state`, `zip`, `address`, `balance`, `created_at`, `updated_at`) VALUES
(900, 'Demo', 'Member', 'demo', 'demo@leadxgpt.com', '$2y$12$PbDDkZXb7kj6fcn5EHKxJ.rwZC1Ho4yaX5LBJxn9oKmBDwlSZhCQO', 1, 1, 1, 1, 0, 0, 'US', '5550100000', '1', 'United States', 'New York', 'NY', '10001', '1 Demo Street', 250.00000000, '2024-06-29 04:14:39', '2024-06-29 04:14:39');

INSERT INTO `user_extras` (`id`, `user_id`, `direct_active`, `direct_total`, `team_sales`, `total_team_sales`, `held_commission`, `created_at`, `updated_at`) VALUES
(1, 900, 0, 0, 0.00000000, 0.00000000, 0.00000000, NULL, '2024-06-29 04:14:39');

INSERT INTO `user_plans` (`id`, `user_id`, `plan_id`, `plan_price`, `listings`, `amenities`, `images`, `videos`, `events`, `allow_map`, `expired_on`, `status`, `created_at`, `updated_at`) VALUES
(1, 900, 4, 199.00000000, 15, 15, 15, 15, 15, 1, '2030-01-01', 1, NULL, '2024-06-29 04:14:39');

INSERT INTO `transactions` (`id`, `user_id`, `amount`, `charge`, `post_balance`, `trx_type`, `trx`, `details`, `remark`, `created_at`, `updated_at`) VALUES
(1, 900, 449.00000000, 0.00000000, 449.00000000, '+', 'DEMO-FUND', 'Demo wallet funding (sample data)', 'balance_add', NULL, '2024-06-29 04:14:39'),
(2, 900, 199.00000000, 0.00000000, 250.00000000, '-', 'DEMO-SETUP', 'Demo Standard Package (sample data)', 'purchase_plan', NULL, '2024-06-29 04:14:39');

-- demo listings ship without map embeds: the map column carries an embed URL
-- (listing base semantics); '1' was an entitlement flag and renders a blank iframe
UPDATE `listings` SET `map` = '' WHERE `map` = '1';

--
-- Demo legal pages content (rendered at /policy/{slug} and linked in the footer)
--

INSERT INTO `frontends` (`id`, `data_keys`, `data_values`, `seo_content`, `tempname`, `slug`, `created_at`, `updated_at`) VALUES
(101, 'policy_pages.element', '{\"title\":\"Terms & Conditions\",\"details\":\"<p>LEADXGPT is an online business directory where local businesses publish listings and members grow their reach through our referral program. These terms govern your use of the platform, including the website, member accounts, listings and the referral program.</p><p>By creating an account or using any part of the platform you accept these terms. If you do not agree with any part of them, please stop using the platform.</p><h3>Eligibility and accounts</h3><p>You must be at least 18 years old to purchase a plan or take part in the referral program. The information you give at registration, including your name, email and mobile number, must be accurate and kept up to date.</p><p>You are responsible for keeping your password safe and for all activity that happens under your account. Tell us immediately if you suspect unauthorised use.</p><h3>Listings</h3><p>Business owners remain responsible for the content they publish in their listings, including photos, prices, opening hours and claims made about their services. LEADXGPT provides the directory platform and may review, edit, hide or remove any listing that breaks our listing rules or the law.</p><h3>Plans and the referral program</h3><p>Paid plans unlock listing capacity and directory features. Plan prices are shown before purchase and plan entitlements apply to a single account only.</p><p>The referral program lets members share a personal referral link. When a referred member completes a genuine plan purchase, commissions are credited as described in the published plan terms. Commissions are paid only on real product sales.</p><h3>Member conduct</h3><p>Members must not send spam or bulk unsolicited messages, must not make false income claims, and must not present the platform as an investment scheme, a job offer or a guaranteed earnings opportunity. Members must not create multiple accounts to self refer or otherwise manipulate the commission system.</p><p>Content that is unlawful, infringing, defamatory or harmful is prohibited anywhere on the platform.</p><h3>Suspension and termination</h3><p>We may warn, suspend or close accounts that break these rules. Where practical we will tell you why, and you can contact support to appeal.</p><h3>Changes and contact</h3><p>We may update these terms from time to time. Material changes are announced on the platform and continued use after an update means you accept the revised terms.</p><p>Questions about these terms can be sent through the contact page or your member support desk.</p>\"}', NULL, 'basic', 'terms', '2024-06-29 04:14:39', '2024-06-29 04:14:39'),
(102, 'policy_pages.element', '{\"title\":\"Privacy Policy\",\"details\":\"<p>This policy explains what personal data LEADXGPT collects, why we collect it, and the rights you have over your data. It reflects the principles of purpose limitation, data minimisation and accountability followed in the Digital Personal Data Protection Act, 2023.</p><h3>Data we collect</h3><p>When you register we collect your name, email address and mobile number. When you publish a listing we collect the business content you submit. When you buy a plan or receive a payout we keep the transaction records required for accounting, and we maintain basic security logs.</p><p>When a member joins through a referral link we record the referral relationship so that commissions can be attributed correctly.</p><h3>Purpose limitation</h3><p>We use personal data only to run your account, publish and maintain listings, process payments and commissions, provide support, prevent abuse and meet legal obligations. We do not sell personal data and we do not use it for unrelated purposes without a fresh legal basis.</p><h3>Sharing and retention</h3><p>Data is shared only with payment gateways, communication service providers and authorities where the law requires it. Every processor works under confidentiality obligations.</p><p>Records are kept while your account is active and for the statutory periods that apply to transaction data, after which they are deleted or anonymised.</p><h3>Your rights</h3><p>You can ask for a copy of your data, ask us to correct it, ask us to erase it, or withdraw consent that you previously gave. These rights are available subject to the retention duties that law imposes on transaction records.</p><p>You can also manage much of your data yourself from the profile section of your account.</p><h3>Security</h3><p>Data is transmitted over encrypted connections and stored under access controls with restricted administrative access. No system is perfectly secure, so please keep your password confidential.</p><h3>Grievance escalation</h3><p>For any privacy question, first write to our grievance officer through the contact page with the subject line privacy request. We acknowledge requests within 72 hours and respond within 30 days.</p><p>If a request is not resolved to your satisfaction within 30 days, you may escalate it to the Data Protection Board of India as provided under the Digital Personal Data Protection Act, 2023.</p><p>Material changes to this policy are posted on this page with an updated effective date.</p>\"}', NULL, 'basic', 'privacy', '2024-06-29 04:14:39', '2024-06-29 04:14:39'),
(103, 'policy_pages.element', '{\"title\":\"Refund Policy\",\"details\":\"<p>This policy covers membership plan purchases and wallet payments made on LEADXGPT. It explains when a refund is possible and how the review process works.</p><h3>The 7 day refund window</h3><p>Duplicate payments, failed payments where your bank or card was debited, and accidental double charges are refundable when reported within 7 days of the transaction. Requests made after 7 days are reviewed at our discretion.</p><h3>Manual review</h3><p>Every request is checked manually against gateway and bank records before approval. This protects members from fraudulent claims and keeps commission accounting accurate.</p><h3>How to request</h3><p>Send a refund request through the contact page or your member support desk with the transaction reference, date, amount and reason. A payment screenshot speeds up verification.</p><h3>Processing time</h3><p>Approved refunds are initiated within 7 working days. Settlement by your bank or card network can add a few additional days depending on the payment method.</p><p>If a payment failed but your bank is still holding the amount, we coordinate with the gateway to release it. Most banks auto reverse such holds within 5 to 7 working days.</p><h3>What is not refundable</h3><p>Commissions already credited to a member wallet are not refundable. Plan fees are not refundable outside the duplicate and failed payment grounds once listing entitlements have been substantially used. Amounts already withdrawn through payouts are also outside this policy.</p><h3>Chargebacks</h3><p>Please contact us before opening a card dispute. Direct refund requests resolve faster, and disputes opened without contacting support first may require account review.</p><h3>Changes</h3><p>We may update this policy from time to time. The version in effect on the date of your transaction applies to that transaction.</p><p>Refund questions can be sent through the contact page with your transaction details attached.</p>\"}', NULL, 'basic', 'refund', '2024-06-29 04:14:39', '2024-06-29 04:14:39'),
(104, 'policy_pages.element', '{\"title\":\"Earnings Disclaimer\",\"details\":\"<p>LEADXGPT does not promise, guarantee or project any income from using the platform, from membership plans or from the referral program. Any statement to the contrary is not authorised by us.</p><h3>What commissions are</h3><p>Commissions are marketing rewards paid on genuine product sales, meaning real membership plan purchases completed by referred members. They are consideration for promotion, not returns on money deposited.</p><p>No commission arises from activity that does not involve a real product sale. Self referrals, fake accounts, cycling of funds and manipulated payments earn nothing, and such credits are reversed when detected.</p><h3>Renewals</h3><p>Plan renewals carry zero percent commission. Commission is computed only on first time qualifying purchases, as set out in the published plan terms.</p><h3>Examples are illustrations</h3><p>Figures shown on the website or shared by members are examples, not forecasts. Actual results depend entirely on your own effort, the demand in your market and the quality of the businesses you work with.</p><h3>Business risk</h3><p>Promoting the platform is business activity and every business activity carries risk. The referral program is not an investment, a deposit scheme or a salaried job, and money paid for plans buys listing services, not returns.</p><h3>No referral fees</h3><p>We never ask members to pay for the right to refer others or to earn without a product. If anyone solicits such payments in our name, report it to support immediately.</p><h3>Representing the platform</h3><p>Members must not publish false income claims, edited payment screenshots or misleading promises when sharing their referral link. Violations lead to suspension and forfeiture of pending commissions.</p><p>Commission income may be taxable where you live. You are responsible for your own tax reporting and compliance.</p><h3>Questions</h3><p>If anything on this page is unclear, contact us through the contact page before you start promoting the platform.</p>\"}', NULL, 'basic', 'earnings-disclaimer', '2024-06-29 04:14:39', '2024-06-29 04:14:39');

--
-- Demo listings (sample directory data, all owned by the demo member account)
--

INSERT INTO `listings` (`id`, `listing_id`, `title`, `description`, `user_id`, `category_id`, `city_id`, `country_id`, `display_image`, `location`, `tags`, `open_time`, `close_time`, `day_off`, `amenities`, `map`, `videos`, `is_featured`, `rejected_reason`, `status`, `created_at`, `updated_at`, `ai_review_summary`) VALUES
(1,  'LST-DEMO-001',  'QuickFlow Plumbing and Drain Works',  'Licensed plumbing crew handling burst pipes, blocked drains, bathroom refits and water tank cleaning across Brooklyn. Round the clock emergency response with upfront pricing and a 12 month workmanship warranty.',  900,  5,  1,  1,  NULL, '142B Bedford Avenue, Brooklyn',  '[\"plumber\",\"emergency plumbing\",\"drain cleaning\",\"pipe repair\"]',  '06:00',  '22:00',  NULL,  '[{\"name\":\"Emergency Service\",\"value\":\"24 hours, 7 days\"},{\"name\":\"Workmanship Warranty\",\"value\":\"12 months\"},{\"name\":\"Free Inspection\",\"value\":\"With every repair\"}]',  NULL,  NULL,  1,  NULL,  1,  '2024-06-29 04:14:39',  '2024-06-29 04:14:39',  NULL),
(2,  'LST-DEMO-002',  'Metro Glow Salon and Spa',  'Full service salon for hair styling, colour, skin care and bridal packages in the heart of Los Angeles. Certified stylists, sulphate free products and private spa rooms for relaxing sessions.',  900,  3,  2,  1,  NULL, '88 Sunset Boulevard',  '[\"salon\",\"hair styling\",\"spa\",\"bridal makeup\"]',  '09:00',  '20:00',  '[\"monday\"]',  '[{\"name\":\"Certified Stylists\",\"value\":\"Salon trained\"},{\"name\":\"Organic Products\",\"value\":\"Sulphate free\"},{\"name\":\"Private Rooms\",\"value\":\"Available\"}]',  NULL,  NULL,  0,  NULL,  1,  '2024-06-29 04:14:39',  '2024-06-29 04:14:39',  NULL),
(3,  'LST-DEMO-003',  'Apex Learning Centre',  'Coaching classes for grades 8 to 12 in mathematics, science and English, plus board and entrance exam preparation. Small batches, weekly parent reports and daily doubt clearing sessions.',  900,  5,  1,  1,  NULL, '221 Madison Avenue, Midtown',  '[\"coaching classes\",\"test preparation\",\"tutoring\",\"entrance exams\"]',  '08:00',  '20:00',  NULL,  '[{\"name\":\"Batch Size\",\"value\":\"Max 15 students\"},{\"name\":\"Study Material\",\"value\":\"Included\"},{\"name\":\"Doubt Sessions\",\"value\":\"Daily\"}]',  NULL,  NULL,  0,  NULL,  1,  '2024-06-29 04:14:39',  '2024-06-29 04:14:39',  NULL),
(4,  'LST-DEMO-004',  'Spice Route Caterers',  'Wedding and corporate catering with North Indian, continental and live counter menus for 50 to 2000 guests. Menu tastings are free before booking, and staff, crockery and setup come included.',  900,  1,  3,  2,  NULL,  '56 Brick Lane, East London',  '[\"catering\",\"wedding food\",\"corporate events\",\"party catering\"]',  '08:00',  '21:00',  NULL,  '[{\"name\":\"Menu Tasting\",\"value\":\"Free before booking\"},{\"name\":\"Live Counters\",\"value\":\"Available\"},{\"name\":\"Service Staff\",\"value\":\"Included\"}]',  NULL,  NULL,  0,  NULL,  1,  '2024-06-29 04:14:39',  '2024-06-29 04:14:39',  NULL),
(5,  'LST-DEMO-005',  'Horizon Holidays Travel Desk',  'IATA accredited travel agency designing honeymoon, family and corporate travel packages across Asia, Europe and the Gulf. Visa assistance, flight bookings and 24 hour on trip support included with every package.',  900,  2,  4,  3,  NULL, '12 Al Rigga Road, Deira',  '[\"travel agency\",\"holiday packages\",\"visa assistance\",\"honeymoon trips\"]',  '09:00',  '19:00',  '[\"sunday\"]',  '[{\"name\":\"IATA Accredited\",\"value\":\"Yes\"},{\"name\":\"Visa Assistance\",\"value\":\"Included\"},{\"name\":\"On Trip Support\",\"value\":\"24 hours\"}]',  NULL,  NULL,  1,  NULL,  1,  '2024-06-29 04:14:39',  '2024-06-29 04:14:39',  NULL),
(6,  'LST-DEMO-006',  'Circuit House Electronics',  'Authorised dealer for leading television, laptop and home appliance brands with EMI options and exchange offers. In house service centre and free delivery within 15 miles of the store.',  900,  5,  2,  1,  NULL, '450 Fairfax Avenue',  '[\"electronics store\",\"televisions\",\"laptops\",\"home appliances\"]',  '10:00',  '21:00',  NULL,  '[{\"name\":\"Authorised Dealer\",\"value\":\"Yes\"},{\"name\":\"EMI Options\",\"value\":\"Available\"},{\"name\":\"Service Centre\",\"value\":\"In house\"}]',  NULL,  NULL,  0,  NULL,  1,  '2024-06-29 04:14:39',  '2024-06-29 04:14:39',  NULL),
(7,  'LST-DEMO-007',  'Sweet Crumb Artisan Bakery',  'Small batch sourdough, croissants, celebration cakes and eggless bakes baked fresh every morning. Custom cake orders need 48 hours notice and breakfast combos start at 6 dollars.',  900,  1,  1,  1,  NULL, '78 Bleecker Street',  '[\"bakery\",\"fresh bread\",\"custom cakes\",\"eggless bakes\"]',  '07:00',  '20:00',  NULL,  '[{\"name\":\"Fresh Daily\",\"value\":\"Baked every morning\"},{\"name\":\"Eggless Range\",\"value\":\"Available\"},{\"name\":\"Custom Cakes\",\"value\":\"48 hour notice\"}]',  NULL,  NULL,  1,  NULL,  1,  '2024-06-29 04:14:39',  '2024-06-29 04:14:39',  NULL),
(8,  'LST-DEMO-008',  'Desert Drive Motor Training',  'RTA certified driving school offering car and light vehicle training with male and female instructors. Flexible lesson slots, mock tests and home pickup anywhere in Dubai.',  900,  5,  4,  3,  NULL, '5 Al Wasl Road, Jumeirah',  '[\"driving school\",\"car lessons\",\"rta test\",\"defensive driving\"]',  '06:00',  '21:00',  NULL,  '[{\"name\":\"Certified Instructors\",\"value\":\"RTA approved\"},{\"name\":\"Home Pickup\",\"value\":\"Anywhere in Dubai\"},{\"name\":\"Mock Tests\",\"value\":\"Included\"}]',  NULL,  NULL,  0,  NULL,  1,  '2024-06-29 04:14:39',  '2024-06-29 04:14:39',  NULL),
(9,  'LST-DEMO-009',  'BlueLine Plumbing Services',  'Gas Safe registered plumbers covering boiler repair, central heating, leak detection and bathroom installations across Greater London. Same day appointments and fixed written quotes.',  900,  5,  3,  2,  NULL, '19 Croydon Road, Croydon',  '[\"plumber\",\"boiler repair\",\"leak detection\",\"gas safe\"]',  '07:00',  '19:00',  '[\"sunday\"]',  '[{\"name\":\"Gas Safe Registered\",\"value\":\"Yes\"},{\"name\":\"Same Day Visits\",\"value\":\"Subject to slots\"},{\"name\":\"Fixed Quotes\",\"value\":\"Written estimate\"}]',  NULL,  NULL,  0,  NULL,  1,  '2024-06-29 04:14:39',  '2024-06-29 04:14:39',  NULL),
(10,  'LST-DEMO-010',  'Glow Room Ladies Salon',  'Private ladies only salon for hair, skin, bridal styling and nail care with imported products and single use kits. Home service available in Jumeirah, Marina and Downtown Dubai.',  900,  3,  4,  3,  NULL, '31 Jumeirah Beach Road',  '[\"ladies salon\",\"bridal styling\",\"nail care\",\"home service\"]',  '10:00',  '21:00',  '[\"friday\"]',  '[{\"name\":\"Ladies Only\",\"value\":\"Private cabins\"},{\"name\":\"Home Service\",\"value\":\"Selected areas\"},{\"name\":\"Imported Products\",\"value\":\"Single use kits\"}]',  NULL,  NULL,  0,  NULL,  1,  '2024-06-29 04:14:39',  '2024-06-29 04:14:39',  NULL),
(11,  'LST-DEMO-011',  'Summit Coaching Academy',  'SAT, ACT and AP coaching for high school students with full length mock exams every month. One on one mentor mapping for each student and a verified record of score improvement.',  900,  5,  2,  1,  NULL, '612 Wilshire Boulevard',  '[\"sat coaching\",\"act prep\",\"ap classes\",\"test prep\"]',  '09:00',  '19:00',  '[\"sunday\"]',  '[{\"name\":\"Mock Exams\",\"value\":\"Every month\"},{\"name\":\"Mentor Support\",\"value\":\"One on one\"},{\"name\":\"Score Reports\",\"value\":\"Detailed analytics\"}]',  NULL,  NULL,  0,  NULL,  1,  '2024-06-29 04:14:39',  '2024-06-29 04:14:39',  NULL),
(12,  'LST-DEMO-012',  'Royal Feast Catering Company',  'Full service catering for weddings, office parties and cultural events with Mexican, Italian and fusion menus. Licensed kitchen, uniformed service staff and custom menu design for every budget.',  900,  1,  2,  1,  NULL, '770 Olympic Boulevard',  '[\"caterer\",\"wedding catering\",\"office parties\",\"menu design\"]',  '08:00',  '22:00',  NULL,  '[{\"name\":\"Licensed Kitchen\",\"value\":\"County certified\"},{\"name\":\"Service Staff\",\"value\":\"Uniformed team\"},{\"name\":\"Custom Menus\",\"value\":\"Budget friendly\"}]',  NULL,  NULL,  1,  NULL,  1,  '2024-06-29 04:14:39',  '2024-06-29 04:14:39',  NULL),
(13,  'LST-DEMO-013',  'StarLine Travels',  'Family run travel agency in Midtown arranging flight deals, cruise holidays, group tours and travel insurance since 2009. Authorised agent for major airlines with zero convenience fee on booked packages.',  900,  2,  1,  1,  NULL, '305 Fifth Avenue, Midtown',  '[\"travel agency\",\"flight booking\",\"cruise holidays\",\"group tours\"]',  '09:30',  '18:30',  '[\"sunday\"]',  '[{\"name\":\"Authorised Agent\",\"value\":\"Major airlines\"},{\"name\":\"Zero Fee\",\"value\":\"On package bookings\"},{\"name\":\"Travel Insurance\",\"value\":\"Available\"}]',  NULL,  NULL,  0,  NULL,  1,  '2024-06-29 04:14:39',  '2024-06-29 04:14:39',  NULL),
(14,  'LST-DEMO-014',  'TechNova Electronics Mart',  'Two floor electronics superstore stocking smartphones, gaming gear, cameras and smart home devices with a price match promise. 14 day return policy and an on site repair kiosk.',  900,  5,  3,  2,  NULL, '88 Oxford Street',  '[\"electronics shop\",\"smartphones\",\"gaming\",\"smart home\"]',  '10:00',  '20:00',  NULL,  '[{\"name\":\"Price Match\",\"value\":\"Promise\"},{\"name\":\"Return Policy\",\"value\":\"14 days\"},{\"name\":\"Repair Kiosk\",\"value\":\"On site\"}]',  NULL,  NULL,  1,  NULL,  1,  '2024-06-29 04:14:39',  '2024-06-29 04:14:39',  NULL);

--
-- Demo leads (sample inbox for the demo member account)
--

INSERT INTO `leads` (`id`, `listing_id`, `user_id`, `name`, `email`, `phone`, `message`, `match_result`, `status`, `source`, `created_at`, `updated_at`) VALUES
(1, 1, 900, 'Rachel Simmons', 'rachel.simmons@example.com', '+1 555 201 3345', 'Kitchen sink is backing up every evening and I need an emergency plumber this week in Brooklyn. Please call after 6 pm.', NULL, 'new', 'public', '2024-06-29 04:14:39', '2024-06-29 04:14:39'),
(2, 2, 900, 'Dana White', 'dana.white@example.com', '+1 555 118 7420', 'Looking for bridal hair and makeup for a wedding in October, plus a trial session for four people.', NULL, 'contacted', 'public', '2024-06-29 04:14:39', '2024-06-29 04:14:39'),
(3, 5, 900, 'Omar Al Rashid', 'omar.rashid@example.com', '+971 55 402 8891', 'Family of five planning a 7 day Europe trip in December with visa help. Shared our budget by email and awaiting the final itinerary quote.', NULL, 'closed', 'public', '2024-06-29 04:14:39', '2024-06-29 04:14:39'),
(4, 7, 900, 'Priya Nair', 'priya.nair@example.com', '+1 555 902 6673', 'Need a three tier eggless cake for an anniversary on short notice. I can pick up from the store.', NULL, 'new', 'public', '2024-06-29 04:14:39', '2024-06-29 04:14:39'),
(5, 12, 900, 'Marcus Reed', 'marcus.reed@example.com', '+1 555 337 1508', 'Office party for 80 people next month, need Mexican and vegetarian options with service staff. Requested a detailed quote.', NULL, 'contacted', 'public', '2024-06-29 04:14:39', '2024-06-29 04:14:39'),
(6, 8, 900, 'Fatima Hussain', 'fatima.hussain@example.com', '+971 55 733 2204', 'Beginner car lessons with home pickup in Marina, weekend slots preferred. Booking confirmed and classes scheduled.', NULL, 'closed', 'public', '2024-06-29 04:14:39', '2024-06-29 04:14:39');
