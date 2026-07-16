-- ---------------------------------------------------------------------------
-- Matrimonial Hub — demo data
--
--   mysql -u root -p matrimonial < database/seed.sql
--
-- Every member's password is  password123
-- The administrator is        admin / admin123
--
-- CHANGE BOTH BEFORE DEPLOYING ANYWHERE REAL.
--
-- These people are invented. The original project's SQL dump shipped with its
-- authors' real names, real email addresses, real national ID numbers and real
-- photographs committed to a public repository; none of that is carried over.
-- ---------------------------------------------------------------------------

USE `matrimonial`;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `messages`;
TRUNCATE TABLE `connection_requests`;
TRUNCATE TABLE `preferences`;
TRUNCATE TABLE `profiles`;
TRUNCATE TABLE `activity_log`;
TRUNCATE TABLE `users`;
TRUNCATE TABLE `admins`;
SET FOREIGN_KEY_CHECKS = 1;

-- Administrator ------------------------------------------------------------
-- bcrypt hash of 'admin123'. The original stored 'password1' in plaintext and
-- compared it inside the SQL WHERE clause.

INSERT INTO `admins` (`username`, `password_hash`) VALUES
('admin', '$2y$10$jt3zJusUwWFr289VDTsj9erVBQ0NY6c9SgkD1giyLytL70YGDxOGG');

-- Members ------------------------------------------------------------------
-- All share the bcrypt hash of 'password123'.

INSERT INTO `users`
    (`user_id`, `email`, `password_hash`, `first_name`, `middle_name`, `last_name`, `dob`,
     `gender`, `religion`, `ethnicity`, `profession`, `nid`, `photo`, `account_status`, `last_seen_at`, `created_at`)
