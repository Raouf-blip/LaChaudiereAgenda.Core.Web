SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Ajout de catégories
INSERT INTO categories (name, description) VALUES
('concert', 'Événements musicaux'),
('spectacle', 'Représentations théâtrales'),
('exposition', 'Expositions artistiques'),
('conférence', 'Conférences et débats');

-- Ajout d’utilisateurs
INSERT INTO users (id, email, password_hash, role) VALUES
(UUID(), 'alice@lachaudiere.org', '$2a$12$8t2LCvQwKM6WOn9Z3rxsH.op.wF.BVpgsQ6VVcQx3yc83rr./3iw.', 100),
(UUID(), 'bob@lachaudiere.org', '$2a$12$jaRb1PEEhGSpCojdUdsSx.PgDMhQaLCL2ZKJhg5tJAT0uSGL3qB9m', 50),
(UUID(), 'carole@lachaudiere.org', '$2a$12$q8R9yzAVJ6T.s3DwuN6MNe.9kaYfwvgHb6YjjlpIZYB5Q9aMlF9lq', 1);

-- Ajout d’images (avec vrais UUIDs)
INSERT INTO images (id, name) VALUES
(UUID(), 'concert-jazz.jpg'),
(UUID(), 'theatre-absurde.jpg'),
(UUID(), 'expo-peinture.jpg'),
(UUID(), 'conference-climat.jpg');

-- Ajout d’événements
INSERT INTO events (
    id, title, artist, description, start_date, end_date, start_time, end_time, price, image_id, category_id, created_by
) VALUES
-- Concert
(UUID(),
 'Soirée Jazz Impro',
 'Quintette Jazz de la Chaudière',
 'Un concert unique mêlant improvisation et standards du jazz contemporain.',
 '2025-06-20', '2025-06-20', '20:30:00', '23:00:00', 12.00,
 (SELECT id FROM images WHERE name = 'concert-jazz.jpg'),
 (SELECT id FROM categories WHERE name = 'concert'),
 NULL),

-- Spectacle
(UUID(),
 'Théâtre de l’Absurde',
 'Ionesco',
 'Une pièce originale inspirée de Ionesco, jouée par la troupe locale.',
 '2025-07-05', '2025-07-05', '19:00:00', '21:00:00', 8.00,
 (SELECT id FROM images WHERE name = 'theatre-absurde.jpg'),
 (SELECT id FROM categories WHERE name = 'spectacle'),
 NULL),

-- Exposition
(UUID(),
 'Regards croisés - Peintures',
'Collectif d’artistes locaux',
 'Exposition de jeunes artistes de la région sur le thème du corps et de l’espace.',
 '2025-06-01', '2025-06-30', NULL, NULL, 0.00,
 (SELECT id FROM images WHERE name = 'expo-peinture.jpg'),
 (SELECT id FROM categories WHERE name = 'exposition'),
 NULL),

-- Conférence
(UUID(),
 'Conférence : Climat et Résilience',
 'Marc Lemoine',
 'Intervention du chercheur Marc Lemoine sur l’adaptation climatique des territoires.',
 '2025-06-18', '2025-06-18', '18:00:00', '20:00:00', 5.00,
 (SELECT id FROM images WHERE name = 'conference-climat.jpg'),
 (SELECT id FROM categories WHERE name = 'conférence'),
 NULL),

 -- Concert
 (UUID(),
 'Festival de Musique du Monde',
    'Orchestre Mondial',
    'Un festival de musique du monde avec des artistes internationaux et locaux.',
    '2025-08-10', '2025-08-12', '15:00:00', '23:00:00', 20.00,
    (SELECT id FROM images WHERE name = 'concert-jazz.jpg'),
    (SELECT id FROM categories WHERE name = 'concert'),
    NULL),

-- Spectacle
(UUID(),
 'Improvisation Théâtrale',
 'La Compagnie Spontanée',
 'Un spectacle d’improvisation interactif avec participation du public.',
 '2025-09-15', '2025-09-15', '20:00:00', '22:00:00', 10.00,
 (SELECT id FROM images WHERE name = 'theatre-absurde.jpg'),
 (SELECT id FROM categories WHERE name = 'spectacle'),
 NULL),

-- Exposition
(UUID(),
 'Couleurs Urbaines',
 'Artistes de la Rue',
 'Une exposition de fresques, graffitis et photographies urbaines.',
 '2025-07-01', '2025-07-31', NULL, NULL, 0.00,
 (SELECT id FROM images WHERE name = 'expo-peinture.jpg'),
 (SELECT id FROM categories WHERE name = 'exposition'),
 NULL),

-- Conférence
(UUID(),
 'Vers une Économie Locale Résiliente',
 'Élodie Martin',
 'Une conférence participative sur les circuits courts et l’autonomie alimentaire.',
 '2025-10-03', '2025-10-03', '19:00:00', '21:00:00', 3.00,
 (SELECT id FROM images WHERE name = 'conference-climat.jpg'),
 (SELECT id FROM categories WHERE name = 'conférence'),
 NULL),

-- Concert
(UUID(),
 'Chants du Monde',
 'Voix Nomades',
 'Un voyage musical à travers les traditions vocales du monde entier.',
 '2025-07-20', '2025-07-20', '21:00:00', '23:30:00', 15.00,
 (SELECT id FROM images WHERE name = 'concert-jazz.jpg'),
 (SELECT id FROM categories WHERE name = 'concert'),
 NULL),

-- Exposition
(UUID(),
 'Fragments Végétaux',
 'Collectif Chlorophylle',
 'Installation artistique autour de la nature, des plantes et du vivant.',
 '2025-08-05', '2025-08-30', NULL, NULL, 2.00,
 (SELECT id FROM images WHERE name = 'expo-peinture.jpg'),
 (SELECT id FROM categories WHERE name = 'exposition'),
 NULL);




