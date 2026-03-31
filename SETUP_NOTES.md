# Setup Notes

- For Windows/XAMPP, run the app with Apache and MySQL from XAMPP.
- `start.sh` and `start_mysql.sh` are Linux/Replit-oriented helper scripts and are not the primary startup path on Windows.
- Shared database compatibility bootstrapping now lives in [admin/inc/app_schema.php](/C:/xampp/htdocs/SE/admin/inc/app_schema.php) and is loaded from [admin/inc/db_config.php](/C:/xampp/htdocs/SE/admin/inc/db_config.php).
