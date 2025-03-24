-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 24, 2025 at 05:42 PM
-- Server version: 5.5.23
-- PHP Version: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel_test_1_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `books_list`
--

CREATE TABLE `books_list` (
  `book_id` bigint(20) UNSIGNED NOT NULL,
  `author_full_name` varchar(50) NOT NULL,
  `book_title` varchar(255) NOT NULL,
  `book_year` int(4) NOT NULL,
  `genre_title` varchar(20) NOT NULL,
  `book_cover` varchar(255) NOT NULL,
  `book_pages_count` int(11) UNSIGNED NOT NULL,
  `book_title_hash` varchar(32) NOT NULL,
  `isDel_INT` tinyint(1) UNSIGNED DEFAULT '0',
  `book_author_hash` varchar(32) DEFAULT NULL,
  `is_delete` tinyint(1) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `books_list`
--

INSERT INTO `books_list` (`book_id`, `author_full_name`, `book_title`, `book_year`, `genre_title`, `book_cover`, `book_pages_count`, `book_title_hash`, `isDel_INT`, `book_author_hash`, `is_delete`) VALUES
(18, 'Дэвид Эллис', 'Дом Лажи', 2022, 'Детективы', 'ee9b638b1e391c43d402a30a86da9520.jpg', 426, '63e45585e7403caca9d7904f9526fcb9', 0, '3bc12192443cf25ffa5a7546b5cecb9f', 0),
(19, 'Шарлотта Линн', 'Утёс чукчи', 1555, 'Ъх', 'c8695d51b09a92aa6aa954da30d596fa.jpg', 666, 'c52a12a03ce1c03fbd4500ed5a51f46f', 0, '0d9ae76782676ffbbac4706666268a7b', 0),
(20, 'Стив Кавана', 'Пятьдесят на пятьдесят', 2020, 'Детективы', '9e5dbfdd7c8a29d7fe5346bbcc52eab0.jpg', 441, '708f8011f65c84a14b607995ca8b0104', 0, '98fac8398bdc1c45d53e9ed0a25b7f18', 0),
(21, 'Крис Кэмбелл', 'Крамблроу', 2023, 'Приключения', '16bd8de43ef70e3a9822cfd5156896b0.jpg', 420, 'fb24a159fd50e9018f446b420ff9218f', 0, 'f635695edf9f134181fbf464f3b792f2', 0),
(22, 'Адриана Дари', 'Хозяйка таверны У Черных скал', 2025, 'Приключения', '73d7096bdcadf1e55a15e22169e2f3d5.jpg', 240, '774fc9c5a45a9ac0fc3d2baf057bb6d7', 0, '7e026988bd66ed52fc4308856d9d8a45', 0),
(23, 'Андрей Булычев', 'Егерь императрицы. Русский маятник', 2025, 'Приключения', '0669e3b12444130e71323d5324899e95.jpg', 300, 'fd3cf3486342e4650d01f8b360636d9e', 0, '0636ad3fac398ec4b0958c765f889769', 0),
(24, 'Екатерина Воронова', 'Наследник тьмы', 2023, 'Фантастика', '23f67117d71aa4fdcf80b90542c7f748.jpg', 390, '1b63b1756fc7d789f7e9b1261059badc', 0, '70da7ec75b54a36ff0f888c8d269580a', 0),
(25, 'Алина Смирнова', 'Анхорн. Цифровые Боги - Империя Алур', 2024, 'Приключения', 'f6494520060288f99fa4d7f4f0546f5a.jpg', 560, '4325087ef9c2b3baca4e9213161e2cd6', 0, 'd8798ff1ad35f683e0d264d663d6f3b7', 0),
(26, 'Ольга Ильина', 'Наперегонки с туманностью', 2025, 'Фантастика', '2e526db85277b178c5af202142f73692.jpg', 310, '00228b578f8026e65841d261316d426e', 0, '8c1c237a04e29c18f69ab1c0e1db5705', 0),
(27, 'Тест-автор', 'Какая-то Книга', 2025, 'Фантастика', '8988b9e156d02875e05fbcc92352256b.jpg', 13, '25adafe874d018f4d4d60100c605dc08', 0, '7881a0e65b4b9eee092ad412765da488', 0),
(28, 'Елена Михалкова', 'Что дальше, миссис Норидж?', 2025, 'Детективы', '8d55fc01413915a14b8a22e958bd459a.jpg', 291, '550127bfbbdb3857ee4b0e2be99ff2a5', 0, '3d0d7d000191430f5d414a40b5d75b5d', 0),
(29, 'Майк Омер', 'За спиной', 2024, 'Детективы', 'b502ba9ea84ba28290045dcb762706ff.jpg', 410, '56bd538cd38e3b6bc20396dbe1b70093', 0, 'bf3f818063bed33738aa2734f2b9dee7', 0);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1xwZKySM9tyrk2SOdy624QUjOKEOWnZVsNNpIo05', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiank2eHc4N2Q5Wnk1WXhNNXpUSkdXcGhUdFQ3cHBNcXNlbW14N29ZTSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly90ZXN0LTEudGVzdC9ib29rcy8zIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1742760586),
('ebKDkuMcyqOlHFjkaBi8Gxir7vcfmDW6o6QGZTrX', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVVJmcW1uWWo1c1lwZkQ5RG9pZlRiY0wxMkJJc0R1elg1MjBPc2pLbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTg6Imh0dHA6Ly90ZXN0LTEudGVzdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1742579994),
('FXeEA58VkKI9TJTDw0QmjvfpHLmT8dJWzIfgF0hk', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTmhEUEFqRDU4bVljeFRPamVMN1I2eGtka2d5aExINEJlQVpKSVJqTSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly90ZXN0LTEudGVzdC9ib29rcy9hZGQtYm9vayI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1742590997),
('kekBoEejtf8SGln5PMtSEhinaWuo9VrBmcZ0A5UG', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Herd/1.17.0 Chrome/120.0.6099.291 Electron/28.2.5 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT1JXZlRtTzJqVXFIRjJJSDJWZndLc01lRGZzWmsycjRWQzllOEtyRCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly90ZXN0LTEudGVzdC8/aGVyZD1wcmV2aWV3Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1742837556),
('l8mlAYhGjjEn9wpt8My0tMgSwKU091QU3kSfcfHV', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU0lSVFkxU3NTejNPT293Mk5GVEtnTVpKOUNyY1lSQ09qM3IwVW5FdCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly90ZXN0LTEudGVzdC9ib29rcy8zIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1742750763),
('LaxNK4SfGexXAIhOwN7B9LmpQtKwp2Vs4crNCS2k', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVWdWQlZjWU51WkpnSUpIUThpM3A1M3lucGVaTmxFTERnc3lzQ0tESCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjQ6Imh0dHA6Ly90ZXN0LTEudGVzdC9ib29rcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1742558789),
('nlVz6IOLPzYFJAs3Draks9hBOl2mYZnkE7c9DBvd', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRlNoMWdyU0JCRHk4Vm5pZ1l0OXBVYkRzdHR1NUdrVGxLTWJIYlNTNiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjQ6Imh0dHA6Ly90ZXN0LTEudGVzdC9ib29rcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1742558294),
('P3ZKj4NxwETDJHkC25DyCKg8pDExFwKaqQ7Kxt9Y', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicVJPbDV1TGEzQ3pDSDA2RFdqWWtLaFNnU1Jkak52b1RwaWloWFR5SyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTg6Imh0dHA6Ly90ZXN0LTEudGVzdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1742672573),
('pziXHRKYLfAO8ocI8Tt9AWeMbBMe51xIBhDr93lA', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRTI0SVR1emNmT1h1Zmh5NU82QTcyZW9aSUg1UURuRm9zWWNCd0FvYSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly90ZXN0LTEudGVzdC9ib29rcy8xIjt9fQ==', 1742723068),
('saG65LNPwFDPPLkQQ7tZiCu1y3Rkj7SilU5oD4mA', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoialF1cEhjOUlDQ2U1UnpYVU9aRUZjc3U3ZHpJc0l0eEdhamJuVEpuUiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTg6Imh0dHA6Ly90ZXN0LTEudGVzdCI7fX0=', 1742837563),
('Wx2UT8Jbt1INUTbGkR7TRn5jhhdG2PmrzhvGarZT', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiY3dSUGg1Y00wODdYTnpjdzd3UUkwWlRxTllKdFJiUWwyQ01wSmFDZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjQ6Imh0dHA6Ly90ZXN0LTEudGVzdC9ib29rcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1742558594),
('Zqac0XVrK9Ami3xgyw9nNNwHc4ZGtRKgFlNzdLwB', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYkI5Vjc5eHI3VUxweEpkYmt6dGJoV2NGdzlwblo1SWhTdGNST3M2eSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjQ6Imh0dHA6Ly90ZXN0LTEudGVzdC9ib29rcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1742558243);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_list`
--

CREATE TABLE `users_list` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `user_login` varchar(20) NOT NULL,
  `user_passh` varchar(255) NOT NULL,
  `user_salt` varchar(64) NOT NULL,
  `create_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users_list`
--

INSERT INTO `users_list` (`user_id`, `user_login`, `user_passh`, `user_salt`, `create_datetime`) VALUES
(6, 'test-user', 'EtRwTNdL/3HvdFBhKdJiH1EBzxM72u6coxGhacH7oSrqFgTlWKkEo.dEeZz7ziFUK8.OVo8ufi6A1', 'af395b4fca136ccb9b1ed9e8660db1fadce434e9069fa7eef4ae42e81d3b5a35', '2025-03-21 18:02:01');

-- --------------------------------------------------------

--
-- Table structure for table `visitors_list`
--

CREATE TABLE `visitors_list` (
  `visitor_id` bigint(20) UNSIGNED NOT NULL,
  `visitor_sess` varchar(128) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `visitor_salt` varchar(64) NOT NULL,
  `create_dtm` datetime NOT NULL,
  `update_dtm` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `visitors_list`
--

INSERT INTO `visitors_list` (`visitor_id`, `visitor_sess`, `user_id`, `visitor_salt`, `create_dtm`, `update_dtm`) VALUES
(2, 'f16652236bbc332698c415aa7df219d71d317f0006e402bba49fd658a744e43d2d81a1207872b2f2954a3ccdeab0ea6a69f2c4abc3361d61848711ed157de1e3', 6, '9057213ebfa2f4890dcfee96c305fc8d69cf3b803d247cc4feab6c67068b9cea', '2025-03-21 15:14:10', '2025-03-24 20:32:37'),
(3, '0d05bb94e1d90a725356d317ceb548d4af1e2a8a096e8a49408f4a8625e4b1e2e7379a035a8bd2d0ba5178400501c0c127d4fdd0cc96dbf82a0b2b20df37e6ec', 0, '85955f2ab9c2269d540a210120c0f5ffbaee9c5a3852ea1be2d2142fbdb24920', '2025-03-24 20:32:33', '2025-03-24 20:32:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books_list`
--
ALTER TABLE `books_list`
  ADD PRIMARY KEY (`book_id`),
  ADD UNIQUE KEY `book_title` (`book_title`),
  ADD KEY `author_id` (`author_full_name`),
  ADD KEY `genre_id` (`genre_title`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `users_list`
--
ALTER TABLE `users_list`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_login` (`user_login`);

--
-- Indexes for table `visitors_list`
--
ALTER TABLE `visitors_list`
  ADD UNIQUE KEY `visitor_id` (`visitor_id`),
  ADD UNIQUE KEY `visitor_sess` (`visitor_sess`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books_list`
--
ALTER TABLE `books_list`
  MODIFY `book_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_list`
--
ALTER TABLE `users_list`
  MODIFY `user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `visitors_list`
--
ALTER TABLE `visitors_list`
  MODIFY `visitor_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
