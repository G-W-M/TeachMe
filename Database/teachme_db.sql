-- Create database
CREATE DATABASE IF NOT EXISTS teachme;
USE teachme;

-- 1. USERS 
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) UNIQUE,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    user_name VARCHAR(50),
    phone VARCHAR(20),
    role ENUM('learner','tutor','admin') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    date_joined DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. ADMIN 
CREATE TABLE admin (
    admin_id INT PRIMARY KEY,
    staff_number VARCHAR(50),
    FOREIGN KEY (admin_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- 3. UNITS 
CREATE TABLE units (
    unit_id INT AUTO_INCREMENT PRIMARY KEY,
    unit_code VARCHAR(20) UNIQUE NOT NULL,
    unit_name VARCHAR(100) NOT NULL
);

-- 4. TUTOR APPLICATIONS 
CREATE TABLE tutor_applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    unit_id INT NOT NULL,
    test_score DECIMAL(5,2),
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    application_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES units(unit_id)
);

-- 5. TUTOR 
CREATE TABLE tutor (
    tutor_id INT PRIMARY KEY,
    bio TEXT,
    max_students INT DEFAULT 3,
    current_students INT DEFAULT 0,
    rating DECIMAL(3,2) DEFAULT 0,
    FOREIGN KEY (tutor_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- 6. TUTOR AVAILABILITY
CREATE TABLE tutor_availability (
    availability_id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT NOT NULL,
    day ENUM('mon','tue','wed','thu','fri','sat','sun') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (tutor_id) REFERENCES tutor(tutor_id) ON DELETE CASCADE
);

-- 7. TUTOR COMPETENCIES 
CREATE TABLE tutor_competencies (
    competency_id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT NOT NULL,
    unit_id INT NOT NULL,
    FOREIGN KEY (tutor_id) REFERENCES tutor(tutor_id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES units(unit_id),
    UNIQUE (tutor_id, unit_id)
);

-- 8. LEARNING REQUESTS 
CREATE TABLE learning_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    learner_id INT NOT NULL,
    unit_id INT NOT NULL,
    description TEXT,
    status ENUM('open','matched','completed','cancelled') DEFAULT 'open',
    matched_tutor_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (learner_id) REFERENCES users(user_id),
    FOREIGN KEY (unit_id) REFERENCES units(unit_id),
    FOREIGN KEY (matched_tutor_id) REFERENCES tutor(tutor_id)
);

-- 9. SESSIONS 
CREATE TABLE sessions (
    session_id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    tutor_id INT NOT NULL,
    learner_id INT NOT NULL,
    session_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('scheduled','completed','cancelled') DEFAULT 'scheduled',
    FOREIGN KEY (request_id) REFERENCES learning_requests(request_id),
    FOREIGN KEY (tutor_id) REFERENCES tutor(tutor_id),
    FOREIGN KEY (learner_id) REFERENCES users(user_id)
);

-- 10. ATTENDANCE
CREATE TABLE session_attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    user_id INT NOT NULL,
    attended BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (session_id) REFERENCES sessions(session_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- 11. FEEDBACK 
CREATE TABLE feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    from_user INT NOT NULL,
    to_user INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comments TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES sessions(session_id),
    FOREIGN KEY (from_user) REFERENCES users(user_id),
    FOREIGN KEY (to_user) REFERENCES users(user_id)
);

-- 12. CERTIFICATES 
CREATE TABLE certificates (
    certificate_id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT NOT NULL,
    certificate_type VARCHAR(50),
    issued_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tutor_id) REFERENCES tutor(tutor_id)
);

-- 13. SYSTEM LOGS 
CREATE TABLE system_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100),
    time DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- 14. NOTIFICATIONS
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    type VARCHAR(20) DEFAULT 'info',
    related_id INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
