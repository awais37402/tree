CREATE DATABASE family_tree;

USE family_tree;

-- Table for members
CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    parent_id INT DEFAULT NULL,
    category VARCHAR(50),
    profile_picture VARCHAR(255),
    additional_info TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for admin
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

SELECT * FROM admin WHERE username = 'awaistahir01234@gmail.com';

INSERT INTO admin (username, password) 
VALUES ('awaistahir01234@gmail.com', '$2y$10$4ZpzspmZvsbn7bTu9RcGFu6CtXBqUBZ5hOTDj7LOrcDGfuIEd4Wi2');

UPDATE admin SET password = 'OUTPUT_HASH_HERE' WHERE username = 'awaistahir01234@gmail.com';
SELECT * FROM admin WHERE username = 'awaistahir01234@gmail.com';

CREATE TABLE relationships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member1_id INT NOT NULL,
    member2_id INT NOT NULL,
    relationship_type VARCHAR(50),
    FOREIGN KEY (member1_id) REFERENCES members(id),
    FOREIGN KEY (member2_id) REFERENCES members(id)
);
DELETE FROM relationships WHERE member1_id = ? OR member2_id = ?

CREATE TABLE relationships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member1_id INT NOT NULL,
    member2_id INT NOT NULL,
    relationship_type VARCHAR(50),
    FOREIGN KEY (member1_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (member2_id) REFERENCES members(id) ON DELETE CASCADE
);
