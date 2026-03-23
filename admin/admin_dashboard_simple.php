<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();

// Get counts for dashboard
$count_rooms = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM `rooms` WHERE `removed` = 0"))['count'];
$count_bookings = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM `booking_order`"))['count'];
$count_users = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM `user_cred`"))['count'];
$count_contacts = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM `contact_us` WHERE `is_read` = 0"))['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        body {
            background-color: #f5f7fb;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: var(--dark-color);
            color: white;
            padding: 20px 0;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s;
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left: 4px solid var(--primary-color);
        }
        
        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .card-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        
        .bg-primary { background-color: var(--primary-color) !important; }
        .bg-success { background-color: #2ecc71 !important; }
        .bg-warning { background-color: #f39c12 !important; }
        .bg-danger { background-color: #e74c3c !important; }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .table-responsive {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            padding: 20px;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: #6c757d;
        }
        
        .badge {
            padding: 8px 12px;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                left: calc(-1 * var(--sidebar-width));
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .menu-toggle {
                display: block !important;
            }
        }
        
        .menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: var(--primary-color);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Sidebar Toggle Button -->
    <button class="menu-toggle" id="sidebarToggle">
        <i class="bi bi-list"></i>
    </button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4 class="text-white">Admin Panel</h4>
            <small class="text-muted">Dashboard</small>
        </div>
        
        <div class="sidebar-menu">
            <a href="admin_dashboard_simple.php" class="active">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="rooms_simple.php">
                <i class="bi bi-house-door"></i> Rooms
            </a>
            <a href="archived_rooms_simple.php">
                <i class="bi bi-archive"></i> Archived Rooms
            </a>
            <a href="bookings_simple.php">
                <i class="bi bi-calendar-check"></i> Bookings
            </a>
            <a href="users_simple.php">
                <i class="bi bi-people"></i> Users
            </a>
            <a href="settings_simple.php">
                <i class="bi bi-gear"></i> Settings
            </a>
            <a href="logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-header">
            <h2>Dashboard Overview</h2>
            <div>
                <span class="text-muted me-2">Welcome, Admin</span>
                <span class="badge bg-primary">Admin</span>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card bg-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Rooms</h6>
                                <h3 class="mb-0"><?php echo $count_rooms; ?></h3>
                            </div>
                            <div class="bg-primary text-white p-3 rounded">
                                <i class="bi bi-house-door card-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Bookings</h6>
                                <h3 class="mb-0"><?php echo $count_bookings; ?></h3>
                            </div>
                            <div class="bg-success text-white p-3 rounded">
                                <i class="bi bi-calendar-check card-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Users</h6>
                                <h3 class="mb-0"><?php echo $count_users; ?></h3>
                            </div>
                            <div class="bg-warning text-white p-3 rounded">
                                <i class="bi bi-people card-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">New Messages</h6>
                                <h3 class="mb-0"><?php echo $count_contacts; ?></h3>
                            </div>
                            <div class="bg-danger text-white p-3 rounded">
                                <i class="bi bi-envelope card-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activities -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Recent Activities</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Activity</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>New booking received</td>
                                        <td>Just now</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                        <td><a href="#" class="btn btn-sm btn-primary">View</a></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Room #101 updated</td>
                                        <td>5 minutes ago</td>
                                        <td><span class="badge bg-info">Updated</span></td>
                                        <td><a href="#" class="btn btn-sm btn-primary">View</a></td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>New user registered</td>
                                        <td>1 hour ago</td>
                                        <td><span class="badge bg-warning">Pending</span></td>
                                        <td><a href="#" class="btn btn-sm btn-primary">View</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                event.target !== toggleBtn && 
                !toggleBtn.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>
