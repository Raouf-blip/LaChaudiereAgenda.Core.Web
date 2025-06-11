DROP DATABASE IF EXISTS lachaudiere;
CREATE DATABASE IF NOT EXISTS lachaudiere;
USE lachaudiere;

-- Table des utilisateurs
CREATE TABLE users (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role INT NOT NULL
);

-- Table des catégories
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Table des images
CREATE TABLE images (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    name VARCHAR(255) NOT NULL
);

-- Table des événements
CREATE TABLE events (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    title VARCHAR(100) NOT NULL,
    artist VARCHAR(100),
    description TEXT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    start_time TIME,
    end_time TIME,
    price DECIMAL(10,2),
    image_id CHAR(36),
    category_id INT NOT NULL,
    created_by CHAR(36),
    is_published BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (image_id) REFERENCES images(id),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);
