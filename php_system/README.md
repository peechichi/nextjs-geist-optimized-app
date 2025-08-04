# Data Management System

A PHP-based system for managing OFAC and IIBS data with search functionality and Excel upload capabilities.

## Features

- **Admin Authentication**: Fixed admin credentials (username: admin, password: admin123)
- **Two Data Modules**: 
  - OFAC (Office of Foreign Assets Control) Database
  - IIBS (Integrated Intelligence-Based Screening) Database
- **Search Functionality**: Real-time search across all database fields
- **Excel Upload**: Upload CSV/Excel files to populate database
- **Responsive Design**: Modern Bootstrap-based interface

## Requirements

- XAMPP (Apache + MariaDB/MySQL + PHP)
- PHP 7.4 or higher
- MariaDB/MySQL database

## Installation

1. **Install XAMPP**
   - Download and install XAMPP from https://www.apachefriends.org/
   - Start Apache and MySQL services

2. **Setup Project**
   ```bash
   # Copy the php_system folder to your XAMPP htdocs directory
   # Example: C:\xampp\htdocs\php_system (Windows)
   # Example: /opt/lampp/htdocs/php_system (Linux)
   ```

3. **Database Configuration**
   - Open `config/database.php`
   - Update database credentials if needed (default: root with no password)

4. **Initialize Database**
   - Open your browser and go to: `http://localhost/php_system/init_database.php`
   - This will create the database and required tables

5. **Access the System**
   - Go to: `http://localhost/php_system/`
   - Login with: username `admin`, password `admin123`

## Database Structure

Both OFAC and IIBS tables contain the following columns:

- `source_media` - Source of the media/information
- `source_media_date` - Date of the source media
- `resolution` - Resolution or status
- `individual_corporation_involved` - Type (Individual/Corporation)
- `first_name` - First name (for individuals)
- `middle_name` - Middle name (for individuals)
- `last_name` - Last name (for individuals)
- `name_ext` - Name extension (Jr., Sr., etc.)
- `corporation_name_fullname` - Full corporation name
- `alternate_name_alias_1` - First alternate name/alias
- `alternate_name_alias_2` - Second alternate name/alias

## Excel Upload Format

When uploading Excel/CSV files, ensure your file contains the following column headers:

- SOURCE MEDIA
- SOURCE MEDIA DATE
- RESOLUTION
- INDIVIDUAL / CORPORATION INVOLVED
- FIRST NAME
- MIDDLE NAME
- LAST NAME
- NAME EXT
- CORPORATION NAME / FULLNAME
- ALTERNATE NAME / ALIAS
- ALTERNATE NAME / ALIAS (second column)

### Sample Files

Sample CSV files are provided in the `sample_data/` directory:
- `sample_ofac.csv` - Sample OFAC data
- `sample_iibs.csv` - Sample IIBS data

## Usage

1. **Login**: Use admin/admin123 to access the system
2. **View Data**: Click on "View/Search OFAC" or "View/Search IIBS" buttons
3. **Search**: Use the search box in the modal to filter records
4. **Upload Data**: Click "Upload Excel" buttons to import CSV/Excel files

## File Structure

```
php_system/
├── index.php              # Main dashboard
├── login.php              # Login page
├── logout.php             # Logout functionality
├── init_database.php      # Database initialization
├── config/
│   └── database.php       # Database configuration
├── api/
│   ├── search.php         # Search API endpoint
│   └── upload.php         # File upload API endpoint
├── sample_data/
│   ├── sample_ofac.csv    # Sample OFAC data
│   └── sample_iibs.csv    # Sample IIBS data
├── uploads/               # Temporary upload directory
└── README.md              # This file
```

## Security Features

- Session-based authentication
- SQL injection protection using prepared statements
- File upload validation
- XSS protection with htmlspecialchars()

## Troubleshooting

1. **Database Connection Issues**
   - Ensure MySQL/MariaDB is running in XAMPP
   - Check database credentials in `config/database.php`

2. **File Upload Issues**
   - Ensure `uploads/` directory has write permissions
   - Check PHP file upload limits in php.ini

3. **Search Not Working**
   - Verify database tables exist by running `init_database.php`
   - Check browser console for JavaScript errors

## Default Credentials

- **Username**: admin
- **Password**: admin123

## Support

For issues or questions, please check the troubleshooting section above or verify your XAMPP installation and configuration.
