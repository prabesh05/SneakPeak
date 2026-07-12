# SneakPeak

**SneakPeak** is a modern, sleek sneaker e-commerce platform built with PHP and MySQL. It features an intuitive shopping experience with a stylish dark-themed interface, user authentication, advanced search functionality, shopping cart management, and comprehensive admin controls.

## 🎨 Features

### For Customers
- **User Authentication**: Secure register and login system with password hashing
- **Product Browsing**: Browse a curated collection of sneakers with detailed product information
- **Advanced Search**: Search products by brand, name, colorway, and price with real-time results
- **Shopping Cart**: Session-based cart with real-time badge updates and quantity management
- **Product Details**: View product brand, name, colorway, pricing, and product images
- **Responsive Design**: Sleek dark theme optimized for all devices
- **User Profile**: Dropdown menu with user email and quick navigation

### For Administrators
- **Product Management**: Add, edit, and manage sneaker listings
- **Inventory Control**: Track and manage product availability
- **Badge System**: Mark products with special badges (New, Sale, Limited, etc.)
- **Admin Dashboard**: Dedicated admin interface for complete store management
- **Flash Notifications**: Real-time feedback for admin actions

## 🛠 Tech Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Styling**: Custom CSS with modern design patterns
- **Session Management**: PHP Sessions for cart and authentication
- **Fonts**: Bebas Neue, Barlow, Barlow Condensed

## 📋 Prerequisites

Before running this project, ensure you have:

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- LOCALHOST environment (for development)
- Modern browser with JavaScript enabled

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
   $conn = new mysqli('localhost', 'root', '', 'SneakPeak', 3306);
   ```

4. **Access the Application**
   - **Homepage**: `http://localhost/SneakPeak/pages/index.php`
   - **Products**: `http://localhost/SneakPeak/pages/products.php`
   - **Search**: `http://localhost/SneakPeak/pages/search.php`
   - **Cart**: `http://localhost/SneakPeak/pages/cart.php`
   - **Login**: `http://localhost/SneakPeak/pages/login.php`
   - **Register**: `http://localhost/SneakPeak/pages/register.php`
   - **Admin Panel**: `http://localhost/SneakPeak/pages/adminProduct.php`
   - **About Us**: `http://localhost/SneakPeak/pages/AboutUs.php`

## 📁 Project Structure

```
SneakPeak/
├── Database/
│   └── SneakPeak_db.sql           # Database schema with all tables
├── pages/
│   ├── index.php                   # Landing page with hero section
│   ├── login.php                   # User authentication (login)
│   ├── register.php                # User registration
│   ├── products.php                # Product catalog with grid display
│   ├── search.php                  # Advanced product search functionality
│   ├── cart.php                    # Shopping cart management
│   ├── addToCart.php               # Cart item addition (AJAX)
│   ├── adminProduct.php            # Admin dashboard for product management
│   ├── AboutUs.php                 # About page with company story
│   ├── cartHelper.php              # Session-based cart helper functions
│   └── database.php                # Database connection & initialization
├── images/                         # Product images directory
├── README.md                       # This file
└── .gitignore                      # Git ignore file
```

## 🗄️ Database Schema

### Users Table (login)
- `id`: Primary key
- `user_name`: Unique username
- `email`: User email (unique)
- `password`: Hashed password (secure storage)
- `role`: User role (user/admin)
- `created_at`: Account creation timestamp

### Products Table
- `id`: Primary key
- `brand`: Shoe brand name
- `name`: Product name
- `colorway`: Product color variant
- `price`: Product price (DECIMAL)
- `badge`: Special badge (e.g., "New", "Sale", "Limited")
- `img`: Image URL/path
- `created_at`: Product creation timestamp

### Cart Table
- `id`: Primary key
- `order_id`: Unique order identifier
- `user_id`: Associated user (if logged in)
- `product_id`: Reference to product
- `product_name`: Product name (snapshot)
- `size`: Selected shoe size
- `quantity`: Item quantity
- `unit_price`: Price per unit
- `line_total`: Total for this line
- `customer_name`: Customer name
- `phone`: Customer phone number
- `address`: Shipping address
- `payment_method`: Payment method selected
- `subtotal`, `shipping_fee`, `total_amount`: Pricing breakdown
- `status`: Order status (pending, completed, etc.)
- `created_at`: Order creation timestamp

## 👥 User Roles

- **Admin**: Full access to product management, inventory, and store settings
  - Can add, edit, delete products
  - Can manage product badges and inventory
  - Access to admin dashboard
- **User**: Standard customer access to browsing and shopping
  - Can browse products
  - Can search for products
  - Can add items to cart
  - Can manage cart and checkout

### Promoting to Admin
To promote a registered user to admin status, run:
```sql
UPDATE login SET role = 'admin' WHERE email = 'user@email.com';
```

Or in phpMyAdmin, edit the user's role field to 'admin'.

## 🎯 Key Pages & Features

### `index.php` - Landing Page
- Hero section with brand showcase
- Eye-catching CTA (Call-to-Action) button
- Animated striped background accents
- Responsive hero layout
- Direct links to products and authentication