VALUES
('nusrat00001', 'nusrat@example.test',  '$2y$10$Q3u63Z4ZY8TkdZ5u7iVfsuV5mzSB80jf6i5x7csage2OZKNfyOkN6', 'Nusrat', NULL,   'Jahan',    '1997-03-14', 'Female', 'Muslim',    'Bengali',     'architect',            'DEMO-1001', NULL, 'Active', NOW(), NOW() - INTERVAL 40 DAY),
('tanvir00002', 'tanvir@example.test',  '$2y$10$Q3u63Z4ZY8TkdZ5u7iVfsuV5mzSB80jf6i5x7csage2OZKNfyOkN6', 'Tanvir', 'Ahmed', 'Hasan',    '1994-08-02', 'Male',   'Muslim',    'Bengali',     'software-engineer',    'DEMO-1002', NULL, 'Active', NOW() - INTERVAL 5 MINUTE, NOW() - INTERVAL 38 DAY),
('rupa000003',  'rupa@example.test',    '$2y$10$Q3u63Z4ZY8TkdZ5u7iVfsuV5mzSB80jf6i5x7csage2OZKNfyOkN6', 'Rupa',   NULL,    'Chowdhury','1996-11-25', 'Female', 'Hindu',     'Bengali',     'doctor',               'DEMO-1003', NULL, 'Active', NOW() - INTERVAL 2 HOUR, NOW() - INTERVAL 35 DAY),
('imran000004', 'imran@example.test',   '$2y$10$Q3u63Z4ZY8TkdZ5u7iVfsuV5mzSB80jf6i5x7csage2OZKNfyOkN6', 'Imran',  NULL,    'Kabir',    '1992-01-19', 'Male',   'Muslim',    'Bengali',     'civil-engineer',       'DEMO-1004', NULL, 'Active', NOW() - INTERVAL 1 DAY,  NOW() - INTERVAL 33 DAY),
('sadia000005', 'sadia@example.test',   '$2y$10$Q3u63Z4ZY8TkdZ5u7iVfsuV5mzSB80jf6i5x7csage2OZKNfyOkN6', 'Sadia',  'Binte', 'Rahman',   '1999-06-30', 'Female', 'Muslim',    'Bengali',     'university-professor', 'DEMO-1005', NULL, 'Active', NOW() - INTERVAL 3 DAY,  NOW() - INTERVAL 30 DAY),
('arif000006',  'arif@example.test',    '$2y$10$Q3u63Z4ZY8TkdZ5u7iVfsuV5mzSB80jf6i5x7csage2OZKNfyOkN6', 'Arif',   NULL,    'Mahmud',   '1990-09-09', 'Male',   'Muslim',    'Bengali',     'entrepreneur',         'DEMO-1006', NULL, 'Active', NOW() - INTERVAL 8 HOUR, NOW() - INTERVAL 28 DAY),
('priya000007', 'priya@example.test',   '$2y$10$Q3u63Z4ZY8TkdZ5u7iVfsuV5mzSB80jf6i5x7csage2OZKNfyOkN6', 'Priya',  NULL,    'Das',      '1998-02-11', 'Female', 'Hindu',     'Bengali',     'graphic-designer',     'DEMO-1007', NULL, 'Active', NOW() - INTERVAL 20 MINUTE, NOW() - INTERVAL 25 DAY),
('shuvo000008',  'shuvo@example.test',  '$2y$10$Q3u63Z4ZY8TkdZ5u7iVfsuV5mzSB80jf6i5x7csage2OZKNfyOkN6', 'Shuvo',  NULL,    'Barua',    '1995-12-05', 'Male',   'Buddhist',  'Bengali',     'data-scientist',       'DEMO-1008', NULL, 'Active', NOW() - INTERVAL 2 DAY,  NOW() - INTERVAL 22 DAY),
('meherun00009', 'meherun@example.test','$2y$10$Q3u63Z4ZY8TkdZ5u7iVfsuV5mzSB80jf6i5x7csage2OZKNfyOkN6', 'Meherun', NULL,   'Nesa',     '1993-04-21', 'Female', 'Muslim',    'Bengali',     'lawyer',               'DEMO-1009', NULL, 'Active', NOW() - INTERVAL 6 DAY,  NOW() - INTERVAL 20 DAY),
('rafi000010',  'rafi@example.test',    '$2y$10$Q3u63Z4ZY8TkdZ5u7iVfsuV5mzSB80jf6i5x7csage2OZKNfyOkN6', 'Rafi',   NULL,    'Islam',    '2000-07-17', 'Male',   'Muslim',    'Bengali',     'ux-ui-designer',       'DEMO-1010', NULL, 'Active', NOW() - INTERVAL 30 MINUTE, NOW() - INTERVAL 18 DAY),
('anika000011', 'anika@example.test',   '$2y$10$Q3u63Z4ZY8TkdZ5u7iVfsuV5mzSB80jf6i5x7csage2OZKNfyOkN6', 'Anika',  NULL,    'Tabassum', '1991-10-08', 'Female', 'Muslim',    'Bengali',     'pharmacist',           'DEMO-1011', NULL, 'Active', NOW() - INTERVAL 4 DAY,  NOW() - INTERVAL 15 DAY),
('joseph00012', 'joseph@example.test',  '$2y$10$Q3u63Z4ZY8TkdZ5u7iVfsuV5mzSB80jf6i5x7csage2OZKNfyOkN6', 'Joseph', NULL,    'Gomes',    '1989-05-03', 'Male',   'Christian', 'Bengali',     'chef',                 'DEMO-1012', NULL, 'Active', NOW() - INTERVAL 9 DAY,  NOW() - INTERVAL 12 DAY),
('farhana00013','farhana@example.test', '$2y$10$Q3u63Z4ZY8TkdZ5u7iVfsuV5mzSB80jf6i5x7csage2OZKNfyOkN6', 'Farhana', NULL,   'Yasmin',   '1996-08-27', 'Female', 'Muslim',    'South Asian', 'teacher',              'DEMO-1013', NULL, 'Active', NOW() - INTERVAL 1 HOUR, NOW() - INTERVAL 9 DAY),
('sabbir00014', 'sabbir@example.test',  '$2y$10$Q3u63Z4ZY8TkdZ5u7iVfsuV5mzSB80jf6i5x7csage2OZKNfyOkN6', 'Sabbir', NULL,    'Ahmed',    '1993-02-14', 'Male',   'Muslim',    'Bengali',     'doctor',               'DEMO-1014', NULL, 'Active', NOW() - INTERVAL 12 HOUR, NOW() - INTERVAL 6 DAY),
-- One suspended and one inactive account, so the admin console has something
-- other than a wall of green to show.
('kamal000015', 'kamal@example.test',   '$2y$10$Q3u63Z4ZY8TkdZ5u7iVfsuV5mzSB80jf6i5x7csage2OZKNfyOkN6', 'Kamal',  NULL,    'Uddin',    '1988-03-30', 'Male',   'Muslim',    'Bengali',     'real-estate-agent',    'DEMO-1015', NULL, 'Suspended', NOW() - INTERVAL 20 DAY, NOW() - INTERVAL 26 DAY),
('tania000016', 'tania@example.test',   '$2y$10$Q3u63Z4ZY8TkdZ5u7iVfsuV5mzSB80jf6i5x7csage2OZKNfyOkN6', 'Tania',  NULL,    'Akter',    '1997-01-06', 'Female', 'Muslim',    'Bengali',     'journalist',           'DEMO-1016', NULL, 'Inactive',  NOW() - INTERVAL 60 DAY, NOW() - INTERVAL 31 DAY);

