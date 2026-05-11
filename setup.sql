-- LifeFlow Blood Bank Management System
-- Run this entire file in MySQL Workbench: File > Open SQL Script > Ctrl+Shift+Enter

CREATE DATABASE IF NOT EXISTS bloodbank CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bloodbank;

-- Drop existing tables in correct order (foreign key safe)
DROP TABLE IF EXISTS sms_logs;
DROP TABLE IF EXISTS certificates;
DROP TABLE IF EXISTS donations;
DROP TABLE IF EXISTS blood_requests;
DROP TABLE IF EXISTS donation_camps;
DROP TABLE IF EXISTS blood_inventory;
DROP TABLE IF EXISTS donors;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(100)  NOT NULL,
  email      VARCHAR(150)  NOT NULL UNIQUE,
  password   VARCHAR(255)  NOT NULL,
  role       ENUM('admin','donor','hospital') NOT NULL DEFAULT 'donor',
  is_active  TINYINT(1)    NOT NULL DEFAULT 1,
  created_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE donors (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  user_id         INT NOT NULL,
  blood_group     ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  phone           VARCHAR(20)   DEFAULT NULL,
  address         VARCHAR(255)  DEFAULT NULL,
  city            VARCHAR(100)  DEFAULT NULL,
  state           VARCHAR(100)  DEFAULT NULL,
  lat             DECIMAL(10,7) DEFAULT NULL,
  lng             DECIMAL(10,7) DEFAULT NULL,
  dob             DATE          DEFAULT NULL,
  gender          ENUM('male','female','other') DEFAULT NULL,
  weight_kg       DECIMAL(5,2)  DEFAULT NULL,
  last_donated    DATE          DEFAULT NULL,
  total_donations INT           NOT NULL DEFAULT 0,
  is_available    TINYINT(1)    NOT NULL DEFAULT 1,
  medical_notes   TEXT          DEFAULT NULL,
  created_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE blood_inventory (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  blood_group    ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL UNIQUE,
  units          INT NOT NULL DEFAULT 0,
  critical_level INT NOT NULL DEFAULT 5,
  updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE blood_requests (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  hospital_id  INT NOT NULL,
  patient_name VARCHAR(100)  DEFAULT NULL,
  blood_group  ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  units_needed INT NOT NULL,
  urgency      ENUM('normal','urgent','critical') NOT NULL DEFAULT 'normal',
  status       ENUM('pending','approved','fulfilled','rejected') NOT NULL DEFAULT 'pending',
  notes        TEXT DEFAULT NULL,
  requested_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fulfilled_at DATETIME  DEFAULT NULL,
  FOREIGN KEY (hospital_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE donation_camps (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(150)  NOT NULL,
  location     VARCHAR(255)  DEFAULT NULL,
  city         VARCHAR(100)  DEFAULT NULL,
  lat          DECIMAL(10,7) DEFAULT NULL,
  lng          DECIMAL(10,7) DEFAULT NULL,
  camp_date    DATE          NOT NULL,
  start_time   TIME          DEFAULT NULL,
  end_time     TIME          DEFAULT NULL,
  organizer    VARCHAR(100)  DEFAULT NULL,
  contact      VARCHAR(50)   DEFAULT NULL,
  max_capacity INT           NOT NULL DEFAULT 100,
  registered   INT           NOT NULL DEFAULT 0,
  status       ENUM('upcoming','active','completed','cancelled') NOT NULL DEFAULT 'upcoming',
  created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE donations (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  donor_id    INT NOT NULL,
  camp_id     INT DEFAULT NULL,
  blood_group ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  units       DECIMAL(4,2)  NOT NULL DEFAULT 1.00,
  donated_at  DATE          NOT NULL,
  staff_notes TEXT          DEFAULT NULL,
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE CASCADE,
  FOREIGN KEY (camp_id)  REFERENCES donation_camps(id) ON DELETE SET NULL
);

CREATE TABLE certificates (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  donor_id       INT NOT NULL,
  donation_id    INT DEFAULT NULL,
  certificate_no VARCHAR(30)  NOT NULL UNIQUE,
  issued_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (donor_id)    REFERENCES donors(id) ON DELETE CASCADE,
  FOREIGN KEY (donation_id) REFERENCES donations(id) ON DELETE SET NULL
);

CREATE TABLE sms_logs (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  to_number  VARCHAR(20)  DEFAULT NULL,
  message    TEXT         DEFAULT NULL,
  status     ENUM('sent','failed','pending') NOT NULL DEFAULT 'pending',
  sent_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  request_id INT          DEFAULT NULL
);

-- Seed blood inventory (all 8 groups)
INSERT INTO blood_inventory (blood_group, units, critical_level) VALUES
('A+',  45, 10), ('A-',  12, 5),
('B+',  38, 10), ('B-',   8, 5),
('AB+', 15,  5), ('AB-',  4, 3),
('O+',  52, 15), ('O-',  10, 8);

-- Seed admin user
-- Email: admin@lifeflow.com | Password: admin123
INSERT INTO users (name, email, password, role) VALUES
('System Admin', 'admin@lifeflow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- After import, update the admin password hash to one verified on your system:
-- UPDATE users SET password='$2y$10$IigxW5mjOZ8JDAahSxFVnezy0IHeinBxEZhJCbm9GdwXxgqsMY0fO' WHERE email='admin@lifeflow.com';
