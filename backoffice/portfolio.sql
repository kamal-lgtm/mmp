-- Base complète pour le backoffice
CREATE DATABASE IF NOT EXISTS portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE portfolio;

DROP TABLE IF EXISTS media;
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS admin;

CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- admin: admin@admin.com / admin123 (MD5)
INSERT INTO admin (email, password) VALUES ('admin@admin.com', MD5('admin123'));

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL UNIQUE
);

INSERT INTO categories (nom) VALUES
('CORPORATE'),('E-SPORT'),('EVENT'),('MOTION DESIGN'),
('PRODUCTS'),('RSE'),('SHOOTING'),('SOCIAL MEDIA'),('SPORT');

CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titre VARCHAR(255) NOT NULL,
  categorie_id INT NOT NULL,
  annee VARCHAR(10),
  description TEXT,
  cover VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE CASCADE
);

INSERT INTO projects (titre, categorie_id, annee, description, cover) VALUES
('Projet Démo', 1, '2025', 'Démo multi-médias (photos et vidéos).', 'uploads/projet_1/cover_demo.jpg');

CREATE TABLE media (
  id INT AUTO_INCREMENT PRIMARY KEY,
  project_id INT NOT NULL,
  type ENUM('image','video') NOT NULL,
  path VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

INSERT INTO media (project_id, type, path) VALUES
(1,'image','uploads/projet_1/photo1.jpg'),
(1,'image','uploads/projet_1/photo2.jpg'),
(1,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ');