-- Profiles -----------------------------------------------------------------

INSERT INTO `profiles`
    (`user_id`, `phone`, `road_number`, `street_number`, `building_number`,
     `secondary_education`, `higher_secondary`, `undergraduate`, `postgraduate`,
     `marital_status`, `height_cm`, `weight_kg`, `complexion`,
     `interests`, `hobbies`, `biography`, `family_background`)
VALUES
('nusrat00001', '01711-000001', '12', '4', '9B', 'Viqarunnisa Noon School', 'Holy Cross College', 'BArch', 'MArch', 'Single', 163.00, 54.00, 'Fair',
 'architecture, reading, travelling', 'sketching, cooking, cycling',
 'I design schools and community buildings, mostly outside Dhaka. Weekends are for the drawing board or a road trip with terrible playlists. I am looking for someone kind, curious, and unbothered by a partner who notices bad ceilings.',
 'Middle-class family from Dhaka. One younger brother, both parents retired teachers.'),

('tanvir00002', '01711-000002', '3', '11', '4A', 'Notre Dame School', 'Notre Dame College', 'BSc', 'MSc', 'Single', 176.00, 72.00, 'Medium',
 'technology, reading, football', 'coding, cricket, photography',
 'Backend engineer, six years in. I like problems that stay solved. Off the clock I am usually behind a camera or arguing about the Premier League.',
 'Middle-class family from Chattogram. Father a retired banker, mother a homemaker.'),

('rupa000003', '01711-000003', '7', '2', '15', 'Holy Cross School', 'Holy Cross College', 'MBBS', 'None', 'Single', 160.00, 52.00, 'Fair',
 'medicine, music, travelling', 'singing, gardening, reading',
 'Paediatrician at a public hospital. Long shifts, short temper before chai. I want a partner who understands why I will occasionally cancel dinner for a sick child.',
 'Hindu family from Sylhet, close-knit. Two sisters, both doctors.'),

('imran000004', '01711-000004', '21', '6', '2C', 'Government Laboratory High School', 'Dhaka College', 'BEng', 'MSc', 'Single', 180.00, 78.00, 'Medium',
 'engineering, travelling, food', 'hiking, cooking, cricket',
 'I build bridges, which is less romantic than it sounds and involves a lot of spreadsheets. I cook properly, though, and I would like someone to cook for.',
 'Large family from Rajshahi. Four siblings, all still argue at every eid.'),

('sadia000005', '01711-000005', '9', '8', '7D', 'Rajuk Uttara Model College', 'Rajuk Uttara Model College', 'BA', 'MA', 'Single', 158.00, 50.00, 'Fair',
 'literature, teaching, reading', 'reading, writing, painting',
 'I teach English literature and I am unapologetic about it. I would like to meet someone who reads — anything at all, I am not precious about what.',
 'Academic family from Dhaka. Both parents professors; dinner is a seminar.'),

('arif000006', '01711-000006', '15', '3', '11A', 'Ideal School', 'Dhaka City College', 'BBA', 'MBA', 'Divorced', 178.00, 80.00, 'Medium',
 'business, food, travelling', 'cooking, cycling, football',
 'I run a small logistics company. Divorced, no children, no drama — it simply did not work, and we were both honest about it. Ready to try again with my eyes open.',
 'Business family from Dhaka. Parents both alive, one older sister in Canada.'),

('priya000007', '01711-000007', '5', '9', '3B', 'Bangladesh Mahila Samity School', 'Dhaka City College', 'BA', 'None', 'Single', 162.00, 53.00, 'Medium',
 'design, art, music', 'painting, photography, travelling',
 'Graphic designer, mostly for NGOs. I care a lot about colour and very little about status. Looking for someone gentle who has their own thing going on.',
 'Hindu family from Khulna. Parents run a small pharmacy; one older brother.'),

