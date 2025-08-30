-- Intelligent Pharmacy Management System
-- Demo Data
--
-- This script populates the database with a generous amount of sample data
-- to demonstrate the application's features.
--
-- To use: Import this file into your database AFTER running schema.sql.

-- Empty existing tables to prevent conflicts
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `inventory_transactions`;
TRUNCATE TABLE `advice_requests`;
TRUNCATE TABLE `bookings`;
TRUNCATE TABLE `wishlists`;
TRUNCATE TABLE `medicines`;
TRUNCATE TABLE `manufacturers`;
TRUNCATE TABLE `categories`;
SET FOREIGN_KEY_CHECKS = 1;


-- 1. Insert Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Pain Relief', 'pain-relief', 'Medications designed to alleviate pain, from headaches to muscle aches.'),
(2, 'Antibiotics', 'antibiotics', 'Drugs that fight bacterial infections. Prescription is usually required.'),
(3, 'Vitamins & Supplements', 'vitamins-supplements', 'Products to supplement your diet and support overall health.'),
(4, 'Skin Care', 'skin-care', 'Topical treatments for skin conditions like eczema, acne, and dryness.'),
(5, 'Allergy & Cold', 'allergy-cold', 'Remedies for seasonal allergies, coughs, and the common cold.');

-- 2. Insert Manufacturers
INSERT INTO `manufacturers` (`id`, `name`, `contact_details`) VALUES
(1, 'PharmaCorp Global', '123 Health St, Medica City, USA'),
(2, 'HealthWell Labs', '456 Wellness Ave, Biotech Park, Germany'),
(3, 'BioGen Solutions', '789 Cure Blvd, Research Triangle, UK'),
(4, 'MedLife Pharmaceuticals', '101 Remedy Lane, Pharma Valley, India');

-- 3. Insert Medicines
-- Note: Thumbnail URLs use a placeholder service.
INSERT INTO `medicines` (`id`, `name`, `slug`, `description`, `category_id`, `manufacturer_id`, `price`, `quantity_in_stock`, `min_stock`, `manufacture_date`, `expiry_date`, `batch_number`, `group_name`, `thumbnail_url`) VALUES
-- Pain Relief
(1, 'Ibuprofen 200mg', 'ibuprofen-200mg', 'Effective for reducing fever and relieving minor aches and pains.', 1, 1, 8.99, 150, 20, '2023-01-15', '2025-12-31', 'B200-11A', 'group one', 'https://picsum.photos/seed/ibuprofen/400/300'),
(2, 'Paracetamol 500mg', 'paracetamol-500mg', 'A common pain reliever and fever reducer. Gentle on the stomach.', 1, 2, 5.49, 200, 20, '2023-03-20', '2026-02-28', 'P500-22B', 'group one', 'https://picsum.photos/seed/paracetamol/400/300'),
(3, 'Aspirin 81mg (Low Dose)', 'aspirin-81mg-low-dose', 'Low-dose aspirin helps prevent heart attacks and strokes.', 1, 3, 12.99, 80, 15, '2022-11-01', '2024-10-31', 'A81-33C', 'group two', 'https://picsum.photos/seed/aspirin/400/300'),
(4, 'Naproxen Sodium 220mg', 'naproxen-sodium-220mg', 'All-day strong pain relief for muscle aches and arthritis.', 1, 4, 15.25, 0, 10, '2023-05-10', '2025-04-30', 'N220-44D', 'group three', 'https://picsum.photos/seed/naproxen/400/300'),
(5, 'Diclofenac Gel 1%', 'diclofenac-gel-1', 'Topical gel for targeted arthritis pain relief in joints.', 1, 1, 18.50, 40, 10, '2023-06-01', '2025-05-31', 'DG1-11E', 'group three', 'https://picsum.photos/seed/diclofenac/400/300'),

-- Antibiotics
(6, 'Amoxicillin 500mg', 'amoxicillin-500mg', 'A broad-spectrum antibiotic used for various bacterial infections.', 2, 2, 22.00, 75, 25, '2023-08-11', '2025-07-31', 'AMX500-22F', 'group one', 'https://picsum.photos/seed/amoxicillin/400/300'),
(7, 'Doxycycline 100mg', 'doxycycline-100mg', 'Used to treat bacterial pneumonia, acne, and other infections.', 2, 3, 35.50, 50, 15, '2023-09-01', '2024-08-31', 'DOX100-33G', 'group two', 'https://picsum.photos/seed/doxycycline/400/300'),
(8, 'Cephalexin 250mg', 'cephalexin-250mg', 'An antibiotic for treating urinary tract and respiratory infections.', 2, 4, 19.80, 12, 15, '2023-02-15', '2025-01-31', 'CEPH250-44H', 'group four', 'https://picsum.photos/seed/cephalexin/400/300'),

