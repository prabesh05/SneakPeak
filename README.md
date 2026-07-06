# SneakPeak

**SneakPeak** is a modern, sleek sneaker e-commerce platform built with PHP and MySQL. It features an intuitive shopping experience with a stylish dark-themed interface, user authentication, and admin product management.

## 🎨 Features

### For Customers
- **User Authentication**: Register and login system with secure password handling
- **Product Browsing**: Browse through a curated collection of sneakers with detailed product information
- **Shopping Cart**: Add items to cart with real-time badge updates
- **Product Details**: View product brand, name, colorway, and pricing
- **Responsive Design**: Sleek dark theme optimized for all devices

### For Administrators
- **Product Management**: Add, edit, and manage sneaker listings
- **Inventory Control**: Track and manage product availability
- **Badge System**: Mark products with special badges (New, Sale, etc.)
- **Admin Dashboard**: Dedicated admin interface for store management

## 🛠 Tech Stack

- **Backend**: PHP
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript
- **Styling**: Custom CSS with modern design patterns
- **Fonts**: Bebas Neue, Barlow, Barlow Condensed

## 📋 Prerequisites

Before running this project, ensure you have:

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- LOCALHOST environment

## 🚀 Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/prabesh05/SneakPeak.git
   cd SneakPeak
   ```

2. **Setup Database**
   ```bash
   # Import the database schema
   mysql -u root -p < Database/SneakPeak_db.sql
   ```

3. **Database Configuration**
   Update `pages/database.php` with your database credentials:
   ```php
   $conn = mysqli_connect('localhost', 'root', '', 'SneakPeak');
   ```

4. **Access the Application**
   - Homepage: `http://localhost/SneakPeak/pages/index.php`
   - Products: `http://localhost/SneakPeak/pages/products.php`
   - Login: `http://localhost/SneakPeak/pages/login.php`
   - Register: `http://localhost/SneakPeak/pages/register.php`

## 📁 Project Structure

```
SneakPeak/
├── Database/
│   └── SneakPeak_db.sql      # Database schema
├── pages/
│   ├── index.php             # Landing page
│   ├── login.php             # User login
│   ├── register.php          # User registration
│   ├── products.php          # Product catalog
│   ├── adminProduct.php      # Admin product management
│   └── database.php          # Database connection
├── images/                   # Product images
├── README.md                 # This file
└── ...
```

## 🗄️ Database Schema

### Users Table (login)
- `id`: Primary key
- `user_name`: Unique username
- `email`: User email (unique)
- `password`: Hashed password
- `role`: User role (user/admin)

### Products Table
- `id`: Primary key
- `brand`: Shoe brand name
- `name`: Product name
- `colorway`: Product color variant
- `price`: Product price
- `badge`: Special badge (e.g., "New", "Sale")
- `img`: Image URL
- `created_at`: Timestamp of creation

## 👥 User Roles

- **Admin**: Full access to product management and store settings
  - Default admin accounts can be created by updating the login table
- **User**: Standard customer access to browsing and purchasing

### Promoting to Admin
To promote a registered user to admin status, run:
```sql
UPDATE login SET role = 'admin' WHERE email = 'user@email.com';
```

## 🎯 Key Pages

### `index.php` - Landing Page
- Hero section with brand showcase
- CTA (Call-to-Action) button
- Animated striped accents
- Responsive hero layout

### `products.php` - Product Catalog
- Product grid display
- Cart integration
- Navigation bar with cart badge
- Responsive product cards

### `login.php` - Authentication
- Email/Username login
- Password validation
- Error handling
- Link to registration

### `register.php` - User Registration
- Email registration
- Username selection
- Password creation
- Auto-login on successful registration

### `adminProduct.php` - Admin Panel
- Product management interface
- Add new products
- Flash message notifications
- Product listing and editing

## 🎨 Design System

### Color Scheme
- **Primary Red**: `#E8192C` (SneakPeak branding)
- **Black**: `#111111` (Background)
- **White**: `#F5F5F5` (Text/Accents)
- **Grey**: `#999` (Secondary text)
- **Green**: `#2ecc71` (Success messages)

### Typography
- **Headlines**: Bebas Neue (Bold, uppercase)
- **Body**: Barlow (Clean, readable)
- **UI Elements**: Barlow Condensed (Compact, modern)

## 🔒 Security Features

- Password hashing for secure storage
- SQL injection prevention via prepared statements
- User role-based access control
- Session management for authentication

## 🔄 Future Enhancements

- [ ] Shopping cart persistence
- [ ] Order management system
- [ ] Product reviews and ratings
- [ ] Payment gateway integration
- [ ] Email notifications
- [ ] Search and filtering functionality
- [ ] Wishlist feature
- [ ] Product inventory tracking


**Created by**: Prabesh  
**Repository**: [prabesh05/SneakPeak](https://github.com/prabesh05/SneakPeak)
