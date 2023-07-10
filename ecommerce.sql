-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 10, 2023 at 10:08 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'Clothing', 0, '2023-07-10 13:35:18', '2023-07-10 13:35:18'),
(2, 'Men\'s Clothing', 1, '2023-07-10 13:36:02', '2023-07-10 13:36:02'),
(3, 'Women\'s Clothing', 1, '2023-07-10 13:36:07', '2023-07-10 13:36:07'),
(4, 'Men\'s Tops', 2, '2023-07-10 13:36:21', '2023-07-10 13:36:21'),
(5, 'Men\'s Bottoms', 2, '2023-07-10 13:36:28', '2023-07-10 13:36:28'),
(6, 'Women\'s Tops', 3, '2023-07-10 13:36:41', '2023-07-10 13:36:41'),
(7, 'Women\'s Bottoms', 3, '2023-07-10 13:36:47', '2023-07-10 13:36:47'),
(8, 'Electronics', 0, '2023-07-10 14:18:24', '2023-07-10 14:18:24'),
(9, 'Computers', 8, '2023-07-10 14:19:53', '2023-07-10 14:19:53'),
(10, 'Mobile Phones', 8, '2023-07-10 14:20:07', '2023-07-10 14:20:07'),
(11, 'Laptops', 9, '2023-07-10 14:21:23', '2023-07-10 14:21:23'),
(12, 'Desktops', 9, '2023-07-10 14:21:33', '2023-07-10 14:21:33'),
(13, 'Tablets', 10, '2023-07-10 14:21:51', '2023-07-10 14:21:51'),
(14, 'Smartphones', 10, '2023-07-10 14:21:59', '2023-07-10 14:37:51');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(4, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(5, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(6, '2016_06_01_000004_create_oauth_clients_table', 1),
(7, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(8, '2019_08_19_000000_create_failed_jobs_table', 1),
(9, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(10, '2023_07_02_144532_create_temps_table', 1),
(11, '2023_07_10_173711_create_categories_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_access_tokens`
--

INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('0a0574b20d3bcd90eb2a31d24e7b44f90ba046b6c507cbff4205cc6c81fb114a440106a1ff4f0641', 5, 1, 'Auth token', '[]', 1, '2023-07-06 09:22:24', '2023-07-10 13:10:21', '2024-07-06 14:52:24'),
('14b9b7e7a7186b281b6f799b5d0a0e762dda5f016127eeb2702af0e697b18b9c943110d9ba88e292', 2, 1, 'Auth token', '[]', 0, '2023-07-10 13:35:08', '2023-07-10 13:35:09', '2024-07-10 19:05:08'),
('207a99b86f3a3583a7bd969ee0f63526010f85b2e036ef9b5364dee6808e6504b47e786b0e7598b5', 2, 1, 'Auth token', '[]', 0, '2023-07-03 02:27:39', '2023-07-03 02:27:39', '2024-07-03 07:57:39'),
('23be9e5c85b68f5c7c3c6d76a60fa70f2fe060474c1914a82152295e1358aa4776ab92f4ac058527', 2, 1, 'Auth token', '[]', 0, '2023-07-03 06:30:05', '2023-07-03 06:30:05', '2024-07-03 12:00:05'),
('24b52ae60f85a55d5ec2a41a621f1a618a065b54af82b40d872648e1ab027a75b0658e76a0d969fc', 2, 1, 'Auth token', '[]', 0, '2023-07-03 04:17:23', '2023-07-03 04:17:23', '2024-07-03 09:47:23'),
('2c3a31b3f43ac3e59ec31da6d43fc135238e7e668fdb0da8513b36fba3326a8ab362bfca71a98969', 2, 1, 'Auth token', '[]', 0, '2023-07-03 04:10:19', '2023-07-03 04:10:19', '2024-07-03 09:40:19'),
('3848c542080df6552d7e3dfce9d9d9d5443cc6ece59acb9e99a0ea0ab5e0c0c974e8e72c836b27b2', 2, 1, 'Auth token', '[]', 0, '2023-07-03 04:11:28', '2023-07-03 04:11:28', '2024-07-03 09:41:28'),
('3a03d9c3ba8889ac580127287cce9f6388837c5e382336cb40a700c8d3ae24d363f43dfdf119bf95', 2, 1, 'Auth token', '[]', 0, '2023-07-03 03:55:47', '2023-07-03 03:55:47', '2024-07-03 09:25:47'),
('3db9942bf726c7c655fe53404660dd46b7435ae2fecdcd6ee8f39f8f4ef72e08ab069d6fab3eb55a', 11, 1, 'Auth token', '[]', 0, '2023-07-06 09:21:01', '2023-07-06 09:21:01', '2024-07-06 14:51:01'),
('403b625e5495ef58f2d41abbad6e38894f3795000dcf8e34146cef7b56554178218838b346616e3f', 5, 1, 'Auth token', '[]', 0, '2023-07-03 06:35:54', '2023-07-03 06:35:54', '2024-07-03 12:05:54'),
('64b6a7c981299203c16139ccd6884c5b2fc81d08ac9430840ec0049a5a9ec720a1972ffa2bbf7ee6', 2, 1, 'Auth token', '[]', 0, '2023-07-03 03:55:20', '2023-07-03 03:55:20', '2024-07-03 09:25:20'),
('698bc9f4fa39ff4384600e7c96c9067aab0432b02bcad756990d3382f16b81c9cc15ab7923476b6a', 2, 1, 'Auth token', '[]', 0, '2023-07-03 03:55:54', '2023-07-03 03:55:54', '2024-07-03 09:25:54'),
('6f47b66765648c0cef8d580121b56c83787657c2f88269e279e8218da0b81c77d9bcc2897c7e6c75', 2, 1, 'Auth token', '[]', 0, '2023-07-03 05:49:36', '2023-07-03 05:49:36', '2024-07-03 11:19:36'),
('7163e83d0a405cc30ec5f6fff2f22ea9db196beed2928a082646d508f16f6ad8c23d18f66c95acc7', 2, 1, 'Auth token', '[]', 1, '2023-07-03 07:54:07', '2023-07-03 07:59:06', '2024-07-03 13:24:07'),
('72a302876ba0721c7ef9cf2b989317aeb9067b7c713f87645a89e4ff87a086a24f7d10dc435728bc', 2, 1, 'Auth token', '[]', 0, '2023-07-03 04:09:14', '2023-07-03 04:09:14', '2024-07-03 09:39:14'),
('818b6107a19e990b08872d95ed0ed5704fce5bcadb984b95c9a2cdeb53a4971e5027d2093a0d6c61', 2, 1, 'Auth token', '[]', 0, '2023-07-03 03:52:31', '2023-07-03 03:52:31', '2024-07-03 09:22:31'),
('ab3bc56996eb07b995a95c3c8d160d50642602d788835b470d043e7093986a7cd473e0d2535f483d', 5, 1, 'Auth token', '[]', 0, '2023-07-03 06:38:21', '2023-07-03 06:38:21', '2024-07-03 12:08:21'),
('ad7494f3d7ba46740bcac05828fb4a358c38fdab416b3c2e67fe35c2d0fe344278750217eb5f745c', 2, 1, 'Auth token', '[]', 0, '2023-07-03 05:49:55', '2023-07-03 05:49:55', '2024-07-03 11:19:55'),
('b1d91982cab40763bc3c7cc967ea9555810e6f2c29489d79e1a207027dd39c00cc48c3a0912ed433', 2, 1, 'Auth token', '[]', 0, '2023-07-06 09:17:10', '2023-07-06 09:17:10', '2024-07-06 14:47:10'),
('b3f51ba60515b5197274472cdafe830fd79229f82b1f077668c32995b20b4e0da9498ca92a557b79', 2, 1, 'Auth token', '[]', 0, '2023-07-03 07:14:43', '2023-07-03 07:14:43', '2024-07-03 12:44:43'),
('c640dce87c1d7443ccca9d732bf794f3634b6e263ad2a51dab3fb9e92eb005dc59994db67100eac3', 2, 1, 'Auth token', '[]', 0, '2023-07-03 03:55:25', '2023-07-03 03:55:25', '2024-07-03 09:25:25'),
('df0f5f075fe62d846e1eaf966b260c9404e61928d9d6281dada6d77801b679759a13e06da40389b9', 9, 1, 'Auth token', '[]', 0, '2023-07-03 06:40:43', '2023-07-03 06:40:43', '2024-07-03 12:10:43'),
('f2450188b7b7b60cff421af2102ee0ca3bef4c758ed1271de6ede368e9707e67af2d93eeabaeed10', 10, 1, 'Auth token', '[]', 0, '2023-07-06 09:09:41', '2023-07-06 09:09:42', '2024-07-06 14:39:41'),
('fdad5138c92d49180ef61b72e32fa793c9009c2e06f57595d49bce99eca4b93612bcb8ad7cec092d', 2, 1, 'Auth token', '[]', 0, '2023-07-03 05:52:01', '2023-07-03 05:52:01', '2024-07-03 11:22:01');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `secret` varchar(100) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `redirect` text NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_clients`
--

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Ecommerce Personal Access Client', 'f4VW1YGEG61UGN5m3aKyBbOcr0eHklDnLCfT8rxD', NULL, 'http://localhost', 1, 0, 0, '2023-07-03 02:27:09', '2023-07-03 02:27:09');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2023-07-03 02:27:09', '2023-07-03 02:27:09');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) NOT NULL,
  `access_token_id` varchar(100) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temps`
--

CREATE TABLE `temps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `temps`
--

INSERT INTO `temps` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(9, '+919727308286', '7600', '2023-07-03 06:30:30', '2023-07-03 06:30:30'),
(11, 'ankur.gurutechnolabs1@gmail.com', '5029', '2023-07-03 06:40:15', '2023-07-03 07:47:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_no` varchar(255) DEFAULT NULL,
  `user_type` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT '1',
  `email_verified` varchar(255) NOT NULL DEFAULT '0',
  `phone_verified` varchar(255) NOT NULL DEFAULT '0',
  `otp_verified` varchar(255) NOT NULL DEFAULT '0',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone_no`, `user_type`, `status`, `email_verified`, `phone_verified`, `otp_verified`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'data', 'admin@gmail.com', '+911234567890', 'admin', '1', '1', '1', '1', NULL, '$2y$10$3azzi/GvHtGWf2P3BaUm4eFMgw8YKa5keQR2keFKBphe4vrbh9is.', NULL, '2023-07-03 00:51:31', '2023-07-03 00:51:31'),
(2, 'ankur', 'thakrar', 'ankur.gurutechnolabs@gmail.com', '+919727308286', 'user', '1', '0', '0', '0', NULL, '$2y$10$sFnKLGeyeqosktmV3NUOPOpxNW.s91KtAYC305OQ0VvmRFg5oha6S', NULL, '2023-07-03 02:27:39', '2023-07-06 09:15:03'),
(5, NULL, NULL, NULL, '+919727308287', 'user', '1', '0', '0', '0', NULL, '$2y$10$ThN14BveWYPlTVHBDXlnJOymx2mxfNCrhpYjdVe0oCrWdQs92Q9vy', NULL, '2023-07-03 06:35:54', '2023-07-03 06:35:54'),
(9, NULL, NULL, 'ankur.gurutechnolabs1@gmail.com', NULL, 'user', '1', '0', '0', '0', NULL, '$2y$10$Slt3G322CS82whUFwxL0T.pObA6A187v.O93q.AgD3wJUj0KzHVg6', NULL, '2023-07-03 06:40:43', '2023-07-03 06:40:43'),
(10, 'ankur', 'thakrar', 'ankur.gurutechnolabs12@gmail.com', '+919727308288', 'user', '1', '0', '0', '0', NULL, '$2y$10$lsd6aeXN69S0.uoJYlctrudArhk6Lm8f23Z9Q8NddfhyZr8alX3qS', NULL, '2023-07-06 09:09:38', '2023-07-06 09:09:38'),
(11, NULL, NULL, NULL, '+919727308280', 'user', '1', '0', '0', '0', NULL, '$2y$10$y5YpNZHo3eqR43wLQbKtDeIOpwgxUwLCJjdH2BscUW7VtTrv4C4Mi', NULL, '2023-07-06 09:21:01', '2023-07-06 09:21:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_auth_codes_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `temps`
--
ALTER TABLE `temps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_no_unique` (`phone_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `temps`
--
ALTER TABLE `temps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