('shuvo000008', '01711-000008', '18', '1', '6', 'Chittagong Collegiate School', 'Chittagong College', 'BSc', 'MSc', 'Single', 174.00, 70.00, 'Medium',
 'data science, technology, reading', 'coding, cycling, cooking',
 'Data scientist. I am happiest with a hard question and a long deadline. Buddhist, not strictly practising, and completely open about it.',
 'Barua family from Chattogram. Quiet household, lots of books.'),

('meherun00009', '01711-000009', '2', '14', '8C', 'Viqarunnisa Noon School', 'Viqarunnisa Noon College', 'LLB', 'MA', 'Single', 165.00, 57.00, 'Olive',
 'law, reading, politics', 'reading, debating, travelling',
 'Family lawyer. I spend my days on other people''s marriages, which has made me careful rather than cynical. I argue for a living; I promise not to at home.',
 'Middle-class family from Dhaka. Father a retired civil servant.'),

('rafi000010', '01711-000010', '11', '7', '1A', 'St. Joseph School', 'Notre Dame College', 'BSc', 'None', 'Single', 172.00, 65.00, 'Fair',
 'design, technology, music', 'photography, gaming, cycling',
 'Product designer, four years in. Youngest here by some margin and fine with that. I want someone I can build a life with slowly rather than at once.',
 'Small family from Dhaka. One sister, parents both in education.'),

('anika000011', '01711-000011', '6', '5', '12', 'Holy Cross School', 'Holy Cross College', 'BSc', 'MSc', 'Widowed', 161.00, 55.00, 'Fair',
 'medicine, reading, gardening', 'gardening, cooking, reading',
 'Pharmacist. Widowed four years ago; it was a good marriage and I would like another one. Patient, practical, and very hard to rush.',
 'Middle-class family from Dhaka. One daughter, seven years old, who comes first.'),

('joseph00012', '01711-000012', '4', '10', '5B', 'St. Gregory School', 'Notre Dame College', 'Diploma', 'None', 'Single', 175.00, 82.00, 'Medium',
 'culinary arts, food, travelling', 'cooking, cycling, music',
 'Head chef at a restaurant in Banani. I work when everyone else eats, so my weekends are Mondays. Catholic family, easy about faith.',
 'Christian family from Barishal. Big, loud, always feeding someone.'),

('farhana00013', '01711-000013', '8', '12', '10D', 'Sylhet Government School', 'MC College', 'BA', 'MA', 'Single', 159.00, 51.00, 'Fair',
 'teaching, literature, travelling', 'reading, cooking, singing',
 'Primary school teacher. Thirty children a day means I have run out of patience for games and have plenty left for people who are straightforward.',
 'Modest family from Sylhet. Parents both alive; two younger brothers at university.'),

('sabbir00014', '01711-000014', '17', '2', '3', 'Adamjee Cantonment College', 'Adamjee Cantonment College', 'MBBS', 'MSc', 'Single', 177.00, 74.00, 'Medium',
 'medicine, football, reading', 'cricket, reading, travelling',
 'Cardiologist. Long hours, and I will not pretend otherwise. What I can offer is someone who shows up when it counts.',
 'Middle-class family from Dhaka. Father a doctor too; the pressure was considerable.'),

('kamal000015', '01711-000015', '30', '1', '20', 'Local High School', 'Local College', 'BBA', 'None', 'Single', 170.00, 85.00, 'Medium',
 'property, business', 'cricket',
 'Property dealer. Best rates in Dhaka, message me for offers.',
 'Business family.'),

('tania000016', '01711-000016', '13', '6', '7', 'Viqarunnisa Noon School', 'Dhaka City College', 'BA', 'None', 'Single', 164.00, 56.00, 'Fair',
 'journalism, politics, reading', 'writing, photography',
 'Reporter. Currently taking a break from the site.',
 'Middle-class family from Dhaka.');

-- Preferences --------------------------------------------------------------
-- Set for a handful of members so match scores have something to work from
-- the moment you sign in.

INSERT INTO `preferences`
    (`user_id`, `preferred_gender`, `preferred_religion`, `preferred_ethnicity`, `preferred_profession`,
     `preferred_marital_status`, `preferred_education`, `min_age`, `max_age`, `min_height_cm`, `max_height_cm`,
     `interests`, `hobbies`)
