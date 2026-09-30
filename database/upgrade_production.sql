-- ============================================================
-- HTG Console v2 – Database Upgrade Script
-- Run this AFTER importing u433571580_admin.sql
-- Adds all new tables and columns added since the original schema
-- Generated: 2026-09-30
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ============================================================
-- 1. ALTER existing tables – add missing columns
-- ============================================================

-- 1a. entries: add reminders_enabled
ALTER TABLE `entries`
  ADD COLUMN `reminders_enabled` tinyint(1) NOT NULL DEFAULT 1 AFTER `state`;

-- 1b. products: add payment_date
ALTER TABLE `products`
  ADD COLUMN `payment_date` date DEFAULT NULL AFTER `expiry_date`;

-- 1c. banks: add opening_balance
ALTER TABLE `banks`
  ADD COLUMN `opening_balance` decimal(14,2) NOT NULL DEFAULT 0.00 AFTER `bank`;

-- 1d. buildings: add property_id, tenant_id (FK added later)
ALTER TABLE `buildings`
  ADD COLUMN `property_id` bigint(20) UNSIGNED DEFAULT NULL AFTER `id`,
  ADD COLUMN `tenant_id` bigint(20) UNSIGNED DEFAULT NULL AFTER `property_id`;

-- ============================================================
-- 2. CREATE new tables
-- ============================================================

-- 2a. notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`, `notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2b. payment_histories
CREATE TABLE IF NOT EXISTS `payment_histories` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entry_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_bank` varchar(255) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_histories_entry_id_foreign` (`entry_id`),
  KEY `payment_histories_product_id_foreign` (`product_id`),
  CONSTRAINT `payment_histories_entry_id_foreign` FOREIGN KEY (`entry_id`) REFERENCES `entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_histories_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2c. services
CREATE TABLE IF NOT EXISTS `services` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2d. properties
CREATE TABLE IF NOT EXISTS `properties` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2e. tenants
CREATE TABLE IF NOT EXISTS `tenants` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `rent_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenants_property_id_foreign` (`property_id`),
  CONSTRAINT `tenants_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2f. whatsapp_settings
CREATE TABLE IF NOT EXISTS `whatsapp_settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `phone_number_id` varchar(255) NOT NULL,
  `business_account_id` varchar(255) DEFAULT NULL,
  `access_token` text NOT NULL,
  `api_version` varchar(10) NOT NULL DEFAULT 'v21.0',
  `display_phone` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2g. whatsapp_templates
CREATE TABLE IF NOT EXISTS `whatsapp_templates` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `language` varchar(10) NOT NULL DEFAULT 'en',
  `category` varchar(50) NOT NULL DEFAULT 'UTILITY',
  `components` longtext DEFAULT NULL CHECK (json_valid(`components`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2h. whatsapp_logs
CREATE TABLE IF NOT EXISTS `whatsapp_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `to_phone` varchar(20) NOT NULL,
  `to_name` varchar(255) DEFAULT NULL,
  `template_name` varchar(255) DEFAULT NULL,
  `message_type` varchar(30) NOT NULL DEFAULT 'template',
  `content` text DEFAULT NULL,
  `wa_message_id` varchar(255) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'sent',
  `error` text DEFAULT NULL,
  `context_type` varchar(50) DEFAULT NULL,
  `context_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sent_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `whatsapp_logs_context_type_context_id_index` (`context_type`, `context_id`),
  KEY `whatsapp_logs_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2i. whatsapp_messages
