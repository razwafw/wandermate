-- Dummy data for roles
INSERT INTO roles (id, name)
VALUES (1, 'customer'),
       (2, 'admin');

-- Dummy data for statuses
INSERT INTO statuses (id, name)
VALUES (1, 'rejected'),
       (2, 'accepted');

-- Dummy data for users
INSERT INTO users (id, name, email, phone, password, role_id)
VALUES (1, 'Alice Smith', 'alice@example.com', '081234567890', 'password1', 1),
       (2, 'Bob Johnson', 'bob@example.com', '081234567891', 'password2', 1),
       (3, 'Charlie Admin', 'charlie@admin.com', '081234567892', 'adminpass', 2);

-- Dummy data for packages
INSERT INTO packages (id, name, subtitle, price, group_size, duration, start_location, end_location,
                      description, highlights, includes, excludes, itinerary)
VALUES (1001, 'Bali Adventure', 'Explore Bali', 4500, 10, '3 days', 'Denpasar', 'Ubud',
        'A fun trip to Bali', 'Beaches\nTemples\nRice Terraces', 'Guide\nMeals\nTransport',
        'Flights\nPersonal expenses',
        'Day 1: Arrival | Arrive in Bali, check-in at hotel\nDay 2: Beach Tour | Visit Kuta and Seminyak beaches\nDay 3: Ubud Tour | Explore Ubud and rice terraces'),
       (1002, 'Yogyakarta Heritage', 'Culture & History', 2560, 8, '2 days', 'Yogyakarta', 'Borobudur',
        'Discover Yogyakarta', 'Borobudur\nPrambanan\nMalioboro', 'Guide\nTickets\nBreakfast',
        'Lunch\nPersonal expenses',
        'Day 1: City Tour | Explore Yogyakarta city\nDay 2: Temple Tour | Visit Borobudur and Prambanan'),
       (1003, 'Lombok Escape', 'Nature & Relax', 4800, 12, '3 days', 'Mataram', 'Gili Trawangan',
        'Relax in Lombok', 'Beaches\nSnorkeling\nWaterfalls', 'Hotel\nBreakfast\nGuide', 'Lunch\nDrinks',
        'Day 1: Beach Day | Relax at Senggigi Beach\nDay 2: Snorkeling | Snorkeling trip to Gili Islands\nDay 3: Waterfall Visit | Visit Sendang Gile waterfall'),
       (1004, 'Komodo Expedition', 'Wildlife Adventure', 10500, 15, '4 days', 'Labuan Bajo', 'Komodo Island',
        'See Komodo dragons', 'Komodo Island\nPink Beach\nPadar Island', 'Boat\nMeals\nGuide',
        'Flights\nEntrance fees',
        'Day 1: Sailing | Depart from Labuan Bajo\nDay 2: Komodo Island | Trekking and dragon spotting\nDay 3: Pink Beach | Snorkeling and relaxing\nDay 4: Padar Island | Sunrise hike and return'),
       (1005, 'Bandung Culinary', 'Food & Fun', 1500, 6, '2 days', 'Bandung', 'Lembang',
        'Taste Bandung', 'Street Food\nMarkets\nCoffee Shops', 'Guide\nMeals\nTransport', 'Drinks\nPersonal expenses',
        'Day 1: Food Tour | Explore Bandung street food\nDay 2: Lembang | Visit Lembang and local markets'),
       (1006, 'Jakarta City Tour', 'Urban Experience', 2400, 20, '1 day', 'Jakarta', 'Jakarta',
        'Explore Jakarta', 'Monas\nKota Tua\nShopping Malls', 'Guide\nTickets\nTransport', 'Meals\nPersonal expenses',
        'Day 1: City Tour | Visit Monas, Kota Tua, and malls'),
       (1007, 'Raja Ampat Diving', 'Underwater Paradise', 9600, 8, '5 days', 'Sorong', 'Waisai',
        'Dive in Raja Ampat', 'Coral Reefs\nTropical Fish\nIsland Hopping', 'Diving\nMeals\nBoat',
        'Flights\nEquipment rental',
        'Day 1: Arrival | Arrive in Sorong, transfer to Waisai\nDay 2: Diving | First day of diving\nDay 3: Island Hopping | Explore nearby islands\nDay 4: Diving | More diving sessions\nDay 5: Departure | Return to Sorong'),
       (1008, 'Sumatra Wildlife', 'Jungle Trek', 3500, 10, '3 days', 'Medan', 'Bukit Lawang',
        'See orangutans', 'Jungle Trek\nWildlife\nRiver Tubing', 'Guide\nMeals\nAccommodation',
        'Drinks\nPersonal expenses',
        'Day 1: Trek Start | Begin jungle trek\nDay 2: Wildlife Spotting | Search for orangutans\nDay 3: River Tubing | Enjoy tubing and return'),
       (1009, 'Bromo Sunrise', 'Mountain Adventure', 4480, 16, '2 days', 'Malang', 'Bromo',
        'Climb Bromo', 'Sunrise View\nJeep Tour\nCrater Hike', 'Guide\nTransport\nBreakfast',
        'Meals\nPersonal expenses',
        'Day 1: Sunrise Tour | Early morning jeep to Bromo\nDay 2: Crater Hike | Hike to Bromo crater and return'),
       (1010, 'Labuan Bajo Leisure', 'Island Hopping', 8400, 14, '3 days', 'Labuan Bajo', 'Padar Island',
        'Island hop', 'Padar Island\nKanawa Island\nSnorkeling', 'Boat\nMeals\nGuide',
        'Flights\nPersonal expenses',
        'Day 1: Island Tour | Visit Padar and Kanawa islands\nDay 2: Snorkeling | Snorkeling and beach activities\nDay 3: Return | Return to Labuan Bajo');