### `products.php` - Product Catalog
- Responsive grid display of all products
- Product cards with images and pricing
- "Add to Cart" functionality
- Cart integration with badge counter
- Navigation bar with user menu
- Mobile-optimized layout

### `search.php` - Advanced Search
- Real-time product search
- Filter by brand, name, and colorway
- Live search results with product cards
- "No results" messaging
- Professional search interface

### `cart.php` - Shopping Cart
- View all cart items with images and prices
- Adjust quantities (1-10 per item limit)
- Remove individual items or clear cart
- Order summary with subtotal and shipping
- Checkout process with customer details
- Order status tracking

### `login.php` - User Authentication
- Email/Username login
- Secure password validation
- Error handling and messages
- Link to registration page
- Remember functionality via sessions

### `register.php` - User Registration
- Email-based registration
- Username and password creation
- Password strength indication
- Auto-login on successful registration
- Form validation

### `adminProduct.php` - Admin Panel
- Add new products with all details
- Edit existing products
- Badge management (New, Sale, Limited, etc.)
- Product listing view
- Flash message notifications
- Inventory management

### `AboutUs.php` - Company Story
- Brand history and mission
- Core values and company culture
- Team showcase
- Responsive design with animations

## 🎨 Design System

### Color Scheme
- **Primary Red**: `#E8192C` (SneakPeak branding)
- **Black**: `#111111` (Main background)
- **Dark Grey**: `#1a1a1a` (Card backgrounds)
- **White**: `#F5F5F5` (Primary text/Accents)
- **Grey**: `#999` (Secondary text)
- **Green**: `#2ecc71` (Success messages)

### Typography
- **Headlines**: Bebas Neue (Bold, uppercase, modern)
- **Body**: Barlow (Clean, readable, accessible)
- **UI Elements**: Barlow Condensed (Compact, modern, professional)

### Visual Elements
- Smooth transitions and hover effects
- Rounded corners with subtle borders
- Gradient overlays for depth
- Box shadows for elevation
- Responsive breakpoints for mobile/tablet/desktop

## 🔒 Security Features

- ✅ Password hashing for secure storage
- ✅ SQL injection prevention via prepared statements
- ✅ User role-based access control (RBAC)
- ✅ Session management for authentication
- ✅ Secure session handling
- ✅ Input validation on forms
- ✅ Error handling without exposing sensitive info

## 🛒 Cart System

### Session-Based Cart
- Cart data stored in PHP sessions (no database persistence on client)
- Cart items tracked as: `{productId}_{size}`
- Supports multiple sizes of same product
- Real-time quantity updates
- Cart badge counter on navbar

### Cart Functions (cartHelper.php)
- `cart_add($id, $size, $qty)` - Add item to cart
- `cart_remove($key)` - Remove item from cart
- `cart_set_qty($key, $qty)` - Update item quantity
- `cart_clear()` - Clear entire cart
- `cart_count()` - Get total items in cart
- `cart_is_empty()` - Check if cart is empty
- `cart_items_with_products($conn)` - Get cart with live product data

## 📱 Responsive Design

- **Desktop**: Full-featured interface with all elements visible
- **Tablet**: Optimized grid layouts and touch-friendly buttons
- **Mobile**: Stacked layouts, readable text, accessible navigation
- CSS media queries for all breakpoints
- Touch-friendly button sizes and spacing

## 🔄 Future Enhancements

- [ ] Shopping cart persistence (database storage)
- [ ] Complete order management system
- [ ] Product reviews and ratings
- [ ] Payment gateway integration (Stripe, PayPal)
- [ ] Email notifications and confirmations
- [ ] Advanced filtering (price range, size, brand)
- [ ] Wishlist/Favorites feature
- [ ] Real-time inventory tracking
- [ ] Order history for users
- [ ] Analytics dashboard for admins
- [ ] Product recommendations
- [ ] User account management

## 🐛 Known Issues & Limitations

- Cart data is session-based (clears on logout)
- No payment processing implemented yet
- Admin functions require direct database access or UI implementation
- Single image per product

## 📝 Usage Tips

1. **First Time Setup**: Create an admin account by:
   - Registering as a normal user
   - Updating their role to 'admin' in the database
   - Logging back in to access admin features

2. **Adding Products**: Use the admin panel to add products with all required fields

3. **Managing Inventory**: Use the admin panel to edit product availability

4. **Testing**: Use sample credentials or create test accounts

## 🤝 Contributing

Contributions are welcome! Please feel free to submit pull requests or open issues for bugs and feature requests.

## 📄 License

This project is open source and available under the MIT License.

## 👨‍💻 Author

**Created by**: Prabesh  
**Repository**: [prabesh05/SneakPeak](https://github.com/prabesh05/SneakPeak)  
**Contact**: [GitHub Profile](https://github.com/prabesh05)

---

**Last Updated**: July 2026  
**Version**: 1.0.0  
**Language**: PHP (100%)

For questions or support, please open an issue on the GitHub repository.
