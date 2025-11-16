-- teachme_db.sql
-- Database creation
CREATE DATABASE IF NOT EXISTS teachme;
USE teachme;

-- Users table (common base for all roles)
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(15),
    role ENUM('learner', 'tutor', 'admin') NOT NULL,
    date_joined DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_role (role),
    INDEX idx_student_id (student_id)
);

CREATE TABLE admin(
    admin_id INT PRIMARY KEY,
    staff_number VARCHAR(50),
    FOREIGN KEY (admin_id) REFERENCES users(user_id)
        ON DELETE CASCADE
);

-- Units/Subjects table
CREATE TABLE units (
    unit_id INT AUTO_INCREMENT PRIMARY KEY,
    unit_code VARCHAR(20) UNIQUE NOT NULL,
    unit_name VARCHAR(100) NOT NULL,
    description TEXT,
    department VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE
);

-- Tutor applications and qualifications
CREATE TABLE tutor_applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    unit_id INT NOT NULL,
    application_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    test_score DECIMAL(5,2),
    max_score DECIMAL(5,2) DEFAULT 100,
    status ENUM('pending', 'approved', 'rejected', 'test_pending') DEFAULT 'pending',
    application_notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES units(unit_id),
    INDEX idx_status (status),
    INDEX idx_unit (unit_id)
);

CREATE TABLE learner (
    learner_id INT PRIMARY KEY,
    unit_code VARCHAR(20) UNIQUE NOT NULL,
    unit_name VARCHAR(100) NOT NULL,
    year_of_study INT,
    FOREIGN KEY (learner_id) REFERENCES users(user_id)
        ON DELETE CASCADE
);


-- Tutor  (for approved tutors only)
CREATE TABLE tutor (
    tutor_id INT PRIMARY KEY,
    user_id INT NOT NULL,
    bio TEXT,
    hourly_rate DECIMAL(8,2) DEFAULT 0,
    max_students INT DEFAULT 3,
    current_student_count INT DEFAULT 0,
    overall_rating DECIMAL(3,2) DEFAULT 0,
    total_sessions INT DEFAULT 0,
    is_available BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CHECK (current_student_count <= max_students)
);

-- Tutor availability
CREATE TABLE tutor_availability (
    availability_id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT NOT NULL,
    day_of_week ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_recurring BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (tutor_id) REFERENCES tutor (tutor_id) ON DELETE CASCADE,
    INDEX idx_tutor_day (tutor_id, day_of_week)
);

-- Tutor competencies (which units they can teach)
CREATE TABLE tutor_competencies (
    competency_id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT NOT NULL,
    unit_id INT NOT NULL,
    proficiency_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'intermediate',
    is_verified BOOLEAN DEFAULT FALSE,
    verified_by INT,
    verified_at DATETIME,
    FOREIGN KEY (tutor_id) REFERENCES tutor (tutor_id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES units(unit_id),
    FOREIGN KEY (verified_by) REFERENCES users(user_id),
    UNIQUE KEY unique_tutor_unit (tutor_id, unit_id)
);

-- Learning requests from students
CREATE TABLE learning_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    learner_id INT NOT NULL,
    unit_id INT NOT NULL,
    topic_description TEXT,
    urgency ENUM('low', 'medium', 'high') DEFAULT 'medium',
    preferred_days VARCHAR(100),
    preferred_times VARCHAR(100),
    status ENUM('open', 'matched', 'in_progress', 'completed', 'cancelled') DEFAULT 'open',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    matched_tutor_id INT,
    matched_at DATETIME,
    FOREIGN KEY (learner_id) REFERENCES users(user_id),
    FOREIGN KEY (unit_id) REFERENCES units(unit_id),
    FOREIGN KEY (matched_tutor_id) REFERENCES tutor (tutor_id),
    INDEX idx_status (status),
    INDEX idx_learner (learner_id),
    INDEX idx_unit (unit_id)
);

-- Tutoring sessions
CREATE TABLE sessions (
    session_id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    tutor_id INT NOT NULL,
    learner_id INT NOT NULL,
    unit_id INT NOT NULL,
    session_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    duration_minutes INT,
    location VARCHAR(100),
    session_notes TEXT,
    status ENUM('scheduled', 'completed', 'cancelled', 'no_show') DEFAULT 'scheduled',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES learning_requests(request_id),
    FOREIGN KEY (tutor_id) REFERENCES tutor (tutor_id),
    FOREIGN KEY (learner_id) REFERENCES users(user_id),
    FOREIGN KEY (unit_id) REFERENCES units(unit_id),
    INDEX idx_date (session_date),
    INDEX idx_tutor (tutor_id),
    INDEX idx_learner (learner_id)
);

-- Session attendance
CREATE TABLE session_attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('tutor', 'learner') NOT NULL,
    attended BOOLEAN DEFAULT FALSE,
    join_time DATETIME,
    leave_time DATETIME,
    attendance_notes TEXT,
    FOREIGN KEY (session_id) REFERENCES sessions(session_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    UNIQUE KEY unique_session_attendance (session_id, user_id, role)
);

-- Feedback and ratings
CREATE TABLE feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    from_user_id INT NOT NULL,
    to_user_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comments TEXT,
    feedback_type ENUM('tutor_to_learner', 'learner_to_tutor') NOT NULL,
    is_anonymous BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES sessions(session_id),
    FOREIGN KEY (from_user_id) REFERENCES users(user_id),
    FOREIGN KEY (to_user_id) REFERENCES users(user_id),
    INDEX idx_to_user (to_user_id),
    INDEX idx_session (session_id)
);

-- Tutor certificates
CREATE TABLE certificates (
    certificate_id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT NOT NULL,
    certificate_type VARCHAR(50) NOT NULL,
    certificate_data JSON,
    issued_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    expiry_date DATE,
    issued_by INT NOT NULL,
    status ENUM('active', 'expired', 'revoked') DEFAULT 'active',
    FOREIGN KEY (tutor_id) REFERENCES tutor (tutor_id),
    FOREIGN KEY (issued_by) REFERENCES users(user_id),
    INDEX idx_tutor (tutor_id),
    INDEX idx_status (status)
);

-- System logs
CREATE TABLE system_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    log_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    INDEX idx_timestamp (log_timestamp),
    INDEX idx_user_action (user_id, action)
);

-- Notifications table
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    notification_type VARCHAR(50),
    is_read BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    related_entity_type VARCHAR(50),
    related_entity_id INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    INDEX idx_user_unread (user_id, is_read),
    INDEX idx_created (created_at)
);

