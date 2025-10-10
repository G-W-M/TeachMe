CREATE TABLE students (
  student_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('learner', 'tutor', 'admin') DEFAULT 'learner'
);


CREATE TABLE tutors (
  tutor_id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  test_score DECIMAL(5,2),
  availability VARCHAR(100),
  performance_score DECIMAL(5,2) DEFAULT 0,
  FOREIGN KEY (student_id) REFERENCES students(student_id)
);



CREATE TABLE learners (
  learner_id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  requested_unit VARCHAR(100),
  FOREIGN KEY (student_id) REFERENCES students(student_id)
);



CREATE TABLE sessions (
  session_id INT AUTO_INCREMENT PRIMARY KEY,
  tutor_id INT NOT NULL,
  learner_id INT NOT NULL,
  unit VARCHAR(100),
  session_date DATETIME NOT NULL,
  attendance BOOLEAN DEFAULT FALSE,
  feedback TEXT,
  FOREIGN KEY (tutor_id) REFERENCES tutors(tutor_id),
  FOREIGN KEY (learner_id) REFERENCES learners(learner_id)
);



CREATE TABLE certificates (
  certificate_id INT AUTO_INCREMENT PRIMARY KEY,
  tutor_id INT NOT NULL,
  award_date DATE,
  criteria TEXT,
  FOREIGN KEY (tutor_id) REFERENCES tutors(tutor_id)
);


CREATE TABLE admins (
  admin_id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  FOREIGN KEY (student_id) REFERENCES students(student_id)
);