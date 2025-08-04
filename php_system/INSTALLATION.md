# Installation Guide - Data Management System

## Quick Start Guide

### Step 1: XAMPP Setup
1. Download XAMPP from https://www.apachefriends.org/
2. Install XAMPP on your system
3. Start **Apache** and **MySQL** services from XAMPP Control Panel

### Step 2: Deploy System
1. Copy the entire `php_system` folder to your XAMPP `htdocs` directory:
   - **Windows**: `C:\xampp\htdocs\php_system`
   - **Linux**: `/opt/lampp/htdocs/php_system`
   - **macOS**: `/Applications/XAMPP/htdocs/php_system`

### Step 3: Initialize Database
1. Open your web browser
2. Go to: `http://localhost/php_system/init_database.php`
3. You should see: "✓ Database and tables created successfully!"

### Step 4: Test System
1. Go to: `http://localhost/php_system/test_system.php`
2. Verify all checks show green checkmarks (✓)
3. Fix any issues shown in red (✗)

### Step 5: Access System
1. Go to: `http://localhost/php_system/`
2. Login with:
   - **Username**: `admin`
   - **Password**: `admin123`

## System Features

### Dashboard
- Two main modules: OFAC and IIBS
- Each module has its own database and search functionality
- Modern, responsive interface

### Data Management
- **Search**: Real-time search across all database fields
- **Upload**: Import data from CSV/Excel files
- **View**: Browse all records with pagination

### File Upload
- Supports CSV and Excel files (.csv, .xlsx, .xls)
- Automatic column mapping
- Error reporting for failed imports

## Database Schema

Each module (OFAC/IIBS) has identical table structure:

```sql
CREATE TABLE ofac_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source_media VARCHAR(255),
    source_media_date DATE,
    resolution TEXT,
    individual_corporation_involved VARCHAR(255),
    first_name VARCHAR(100),
    middle_name VARCHAR(100),
    last_name VARCHAR(100),
    name_ext VARCHAR(50),
    corporation_name_fullname VARCHAR(255),
    alternate_name_alias_1 VARCHAR(255),
    alternate_name_alias_2 VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Testing with Sample Data

1. Use the provided sample files:
   - `sample_data/sample_ofac.csv`
   - `sample_data/sample_iibs.csv`

2. Upload process:
   - Click "Upload Excel" button for either OFAC or IIBS
   - Select the appropriate sample CSV file
   - Click "Upload"
   - Verify success message

3. Test search functionality:
   - Click "View/Search" for the module you uploaded data to
   - Try searching for names like "John", "Smith", "ABC"

## Troubleshooting

### Common Issues

**1. Database Connection Failed**
- Ensure MySQL is running in XAMPP
- Check database credentials in `config/database.php`
- Default: username=`root`, password=`empty`

**2. File Upload Not Working**
- Check if `uploads/` directory exists and is writable
- Verify PHP upload limits in XAMPP settings
- File size limit: 10MB (configurable in `.htaccess`)

**3. Search Returns No Results**
- Ensure database tables were created (`init_database.php`)
- Upload sample data first
- Check browser console for JavaScript errors

**4. Login Issues**
- Verify credentials: `admin` / `admin123`
- Clear browser cache and cookies
- Check if sessions are working

### File Permissions (Linux/macOS)
```bash
chmod 755 php_system/
chmod 777 php_system/uploads/
```

### PHP Configuration
Minimum requirements:
- PHP 7.4+
- PDO extension
- PDO MySQL extension
- JSON extension
- File uploads enabled

## Security Notes

- Default admin credentials should be changed in production
- The system includes basic security measures:
  - Session-based authentication
  - SQL injection protection
  - XSS protection
  - File upload validation

## File Structure Overview

```
php_system/
├── index.php              # Main dashboard
├── login.php              # Authentication
├── logout.php             # Session cleanup
├── init_database.php      # Database setup
├── test_system.php        # System diagnostics
├── .htaccess              # Security & configuration
├── config/
│   └── database.php       # DB configuration
├── api/
│   ├── search.php         # Search functionality
│   └── upload.php         # File upload handler
├── sample_data/           # Test data files
├── uploads/               # Temporary upload storage
└── README.md              # Documentation
```

## Next Steps After Installation

1. **Test the system** with sample data
2. **Customize** database credentials if needed
3. **Import** your actual data files
4. **Configure** any additional security measures
5. **Backup** your database regularly

## Support

If you encounter issues:
1. Run `test_system.php` to diagnose problems
2. Check XAMPP error logs
3. Verify all installation steps were completed
4. Ensure XAMPP services are running

---

**Default Login Credentials:**
- Username: `admin`
- Password: `admin123`

**System URL:** `http://localhost/php_system/`
