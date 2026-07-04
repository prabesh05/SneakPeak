CREATE DATABASE SneakPeak;

USE SneakPeak;

CREATE TABLE login(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user'
);



-- to promote the registered account in to admin

UPDATE login SET role = 'admin' WHERE email = 'admin1@sneakpeak.np';
UPDATE login SET role = 'admin' WHERE email = 'admin2@sneakpeak.np';
UPDATE login SET role = 'admin' WHERE email = 'owner@sneakpeak.np';


CREATE TABLE  products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand VARCHAR(50) NOT NULL,
    name VARCHAR(150) NOT NULL,
    colorway VARCHAR(150) DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL,
    badge VARCHAR(20) DEFAULT NULL,
    img VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products (brand, name, colorway, price, badge, img) VALUES
-- NIKE
('nike', 'Air Jordan 1 Retro High OG', 'Chicago / Red & White', 180, 'hot', 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&q=80'),
('nike', 'Nike Dunk Low', 'Panda / Black & White', 110, NULL, 'https://images.unsplash.com/photo-1600269452121-4f2416e55c28?w=600&q=80'),
('nike', 'Air Force 1 \'07', 'Triple White', 90, NULL, 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600&q=80'),
('nike', 'Nike Air Max 90', 'Infrared / White & Red', 130, 'new', 'https://images.unsplash.com/photo-1605348532760-6753d2c43329?w=600&q=80'),

-- ADIDAS
('adidas', 'Adidas Ultraboost 23', 'Core Black / Cloud White', 190, 'hot', 'https://images.unsplash.com/photo-1539185441755-769473a23570?w=600&q=80'),
('adidas', 'Adidas Stan Smith', 'White / Green', 100, NULL, 'https://images.stockx.com/images/adidas-Stan-Smith-Footwear-White-Core-Black.jpg?fit=fill&bg=FFFFFF&w=480&h=320&q=60&dpr=1&trim=color&updated_at=1664882949'),
('adidas', 'Adidas Samba OG', 'Core Black / Gum', 100, 'hot', 'https://images.unsplash.com/photo-1584735175315-9d5df23be4be?w=600&q=80'),
('adidas', 'Adidas Forum Low', 'Cloud White / Collegiate Navy', 90, 'new', 'https://images.unsplash.com/photo-1620155176061-52b66f842a39?w=600&q=80'),

-- NEW BALANCE
('new balance', 'New Balance 550', 'White / Green', 110, 'hot', 'https://images.unsplash.com/photo-1608231387042-66d1773d3028?w=600&q=80'),
('new balance', 'New Balance 990v6', 'Grey / Navy', 185, NULL, 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=600&q=80'),
('new balance', 'New Balance 2002R', 'Sea Salt / Beige', 150, 'new', 'https://images.unsplash.com/photo-1562183241-b937e95585b6?w=600&q=80'),

-- UNDER ARMOUR
('under armour', 'UA Curry 11', 'Performance Blue', 160, 'new', 'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=600&q=80'),
('under armour', 'UA HOVR Phantom 3', 'Black / Metallic Silver', 140, NULL, 'https://images.unsplash.com/photo-1603808033192-082d6919d3e1?w=600&q=80'),
('under armour', 'UA SlipSpeed Mega', 'Pitch Gray / White', 120, 'sale', 'https://images.unsplash.com/photo-1491553895911-0055eca6402d?w=600&q=80'),

-- CONVERSE
('converse', 'Chuck Taylor All Star Hi', 'Classic Black', 65, NULL, 'https://images.unsplash.com/photo-1463100099107-aa0980c362e6?w=600&q=80'),
('converse', 'Converse Run Star Hike', 'White / Black / Gum', 100, 'hot', 'https://images.unsplash.com/photo-1518002171953-a080ee817e1f?w=600&q=80'),
('converse', 'Chuck 70 High Top', 'Vintage Canvas / White', 85, 'new', 'https://images.unsplash.com/photo-1445632283550-de097b87ad43?w=600&q=80'),

-- CROCS
('crocs', 'Classic Clog', 'Neon Yellow', 55, 'hot', 'https://media.crocs.com/images/f_auto%2Cq_auto%2Cw_900%2Ch_900%2Cc_pad%2Cb_transparent/products/10001_3YF_ALT100/crocs.jpg'),
('crocs', 'Classic Mega Crush Clog', 'Black', 80, 'sale', 'https://media.crocs.com/images/f_auto%2Cq_auto%2Cw_900%2Ch_900%2Cc_pad%2Cb_transparent/products/10001_5EP_ALT100/crocs.jpg'),
('crocs', 'Echo Clog', 'Black', 80, NULL, 'https://media.crocs.com/images/f_auto%2Cq_auto%2Cw_900%2Ch_900%2Cc_pad%2Cb_transparent/products/207937_001_ALT100/crocs.jpg'),

-- PUMA
('puma', 'Puma Suede Classic XXI', 'Black / Puma Team Gold', 75, NULL, 'https://images.unsplash.com/photo-1600181957967-7ff2f3fb4f19?w=600&q=80'),
('puma', 'Puma RS-X', 'White / Team Royal / Red', 110, 'hot', 'https://images.unsplash.com/photo-1511556532299-8f662fc26c06?w=600&q=80'),

-- VANS
('vans', 'Vans Old Skool', 'Black / White', 65, NULL, 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=600&q=80'),
('vans', 'Vans Sk8-Hi', 'Classic White', 80, 'new', 'https://images.unsplash.com/photo-1609259510516-5aea81aeeedc?w=600&q=80');