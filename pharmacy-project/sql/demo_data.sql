--
-- Demo Data for Pharmacy Management System
--

-- Roles
INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'admin'),
(2, 'client');

-- Users
-- Passwords should be properly hashed in the application.
-- The hash below is a placeholder for 'password123' using PASSWORD_BCRYPT.
-- In a real app, you'd use password_hash('password123', PASSWORD_DEFAULT);
INSERT INTO `users` (`id`, `role_id`, `username`, `email`, `password_hash`, `first_name`, `last_name`) VALUES
(1, 1, 'admin', 'admin@pharmacy.com', '$2y$10$9.M4.W2bJ2.A5S.X/l3y3uI.Uq2C.j1jX2Y.u1jX2Y.u1jX2Y.u1', 'John', 'Doe'),
(2, 2, 'alice', 'alice@example.com', '$2y$10$9.M4.W2bJ2.A5S.X/l3y3uI.Uq2C.j1jX2Y.u1jX2Y.u1jX2Y.u1', 'Alice', 'Smith'),
(3, 2, 'bob', 'bob@example.com', '$2y$10$9.M4.W2bJ2.A5S.X/l3y3uI.Uq2C.j1jX2Y.u1jX2Y.u1jX2Y.u1', 'Bob', 'Johnson');


-- Categories
INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Pain Relief', 'Medications to relieve pain, such as headaches, muscle aches, and arthritis.'),
(2, 'Cold & Flu', 'Remedies for symptoms of the common cold and influenza.'),
(3, 'Allergy & Hay Fever', 'Antihistamines and other medications for allergic reactions.'),
(4, 'Vitamins & Supplements', 'Dietary supplements to support overall health.'),
(5, 'Digestive Health', 'Products for heartburn, indigestion, and other digestive issues.');

-- Manufacturers
INSERT INTO `manufacturers` (`id`, `name`, `contact_info`) VALUES
(1, 'Global Health Inc.', '123 Wellness Way, Pharma City'),
(2, 'MediCare Solutions', '456 Recovery Road, Healthburg'),
(3, 'BioPharm Ltd.', '789 Life Science Lane, Newton');

-- Medicines (Sample of 20)
-- Prices are just examples.
INSERT INTO `medicines` (`id`, `name`, `description`, `price`, `category_id`, `manufacturer_id`, `manufacturing_date`, `expiry_date`) VALUES
(1, 'Paracetamol 500mg', 'Effective relief from pain and fever.', 2.99, 1, 1, '2023-01-15', '2026-01-14'),
(2, 'Ibuprofen 200mg', 'Reduces inflammation and relieves pain.', 3.49, 1, 2, '2023-03-20', '2025-03-19'),
(3, 'Aspirin 300mg', 'For pain relief and as an anti-inflammatory.', 2.50, 1, 1, '2022-11-10', '2024-11-09'),
(4, 'Loratadine 10mg', 'Non-drowsy antihistamine for allergy relief.', 5.99, 3, 3, '2023-05-01', '2025-04-30'),
(5, 'Vitamin C 1000mg', 'Supports the immune system.', 8.99, 4, 1, '2023-08-01', '2025-07-31'),
(6, 'Gaviscon Advance', 'Fast-acting relief from heartburn and indigestion.', 7.25, 5, 2, '2023-02-11', '2025-02-10'),
(7, 'Cold & Flu Max Strength', 'All-in-one capsules for cold and flu symptoms.', 6.50, 2, 3, '2023-09-01', '2025-08-31'),
(8, 'Calcium + Vitamin D', 'For strong bones and teeth.', 12.00, 4, 1, '2023-04-10', '2026-04-09'),
(9, 'Cetirizine 10mg', 'One-a-day allergy relief.', 4.99, 3, 2, '2022-12-25', '2024-12-24'),
(10, 'Antiseptic Cream', 'Soothes and helps prevent infection.', 3.99, 5, 3, '2023-06-18', '2025-06-17'),
(11, 'Omega-3 Fish Oil', 'Supports heart and brain health.', 15.50, 4, 1, '2023-07-22', '2025-07-21'),
(12, 'Decongestant Nasal Spray', 'Clears blocked noses.', 4.75, 2, 2, '2023-10-05', '2025-10-04'),
(13, 'Probiotic Capsules', 'Supports a healthy gut microbiome.', 18.99, 5, 3, '2023-08-15', '2024-08-14'),
(14, 'Codeine Phosphate 30mg', 'Strong pain reliever for moderate to severe pain.', 9.99, 1, 1, '2023-01-30', '2025-01-29'),
(15, 'Fexofenadine 180mg', 'Prescription-strength non-drowsy antihistamine.', 11.50, 3, 2, '2023-04-20', '2026-04-19'),
(16, 'Multivitamin Gummies', 'Chewable daily multivitamin for adults.', 10.25, 4, 1, '2023-09-11', '2025-09-10'),
(17, 'Cough Syrup - Chesty', 'Expectorant to help clear chesty coughs.', 5.50, 2, 3, '2023-11-01', '2025-10-31'),
(18, 'Loperamide 2mg', 'For the relief of acute diarrhoea.', 3.80, 5, 2, '2023-03-03', '2026-03-02'),
(19, 'Naproxen 250mg', 'All-day relief from pain and inflammation.', 8.75, 1, 1, '2022-10-10', '2024-10-09'),
(20, 'Zinc Lozenges', 'May help reduce the duration of colds.', 6.00, 2, 3, '2023-09-25', '2025-09-24');