-- Vitamins & Supplements
(9, 'Vitamin D3 2000 IU', 'vitamin-d3-2000-iu', 'Supports bone health and immune function. Essential for all ages.', 3, 1, 14.00, 300, 30, '2023-01-01', '2026-12-31', 'VD3-11I', 'group one', 'https://picsum.photos/seed/vitamind/400/300'),
(10, 'Omega-3 Fish Oil', 'omega-3-fish-oil', 'High-potency fish oil for heart and brain health.', 3, 2, 25.99, 120, 20, '2023-07-20', '2025-06-30', 'O3-22J', 'group two', 'https://picsum.photos/seed/fishoil/400/300'),
(11, 'Multivitamin for Adults', 'multivitamin-for-adults', 'A complete daily multivitamin with key nutrients for adults.', 3, 4, 19.99, 180, 25, '2023-04-10', '2025-03-31', 'MVA-44K', 'group three', 'https://picsum.photos/seed/multivitamin/400/300'),
(12, 'Expired Vitamin B12', 'expired-vitamin-b12', 'This is an expired product for testing purposes.', 3, 3, 9.99, 30, 10, '2021-01-01', '2023-01-01', 'VB12-EXP', 'https://picsum.photos/seed/expired/400/300'),

-- Skin Care
(13, 'Hydrocortisone Cream 1%', 'hydrocortisone-cream-1', 'Relieves itching and inflammation from insect bites and eczema.', 4, 1, 7.29, 90, 15, '2023-03-01', '2025-02-28', 'HC1-11L', 'group one', 'https://picsum.photos/seed/hydrocortisone/400/300'),
(14, 'Clotrimazole Antifungal Cream', 'clotrimazole-antifungal-cream', 'Cures most athlete''s foot, jock itch, and ringworm.', 4, 2, 11.50, 60, 10, '2022-12-10', '2024-11-30', 'CLO-22M', 'group two', 'https://picsum.photos/seed/clotrimazole/400/300'),
(15, 'Salicylic Acid Acne Wash', 'salicylic-acid-acne-wash', 'A gentle face wash for treating and preventing acne.', 4, 3, 16.75, 5, 10, '2023-08-01', '2025-07-31', 'SAAW-33N', 'group five', 'https://picsum.photos/seed/acnewash/400/300'),

-- Allergy & Cold
(16, 'Loratadine 10mg', 'loratadine-10mg', '24-hour non-drowsy relief from indoor and outdoor allergy symptoms.', 5, 4, 14.99, 250, 30, '2023-02-01', '2026-01-31', 'LOR10-44O', 'group one', 'https://picsum.photos/seed/loratadine/400/300'),
(17, 'Cetirizine 10mg', 'cetirizine-10mg', 'Provides potent 24-hour relief from allergy symptoms.', 5, 1, 13.49, 8, 10, '2023-04-15', '2025-03-31', 'CET10-11P', 'group five', 'https://picsum.photos/seed/cetirizine/400/300'),
(18, 'Guaifenesin Cough Syrup', 'guaifenesin-cough-syrup', 'Expectorant that helps loosen phlegm and thin bronchial secretions.', 5, 2, 9.95, 110, 20, '2023-09-20', '2025-08-31', 'GCS-22Q', 'group two', 'https://picsum.photos/seed/coughsyrup/400/300'),
(19, 'Pseudoephedrine Nasal Decongestant', 'pseudoephedrine-nasal-decongestant', 'Provides powerful relief from nasal congestion and sinus pressure.', 5, 3, 11.20, 0, 15, '2023-06-01', '2024-05-31', 'PSEUDO-33R', 'group three', 'https://picsum.photos/seed/pseudoephedrine/400/300'),
(20, 'Zinc Lozenges', 'zinc-lozenges', 'Helps reduce the duration and severity of the common cold.', 5, 4, 6.50, 150, 20, '2023-08-01', '2026-07-31', 'ZINC-44S', 'group four', 'https://picsum.photos/seed/zinc/400/300');
