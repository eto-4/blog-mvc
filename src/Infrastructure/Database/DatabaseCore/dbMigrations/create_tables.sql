-- Taula d'usuaris
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `avatar` VARCHAR(255) DEFAULT NULL,
    `bio` TEXT DEFAULT NULL,
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
    `last_login_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`)
);

-- Taula de posts
CREATE TABLE IF NOT EXISTS `posts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `content` TEXT NOT NULL,
    `excerpt` VARCHAR(500) DEFAULT NULL,
    `featured_image` VARCHAR(255) DEFAULT NULL,
    `author_id` INT NOT NULL,
    `status` ENUM('draft', 'published', 'archived') DEFAULT 'published',
    `views_count` INT DEFAULT 0,
    `published_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_author` (`author_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_published_at` (`published_at`),
    FULLTEXT `idx_search` (`title`, `content`)
);

-- Taula d'auditoria d'accions administratives
-- Guarda un snapshot JSON de l'entitat eliminada/modificada
-- expires_at es calcula automàticament com created_at + 9 mesos (~270 dies)
CREATE TABLE IF NOT EXISTS `audit_log` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `action` VARCHAR(50) NOT NULL,
    `entity_type` VARCHAR(50) NOT NULL,
    `entity_id` INT NOT NULL,
    `entity_data` JSON NOT NULL,
    `performed_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expires_at` TIMESTAMP GENERATED ALWAYS AS
        (DATE_ADD(`created_at`, INTERVAL 270 DAY)) STORED,
    INDEX `idx_action`      (`action`),
    INDEX `idx_entity_type` (`entity_type`),
    INDEX `idx_performed_by`(`performed_by`),
    INDEX `idx_expires_at`  (`expires_at`),
    FOREIGN KEY (`performed_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
);