-- Dummy data for images
INSERT INTO images (id, package_id, url)
VALUES (1, 1001, 'bali.jpg'),
       (2, 1002, 'yogyakarta.jpg'),
       (3, 1003, 'lombok.jpg'),
       (4, 1004, 'komodo.jpg'),
       (5, 1005, 'bandung.jpg'),
       (6, 1006, 'jakarta.jpg'),
       (7, 1007, 'rajaampat.jpg'),
       (8, 1008, 'sumatra.jpg'),
       (9, 1009, 'bromo.jpg'),
       (10, 1010, 'labuanbajo.jpg');

-- Dummy data for orders
INSERT INTO orders (id, booking_date, departure_date, amount, request, customer_id, package_id, status_id,
                    itinerary_url)
VALUES (1, '10:00:00', '2025-07-01', 2, 'Vegetarian meal', 1, 1001, 2, 'itinerary-1001.txt'),
       (2, '11:00:00', '2025-07-05', 1, NULL, 2, 1002, 1, NULL),
       (3, '12:00:00', '2025-07-10', 3, 'Extra bed', 1, 1003, NULL, NULL),
       (4, '13:00:00', '2025-07-15', 2, NULL, 2, 1004, 2, 'itinerary-1004.txt'),
       (5, '14:00:00', '2025-07-20', 1, 'Window seat', 1, 1005, NULL, NULL),
       (6, '15:00:00', '2025-07-25', 2, NULL, 2, 1006, 2, 'itinerary-1006.txt'),
       (7, '16:00:00', '2025-07-30', 1, NULL, 1, 1007, 1, NULL),
       (8, '17:00:00', '2025-08-01', 2, 'Late check-in', 2, 1008, NULL, NULL),
       (9, '18:00:00', '2025-08-05', 1, NULL, 1, 1009, 2, 'itinerary-1009.txt'),
       (10, '19:00:00', '2025-08-10', 3, 'Allergy info', 2, 1010, 1, NULL);
