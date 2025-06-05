-- Ajout de catégories
INSERT INTO categories (id, name) VALUES
(UUID(), 'concert'),
(UUID(), 'spectacle'),
(UUID(), 'exposition'),
(UUID(), 'conférence');

-- Ajout d’utilisateurs
INSERT INTO users (id, email, password_hash, role) VALUES
(UUID(), 'alice@lachaudiere.org', 'hashed_password_1', '100'),
(UUID(), 'bob@lachaudiere.org', 'hashed_password_2', '50'),
(UUID(), 'carole@lachaudiere.org', 'hashed_password_3', '1');

INSERT INTO images (id, name) VALUES
(1001, 'concert-jazz.jpg'),
(1002, 'theatre-absurde.jpg'),
(1003, 'expo-peinture.jpg'),
(1004, 'conference-climat.jpg');


-- Ajout d’événements
INSERT INTO events (
    id, title, description, start_date, end_date, start_time, end_time, price, image_id, category_id,
) VALUES
-- Concert
('00000000-0000-0000-0000-000000000001',
 'Soirée Jazz Impro',
 'Un concert unique mêlant improvisation et standards du jazz contemporain.',
 '2025-06-20', '2025-06-20', '20:30:00', '23:00:00', 12.00,
 '00000000-0000-0000-0000-000000001001',
 (SELECT id FROM categories WHERE name = 'concert')),


-- Spectacle
('00000000-0000-0000-0000-000000000002',
 'Théâtre de l’Absurde',
 'Une pièce originale inspirée de Ionesco, jouée par la troupe locale.',
 '2025-07-05', '2025-07-05', '19:00:00', '21:00:00', 8.00,
 '00000000-0000-0000-0000-000000001002',
 (SELECT id FROM categories WHERE name = 'spectacle')),

-- Exposition
('00000000-0000-0000-0000-000000000003',
 'Regards croisés - Peintures',
 'Exposition de jeunes artistes de la région sur le thème du corps et de l’espace.',
 '2025-06-01', '2025-06-30', NULL, NULL, 0.00,
 '00000000-0000-0000-0000-000000001003',
 (SELECT id FROM categories WHERE name = 'exposition')),

-- Conférence
('00000000-0000-0000-0000-000000000004',
 'Conférence : Climat et Résilience',
 'Intervention du chercheur Marc Lemoine sur l’adaptation climatique des territoires.',
 '2025-06-18', '2025-06-18', '18:00:00', '20:00:00', 5.00,
 '00000000-0000-0000-0000-000000001004',
 (SELECT id FROM categories WHERE name = 'conférence')),