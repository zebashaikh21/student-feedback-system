CREATE DATABASE IF NOT EXISTS student_feedback_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE student_feedback_system;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS feedback_answers;
DROP TABLE IF EXISTS feedback;
DROP TABLE IF EXISTS questions;
DROP TABLE IF EXISTS subjects;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','student','faculty') NOT NULL,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  student_code VARCHAR(30) DEFAULT NULL,
  faculty_code VARCHAR(30) DEFAULT NULL,
  department VARCHAR(100) DEFAULT NULL,
  semester VARCHAR(50) DEFAULT NULL,
  designation VARCHAR(100) DEFAULT NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE subjects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  subject_name VARCHAR(100) NOT NULL,
  semester VARCHAR(50) DEFAULT NULL,
  status ENUM('active','inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  question_text VARCHAR(255) NOT NULL,
  category VARCHAR(100) DEFAULT NULL,
  status ENUM('active','inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  faculty_id INT NOT NULL,
  subject_id INT NOT NULL,
  comment TEXT,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_feedback (student_id, faculty_id, subject_id),
  FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (faculty_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE feedback_answers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  feedback_id INT NOT NULL,
  question_id INT NOT NULL,
  rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
  FOREIGN KEY (feedback_id) REFERENCES feedback(id) ON DELETE CASCADE,
  FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Demo accounts use plain text only for first login; login.php upgrades them to password hashes.
INSERT INTO users (username,password,role,name,email,student_code,faculty_code,department,semester,designation) VALUES
('admin','Admin@123','admin','Admin User','admin@example.com',NULL,NULL,'Administration',NULL,'Administrator'),
('student01','Student@123','student','Priya Sharma','student01@example.com','STU001',NULL,'Computer Science','MCA 3rd Semester',NULL),
('student02','Student@456','student','Aman Verma','student02@example.com','STU002',NULL,'Computer Science','MCA 3rd Semester',NULL),
('faculty01','Faculty@123','faculty','Dr. Rahul Sharma','faculty01@example.com',NULL,'FAC001','Computer Science',NULL,'Assistant Professor'),
('faculty02','Faculty@456','faculty','Dr. Priya Singh','faculty02@example.com',NULL,'FAC002','Computer Science',NULL,'Associate Professor');

INSERT INTO subjects (subject_name,semester) VALUES
('Database Management System','MCA 3rd Semester'),
('Java Programming','MCA 3rd Semester'),
('Data Structures and Algorithms','MCA 3rd Semester'),
('Computer Networks','MCA 3rd Semester'),
('Machine Learning','MCA 3rd Semester'),
('Web Technologies','MCA 3rd Semester');

INSERT INTO questions (question_text,category) VALUES
('The faculty explains concepts clearly.','Teaching'),
('The faculty is well prepared for classes.','Teaching'),
('The faculty encourages student participation.','Engagement'),
('The faculty is approachable and helpful.','Support'),
('The faculty is punctual and regular.','Professionalism'),
('The faculty provides clear guidance.','Teaching'),
('The faculty uses effective teaching methods.','Teaching'),
('The faculty is fair in evaluation.','Assessment'),
('The faculty shows interest in student progress.','Support'),
('Overall, I am satisfied with the teaching performance.','Overall');
