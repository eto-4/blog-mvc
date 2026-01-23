CREATE TABLE IF NOT EXISTS `tasks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT '',
  `tags` JSON DEFAULT '[]',
  `cost` DECIMAL(10,2) DEFAULT 0,
  `due_date` DATETIME NOT NULL,
  `expected_hours` INT DEFAULT 20,
  `used_hours` INT DEFAULT 0,
  `priority` ENUM('low', 'medium', 'high') DEFAULT 'medium',
  `state` ENUM('pending', 'in-progress', 'blocked', 'completed') DEFAULT 'pending',
  `finished_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;