CREATE TABLE IF NOT EXISTS `whatsapp_messages` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `phone` varchar(20) NOT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `direction` enum('in','out') NOT NULL DEFAULT 'out',
  `message_type` varchar(30) NOT NULL DEFAULT 'text',
  `content` text DEFAULT NULL,
  `media_url` text DEFAULT NULL,
  `media_mime` varchar(100) DEFAULT NULL,
  `wa_message_id` varchar(255) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'sent',
  `context_type` varchar(50) DEFAULT NULL,
  `context_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sent_by` bigint(20) UNSIGNED DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `whatsapp_messages_phone_index` (`phone`),
  KEY `whatsapp_messages_wa_message_id_index` (`wa_message_id`),
  KEY `whatsapp_messages_phone_created_at_index` (`phone`, `created_at`),
  KEY `whatsapp_messages_direction_index` (`direction`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2j. reminder_rules
CREATE TABLE IF NOT EXISTS `reminder_rules` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('before_expiry','on_expiry','after_expiry') NOT NULL,
  `days` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `channels` longtext NOT NULL CHECK (json_valid(`channels`)),
  `wa_template_name` varchar(255) DEFAULT NULL,
  `sms_template_id` varchar(255) DEFAULT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2k. reminder_logs
CREATE TABLE IF NOT EXISTS `reminder_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `entry_id` bigint(20) UNSIGNED NOT NULL,
  `rule_id` bigint(20) UNSIGNED DEFAULT NULL,
  `channel` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'sent',
  `error` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `reminder_logs_product_id_rule_id_index` (`product_id`, `rule_id`),
  KEY `reminder_logs_entry_id_index` (`entry_id`),
  KEY `reminder_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2l. balance_reminder_logs
CREATE TABLE IF NOT EXISTS `balance_reminder_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `entry_id` bigint(20) UNSIGNED NOT NULL,
  `channel` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'sent',
  `error` text DEFAULT NULL,
  `sent_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `balance_reminder_logs_product_id_created_at_index` (`product_id`, `created_at`),
  KEY `balance_reminder_logs_entry_id_index` (`entry_id`),
  KEY `balance_reminder_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2m. lead_notes
CREATE TABLE IF NOT EXISTS `lead_notes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `telecaller_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('note','call','meeting','email','whatsapp','status_change') NOT NULL DEFAULT 'note',
  `content` text NOT NULL,
  `meta` longtext DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_notes_telecaller_id_created_at_index` (`telecaller_id`, `created_at`),
  KEY `lead_notes_user_id_foreign` (`user_id`),
  CONSTRAINT `lead_notes_telecaller_id_foreign` FOREIGN KEY (`telecaller_id`) REFERENCES `telecallers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lead_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. Add FOREIGN KEYS on buildings (after properties/tenants exist)
-- ============================================================

ALTER TABLE `buildings`
  ADD CONSTRAINT `buildings_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `buildings_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE SET NULL;

-- ============================================================
-- 4. SEED data for new tables
-- ============================================================

-- 4a. services (18 default services)
INSERT INTO `services` (`name`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
('Local Keyword SEO', 1, 0, NOW(), NOW()),
('Virtual Tour', 1, 1, NOW(), NOW()),
('Google Business Profile Management', 1, 2, NOW(), NOW()),
('Zonal Keyword SEO', 1, 3, NOW(), NOW()),
('Google Ads', 1, 4, NOW(), NOW()),
('Google Ads Recharge', 1, 5, NOW(), NOW()),
('Meta Ads Management', 1, 6, NOW(), NOW()),
('Facebook Ads Recharge', 1, 7, NOW(), NOW()),
('Social Media Management', 1, 8, NOW(), NOW()),
('Website Design', 1, 9, NOW(), NOW()),
('Custom Development', 1, 10, NOW(), NOW()),
('Website Amc', 1, 11, NOW(), NOW()),
('Product Photography', 1, 12, NOW(), NOW()),
('Domain', 1, 13, NOW(), NOW()),
('Hosting', 1, 14, NOW(), NOW()),
('QR Code', 1, 15, NOW(), NOW()),
('Web SEO', 1, 16, NOW(), NOW()),
('Others', 1, 17, NOW(), NOW());

-- 4b. reminder_rules (6 default rules)
INSERT INTO `reminder_rules` (`name`, `type`, `days`, `channels`, `wa_template_name`, `sms_template_id`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
('30 Days Before', 'before_expiry', 30, '["email"]', NULL, NULL, 1, 0, NOW(), NOW()),
('15 Days Before', 'before_expiry', 15, '["sms","email"]', NULL, NULL, 2, 1, NOW(), NOW()),
('7 Days Before', 'before_expiry', 7, '["sms","email","whatsapp"]', NULL, NULL, 3, 1, NOW(), NOW()),
('3 Days Before', 'before_expiry', 3, '["sms","whatsapp"]', NULL, NULL, 4, 1, NOW(), NOW()),
('1 Day Before', 'before_expiry', 1, '["sms","email","whatsapp"]', NULL, NULL, 5, 1, NOW(), NOW()),
('On Expiry Day', 'on_expiry', 0, '["sms","email","whatsapp"]', NULL, NULL, 6, 1, NOW(), NOW());

-- 4c. Seed payment_histories from existing products (where paid_amount > 0)
INSERT INTO `payment_histories` (`entry_id`, `product_id`, `product_name`, `amount`, `payment_date`, `created_at`, `updated_at`)
SELECT
  p.`entry_id`,
  p.`id`,
  p.`product_name`,
  CAST(p.`paid_amount` AS DECIMAL(12,2)),
  COALESCE(p.`payment_date`, CURDATE()),
  NOW(),
  NOW()
FROM `products` p
WHERE CAST(p.`paid_amount` AS DECIMAL(12,2)) > 0;

-- 4d. Seed properties from distinct building names
INSERT INTO `properties` (`name`, `is_active`, `created_at`, `updated_at`)
SELECT DISTINCT TRIM(`building`), 1, NOW(), NOW()
FROM `buildings`
WHERE `building` IS NOT NULL AND TRIM(`building`) != '';

-- 4e. Seed tenants from distinct name+building combos
INSERT INTO `tenants` (`property_id`, `name`, `mobile`, `rent_amount`, `is_active`, `created_at`, `updated_at`)
SELECT
  pr.`id`,
  sub.`name`,
  sub.`mobile`,
  COALESCE(CAST(sub.`amount` AS DECIMAL(12,2)), 0),
  1,
  NOW(),
  NOW()
FROM (
  SELECT DISTINCT TRIM(`building`) AS bldg, `name`, `mobile`, `amount`
  FROM `buildings`
  WHERE `name` IS NOT NULL AND TRIM(`name`) != ''
    AND `building` IS NOT NULL AND TRIM(`building`) != ''
) sub
JOIN `properties` pr ON pr.`name` = sub.bldg;

-- 4f. Link buildings to properties
UPDATE `buildings` b
JOIN `properties` pr ON pr.`name` = TRIM(b.`building`)
SET b.`property_id` = pr.`id`
WHERE b.`building` IS NOT NULL AND TRIM(b.`building`) != '';

-- 4g. Link buildings to tenants
UPDATE `buildings` b
JOIN `properties` pr ON pr.`id` = b.`property_id`
JOIN `tenants` t ON t.`property_id` = pr.`id` AND t.`name` = b.`name`
SET b.`tenant_id` = t.`id`
WHERE b.`name` IS NOT NULL AND b.`property_id` IS NOT NULL;

-- ============================================================
-- 5. Update migrations table so Laravel knows these ran
-- ============================================================

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2025_02_26_121422_create_notifications_table', 2),
('2026_09_14_073131_add_payment_date_to_products_table', 2),
('2026_09_14_131200_create_payment_histories_table', 2),
('2026_09_14_131201_seed_payment_histories_from_existing', 2),
('2026_09_14_131300_add_payment_bank_to_payment_histories_table', 2),
('2026_09_17_100000_add_opening_balance_to_banks_table', 2),
('2026_09_25_100000_create_services_table', 2),
('2026_09_26_100000_create_properties_and_tenants_tables', 2),
('2026_09_26_110000_create_whatsapp_tables', 2),
('2026_09_27_100000_create_reminder_system_tables', 2),
('2026_09_27_200000_create_balance_reminder_tables', 2),
('2026_09_28_100000_create_whatsapp_messages_table', 2),
('2026_09_28_120000_create_lead_notes_table', 2);

-- ============================================================
-- Done! All new tables and columns have been added.
-- ============================================================
