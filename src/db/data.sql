-- Ajout de catégories
INSERT INTO categories (name) VALUES
('concert'),
('spectacle'),
('exposition'),
('conférence');

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
 NULL);
