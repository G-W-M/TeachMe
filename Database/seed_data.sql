INSERT INTO users (student_id, email, password_hash, user_name, phone, role)
VALUES (
    'ADM001',
    'admin@teachme.com',
    '$2y$10$Z9joMn2hG/Xb0cuhG5JzU.FgkM1z3VZTmsYjPqkj/tG9QWQ8GcEFe', 
    'System Admin',
    '0700000000',
    'admin'
);

INSERT INTO admin (admin_id, staff_number)
VALUES (LAST_INSERT_ID(), 'STAFF-001');
--(Admin123)