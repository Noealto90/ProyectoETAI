# ETAI Laboratory Reservation System

A comprehensive web-based laboratory management system designed for educational institutions. This platform streamlines the process of reserving laboratory spaces, managing equipment inventory, reporting damages, and generating detailed usage reports with role-based access control.

## Screenshots

### Student Dashboard

<img width="1904" height="880" alt="Student Dashboard Interface" src="https://github.com/user-attachments/assets/83afbb72-a463-4997-a7a9-92baae0c459c" />

### Professor Dashboard

<img width="1910" height="880" alt="Professor Dashboard Interface" src="https://github.com/user-attachments/assets/f5eaf259-a4b0-4882-a998-dc2673474af2" />

### Super Administrator Dashboard

<img width="1887" height="882" alt="Super Administrator Dashboard" src="https://github.com/user-attachments/assets/df26dfae-3b8c-4686-8479-ee94c2fcea95" />

### Space Selection Interface

<img width="1892" height="922" alt="Laboratory Space Selection Interface" src="https://github.com/user-attachments/assets/1731c2c9-71f7-4d22-b131-beb32417f928" />

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: PostgreSQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Email Service**: PHPMailer
- **PDF Generation**: FPDF/TCPDF
- **Authentication**: Session-based authentication
- **Security**: Password hashing, SQL injection prevention

## Key Features

### User Authentication & Authorization

- Secure login and registration system
- Role-based access control (Super Admin, Administrator, Professor, Student)
- Institutional email validation (@etai.ac.cr)
- Password encryption and validation (minimum 10 characters)
- Session management and logout functionality

### Reservation Management

- Real-time laboratory space booking
- Hourly reservation slots
- Reservation cancellation and renewal
- Conflict detection and prevention
- Class-based group reservations
- Individual and shared usage options

### Equipment Management

- Complete equipment inventory tracking
- Equipment categorization (computers, desks, chairs)
- Equipment assignment to laboratories
- Damage reporting and tracking
- Equipment restoration workflow
- Equipment status monitoring

### Reporting & Analytics

- Detailed usage reports
- PDF report generation
- Equipment damage reports
- Laboratory utilization statistics
- User activity tracking
- Administrative dashboards

### Role-Based Dashboards

#### Super Administrator

- Complete system oversight
- User role assignment
- Semester/quarter management
- System-wide reports
- Equipment and laboratory management

#### Administrator

- Laboratory management
- Equipment inventory control
- Damage report handling
- Reservation oversight

#### Professor

- Class reservation creation
- Student reservation viewing
- Equipment damage reporting
- Usage reports access

#### Student

- Personal reservations
- Equipment damage reporting
- Reservation renewal
- Reservation history viewing

## Installation

### Prerequisites

- PHP 7.4 or higher
- PostgreSQL 12 or higher
- Apache/Nginx web server
- Composer (optional, for dependencies)

### Setup Instructions

1. **Clone the repository**:

```bash
git clone https://github.com/Noealto90/ProyectoETAI.git
cd ProyectoETAI
```

2. **Configure the Database**:

```bash
# Create PostgreSQL database
psql -U postgres
CREATE DATABASE reservas_etai;
\q

# Import the database schema
psql -U postgres -d reservas_etai -f BaseDeDatos.sql
```

3. **Configure Database Connection**:

Edit `includes/conexion.php` and `includes/conexion_estudiante.php`:

```php
<?php
$host = 'localhost';
$dbname = 'reservas_etai';
$username = 'your_username';
$password = 'your_password';
$port = '5432';
?>
```

4. **Configure Email Settings**:

Edit `includes/setting.php` for PHPMailer configuration:

```php
<?php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your_email@gmail.com');
define('SMTP_PASSWORD', 'your_app_password');
?>
```

5. **Run the Project**:

Place the project folder inside your local server environment (e.g., XAMPP's `htdocs` or WAMP's `www` directory).

Then open your web browser and navigate to:

```
http://localhost/ProyectoETAI/index.php
```

## Usage

### First-Time Setup

1. **Create Super Admin Account**:

   - Register with an institutional email (@etai.ac.cr)
   - Manually update the database to set the first user as superAdmin:

   ```sql
   UPDATE usuarios SET rol = 'superAdmin' WHERE id = 1;
   ```

2. **Configure Semester/Quarter**:

   - Login as Super Admin
   - Navigate to Quarter Management
   - Set active academic period

3. **Add Laboratories**:

   - Go to Laboratory Management
   - Add laboratory spaces with capacity

4. **Add Equipment**:
   - Navigate to Equipment Management
   - Register equipment with unique codes
   - Assign to laboratories

### Making a Reservation (Student)

1. Log in with institutional credentials
2. Select "New Reservation" from dashboard
3. Choose laboratory and time slot
4. Select usage type (individual/shared)
5. Confirm reservation

### Reporting Equipment Damage

1. Navigate to "Report Damage"
2. Select equipment and laboratory
3. Describe the issue
4. Submit report

## Common Issues & Solutions

### Database Connection Error

**Problem**: Cannot connect to database

**Solution**:

- Verify PostgreSQL service is running
- Check database credentials in `includes/conexion.php`
- Ensure database exists: `psql -U postgres -l`

### Email Not Sending

**Problem**: Registration emails not being sent

**Solution**:

- Verify SMTP credentials in `includes/setting.php`
- Enable "Less secure app access" or use App Passwords for Gmail
- Check firewall settings for SMTP port (587/465)

## Database Schema

### Main Tables

- **usuarios**: User accounts with role-based access
- **laboratorios**: Laboratory spaces and capacity
- **equipos**: Equipment inventory
- **espacios**: Available spaces within laboratories
- **reservas**: Reservation records
- **reportes_danos**: Equipment damage reports
- **cuatrimestres**: Academic period management

### Key Relationships

- Users can have multiple reservations
- Laboratories contain multiple equipment items
- Reservations linked to specific spaces and users
- Damage reports associated with equipment and users

## Future Enhancements

- [ ] Mobile application (iOS/Android)
- [ ] Real-time notifications (WebSocket)
- [ ] QR code-based check-in/check-out
- [ ] Integration with institutional calendar
- [ ] Advanced analytics and reporting
- [ ] Multi-language support
- [ ] API for third-party integrations
- [ ] Automated equipment maintenance scheduling

## Contributors

This project was developed as part of the **Administración de Proyectos** course at the **Instituto Tecnológico de Costa Rica**.

**Development Team**:

- Backend Development & Database Design
- Frontend Development & UI/UX
- System Architecture & Integration

## License

This project is developed for academic purposes as part of the **Administración de proyectos** course - II Semester 2024.