VALUES
('nusrat00001', 'Male',   'Muslim',    'Bengali', 'software-engineer', 'Single', 'MSc',  27, 34, 170.00, 190.00, 'technology, reading, travelling', 'cooking, cycling, photography'),
('tanvir00002', 'Female', 'Muslim',    'Bengali', 'architect',         'Single', 'MArch', 24, 32, 150.00, 172.00, 'architecture, reading, food',    'cooking, sketching'),
('rupa000003',  'Male',   'Hindu',     'Bengali', 'data-scientist',    'Single', 'MSc',  26, 35, 168.00, 190.00, 'music, travelling, reading',     'singing, cooking'),
('imran000004', 'Female', 'Muslim',    'Bengali', 'teacher',           'Single', 'MA',   24, 32, 150.00, 170.00, 'travelling, food, literature',   'cooking, reading'),
('sadia000005', 'Male',   'Muslim',    'Bengali', 'university-professor','Single','MSc', 26, 36, 168.00, 190.00, 'literature, reading',            'reading, writing'),
('priya000007', 'Male',   'Hindu',     'Bengali', 'graphic-designer',  'Single', 'BA',   25, 34, 165.00, 185.00, 'art, design, music',             'painting, photography'),
('shuvo000008', 'Female', 'Buddhist',  'Bengali', NULL,                'Single', NULL,   24, 33, 150.00, 172.00, 'reading, technology',            'cooking, cycling'),
('rafi000010',  'Female', 'Muslim',    'Bengali', 'graphic-designer',  'Single', 'BA',   22, 30, 150.00, 170.00, 'design, music, technology',      'photography, gaming'),
('sabbir00014', 'Female', 'Muslim',    'Bengali', 'doctor',            'Single', 'MBBS', 25, 33, 152.00, 172.00, 'medicine, reading',              'reading, travelling'),
('meherun00009','Male',   'Muslim',    'Bengali', 'lawyer',            'Single', 'LLB',  28, 40, 168.00, 190.00, 'reading, politics',              'reading, debating'),
-- Members who have not set preferences yet, so the "set your preferences"
-- prompt has somewhere to appear.
('arif000006',  NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('anika000011', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('joseph00012', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('farhana00013',NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('kamal000015', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('tania000016', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- Connection requests ------------------------------------------------------
-- A mix of every status, so each tab in the UI has content.

INSERT INTO `connection_requests` (`sender_id`, `receiver_id`, `status`, `message`, `created_at`, `responded_at`) VALUES
-- Accepted, and talking (see messages below)
('tanvir00002', 'nusrat00001', 'Accepted', 'Your bio made me laugh — the bad ceilings line. I would like to hear more.', NOW() - INTERVAL 9 DAY, NOW() - INTERVAL 8 DAY),
-- Accepted, not yet talking
('nusrat00001', 'shuvo000008', 'Accepted', NULL, NOW() - INTERVAL 6 DAY, NOW() - INTERVAL 5 DAY),
('rupa000003',  'shuvo000008', 'Accepted', 'A fellow long-hours person. Solidarity.', NOW() - INTERVAL 4 DAY, NOW() - INTERVAL 3 DAY),
-- Pending, waiting on Nusrat — this is what you see in her Requests tab
('rafi000010',  'nusrat00001', 'Pending',  'I am a designer too. Would love to talk shop and then not talk shop at all.', NOW() - INTERVAL 2 DAY, NULL),
('imran000004', 'nusrat00001', 'Pending',  'You build them, I hold them up. Coffee?', NOW() - INTERVAL 20 HOUR, NULL),
-- Pending, sent by Nusrat — her Sent tab
('nusrat00001', 'sabbir00014', 'Pending',  NULL, NOW() - INTERVAL 1 DAY, NULL),
-- Other members' traffic, so the admin numbers are not all from one person
('sabbir00014', 'rupa000003',  'Pending',  'Fellow doctor. I promise not to talk about work. I will talk about work.', NOW() - INTERVAL 3 DAY, NULL),
('arif000006',  'meherun00009','Declined', NULL, NOW() - INTERVAL 10 DAY, NOW() - INTERVAL 9 DAY),
('joseph00012', 'priya000007', 'Pending',  'I cook. That is my whole pitch.', NOW() - INTERVAL 5 DAY, NULL),
('kamal000015', 'farhana00013','Declined', 'Best property rates, also marriage.', NOW() - INTERVAL 12 DAY, NOW() - INTERVAL 12 DAY),
('rafi000010',  'priya000007', 'Accepted', 'Designer solidarity?', NOW() - INTERVAL 7 DAY, NOW() - INTERVAL 7 DAY),
('shuvo000008', 'farhana00013','Cancelled', NULL, NOW() - INTERVAL 8 DAY, NOW() - INTERVAL 7 DAY);

-- Messages -----------------------------------------------------------------
-- Stored as plain text. The original encrypted these with AES-128-ECB under a
-- hardcoded placeholder key committed to the repo, which provided no
-- confidentiality while permanently corrupting any apostrophe.

INSERT INTO `messages` (`sender_id`, `receiver_id`, `body`, `read_at`, `created_at`) VALUES
('tanvir00002', 'nusrat00001', 'Hi Nusrat — thanks for accepting. I have to ask: which ceiling in Dhaka offends you most?', NOW() - INTERVAL 8 DAY, NOW() - INTERVAL 8 DAY),
('nusrat00001', 'tanvir00002', 'The airport arrivals hall, easily. It is a beautiful roof doing nothing at all.', NOW() - INTERVAL 8 DAY, NOW() - INTERVAL 8 DAY + INTERVAL 12 MINUTE),
('tanvir00002', 'nusrat00001', 'That is a strong opening position. I will never look at it the same way.', NOW() - INTERVAL 8 DAY, NOW() - INTERVAL 8 DAY + INTERVAL 20 MINUTE),
('nusrat00001', 'tanvir00002', 'Good. So — six years of backend. What is the thing you are quietly proud of?', NOW() - INTERVAL 7 DAY, NOW() - INTERVAL 7 DAY),
('tanvir00002', 'nusrat00001', 'A payment system that has not gone down in three years. Nobody notices it, which is the point.', NOW() - INTERVAL 7 DAY, NOW() - INTERVAL 7 DAY + INTERVAL 8 MINUTE),
('nusrat00001', 'tanvir00002', 'That is exactly how I feel about a good staircase. Invisible when it works.', NOW() - INTERVAL 7 DAY, NOW() - INTERVAL 7 DAY + INTERVAL 15 MINUTE),
('tanvir00002', 'nusrat00001', 'Are you free Saturday? There is a place in Dhanmondi with a genuinely good roof.', NULL, NOW() - INTERVAL 2 HOUR),
('tanvir00002', 'nusrat00001', 'No pressure either way.', NULL, NOW() - INTERVAL 118 MINUTE),
('rafi000010', 'priya000007', 'Hey! Loved your NGO campaign work — the one with the jute textures.', NOW() - INTERVAL 6 DAY, NOW() - INTERVAL 7 DAY),
('priya000007', 'rafi000010', 'Thank you! That one took four rounds to get right. What are you working on?', NOW() - INTERVAL 6 DAY, NOW() - INTERVAL 6 DAY);

-- Activity log -------------------------------------------------------------

INSERT INTO `activity_log` (`user_id`, `activity`, `ip_address`, `created_at`) VALUES
('nusrat00001', 'Account created',             '127.0.0.1', NOW() - INTERVAL 40 DAY),
('tanvir00002', 'Account created',             '127.0.0.1', NOW() - INTERVAL 38 DAY),
('tanvir00002', 'Sent a connection request',   '127.0.0.1', NOW() - INTERVAL 9 DAY),
('nusrat00001', 'Accepted a connection',       '127.0.0.1', NOW() - INTERVAL 8 DAY),
('rafi000010',  'Sent a connection request',   '127.0.0.1', NOW() - INTERVAL 7 DAY),
('priya000007', 'Accepted a connection',       '127.0.0.1', NOW() - INTERVAL 7 DAY),
('meherun00009','Declined a connection',       '127.0.0.1', NOW() - INTERVAL 9 DAY),
('farhana00013','Declined a connection',       '127.0.0.1', NOW() - INTERVAL 12 DAY),
('nusrat00001', 'Signed in',                   '127.0.0.1', NOW() - INTERVAL 2 HOUR),
('tanvir00002', 'Signed in',                   '127.0.0.1', NOW() - INTERVAL 5 MINUTE);
