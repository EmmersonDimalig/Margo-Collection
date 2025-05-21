-- Add role column to users table
ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user';

-- Create admin user (password: margamargalowlow)
INSERT INTO users (username, email, password, role) 
VALUES ('Margondez', 'admin@margocollection.com', '$2y$10$YourHashedPasswordHere', 'admin'); 