-- Medicine Images
-- In a real system, these paths would point to actual uploaded files.
INSERT INTO `medicine_images` (`medicine_id`, `image_url`, `is_primary`) VALUES
(1, 'uploads/medicine_images/paracetamol.jpg', 1),
(2, 'uploads/medicine_images/ibuprofen.jpg', 1),
(3, 'uploads/medicine_images/aspirin.jpg', 1),
(4, 'uploads/medicine_images/loratadine.jpg', 1),
(5, 'uploads/medicine_images/vitamin_c.jpg', 1),
(6, 'uploads/medicine_images/gaviscon.jpg', 1),
(7, 'uploads/medicine_images/cold_flu_max.jpg', 1),
(8, 'uploads/medicine_images/calcium_d.jpg', 1),
(9, 'uploads/medicine_images/cetirizine.jpg', 1),
(10, 'uploads/medicine_images/antiseptic_cream.jpg', 1),
(11, 'uploads/medicine_images/omega3.jpg', 1),
(12, 'uploads/medicine_images/nasal_spray.jpg', 1),
(13, 'uploads/medicine_images/probiotic.jpg', 1),
(14, 'uploads/medicine_images/codeine.jpg', 1),
(15, 'uploads/medicine_images/fexofenadine.jpg', 1),
(16, 'uploads/medicine_images/multivitamin_gummies.jpg', 1),
(17, 'uploads/medicine_images/cough_syrup.jpg', 1),
(18, 'uploads/medicine_images/loperamide.jpg', 1),
(19, 'uploads/medicine_images/naproxen.jpg', 1),
(20, 'uploads/medicine_images/zinc_lozenges.jpg', 1);


-- Stock
-- Assuming one stock record per medicine for simplicity.
INSERT INTO `stock` (`medicine_id`, `quantity`, `location`)
SELECT `id`, FLOOR(RAND() * 200) + 10, 'Aisle 1' FROM `medicines`;


-- Bookings
INSERT INTO `bookings` (`user_id`, `prescription_image_path`, `status`, `notes`) VALUES
(2, 'uploads/prescriptions/alice_rx_1.jpg', 'pending', 'Please check for generic availability.'),
(3, 'uploads/prescriptions/bob_rx_1.jpg', 'confirmed', 'Ready for pickup on Friday.');

-- Wishlist
INSERT INTO `wishlist` (`user_id`, `medicine_id`) VALUES
(2, 5), -- Alice wants Vitamin C
(2, 11), -- Alice wants Omega-3 Fish Oil
(3, 2);  -- Bob wants Ibuprofen

-- Settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'Jules Pharmacy'),
('site_contact_email', 'contact@julespharmacy.com'),
('low_stock_threshold', '20');
