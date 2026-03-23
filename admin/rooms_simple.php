<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();

// Handle room status toggle
if(isset($_POST['toggle_status'])) {
    $frm_data = filteration($_POST);
    $room_id = $frm_data['room_id'];
    $status = $frm_data['status'] == '1' ? 0 : 1;
    
    $query = "UPDATE `rooms` SET `status` = ? WHERE `id` = ?";
    $values = [$status, $room_id];
    
    if(update($query, $values, 'ii')) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update room status']);
    }
    exit;
}

// Fetch all active rooms
$query = "SELECT r.*, 
                 (SELECT GROUP_CONCAT(DISTINCT f.name) 
                  FROM `room_features` rf 
                  JOIN `features` f ON rf.features_id = f.sr_no 
                  WHERE rf.room_id = r.id) as features,
                 (SELECT GROUP_CONCAT(DISTINCT fac.name) 
                  FROM `room_facilities` rfac 
                  JOIN `facilities` fac ON rfac.facilities_id = fac.sr_no 
                  WHERE rfac.room_id = r.id) as facilities
          FROM `rooms` r 
          WHERE r.`removed` = 0
          ORDER BY r.id DESC";

$result = mysqli_query($con, $query);
$rooms = [];
while($row = mysqli_fetch_assoc($result)) {
    $rooms[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #4361ee;
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
            z-index: 1000;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
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
            margin-bottom: 20px;
        }
        
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
        
        .table-responsive {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            padding: 20px;
        }
        
        .room-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .status-toggle {
            cursor: pointer;
        }
        
        .action-btns .btn {
            margin: 0 2px;
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
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4 class="text-white">Admin Panel</h4>
            <small class="text-muted">Rooms Management</small>
        </div>
        
        <div class="sidebar-menu">
            <a href="admin_dashboard_simple.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="rooms_simple.php" class="active">
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
            <h2>Manage Rooms</h2>
            <a href="add_room_simple.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Room
            </a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="roomsTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Room Name</th>
                                <th>Price</th>
                                <th>Area</th>
                                <th>Guests</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($rooms as $index => $room): 
                                // Get room thumbnail image
                                $img_query = "SELECT * FROM `room_images` WHERE `room_id` = {$room['id']} AND `thumb` = 1 LIMIT 1";
                                $img_result = mysqli_query($con, $img_query);
                                $image = $img_result && mysqli_num_rows($img_result) > 0 
                                    ? ROOMS_IMG_PATH . mysqli_fetch_assoc($img_result)['image'] 
                                    : 'images/default_room.jpg';
                            ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <img src="<?php echo $image; ?>" alt="Room Image" class="room-image">
                                    </td>
                                    <td><?php echo htmlspecialchars($room['name']); ?></td>
                                    <td>$<?php echo $room['price']; ?>/night</td>
                                    <td><?php echo $room['area']; ?> sq.ft</td>
                                    <td><?php echo $room['adult']; ?> Adults, <?php echo $room['children']; ?> Children</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" type="checkbox" 
                                                   data-room-id="<?php echo $room['id']; ?>"
                                                   <?php echo $room['status'] == 1 ? 'checked' : ''; ?>>
                                            <label class="form-check-label">
                                                <?php echo $room['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="action-btns">
                                        <a href="view_room_simple.php?id=<?php echo $room['id']; ?>" class="btn btn-sm btn-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="edit_room_simple.php?id=<?php echo $room['id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button onclick="archiveRoom(<?php echo $room['id']; ?>)" class="btn btn-sm btn-danger" title="Archive">
                                            <i class="bi bi-archive"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Archive Confirmation Modal -->
    <div class="modal fade" id="archiveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Archive Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to archive this room? It will be moved to archived rooms.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmArchive">Yes, Archive</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#roomsTable').DataTable({
                responsive: true,
                order: [[0, 'asc']]
            });
            
            // Toggle room status
            $('.status-toggle').change(function() {
                const roomId = $(this).data('room-id');
                const status = $(this).is(':checked') ? '1' : '0';
                
                $.ajax({
                    url: 'rooms_simple.php',
                    type: 'POST',
                    data: {
                        toggle_status: 1,
                        room_id: roomId,
                        status: status
                    },
                    success: function(response) {
                        const res = JSON.parse(response);
                        if(res.status !== 'success') {
                            alert('Failed to update room status');
                            location.reload();
                        }
                    },
                    error: function() {
                        alert('Error updating room status');
                        location.reload();
                    }
                });
            });
        });
        
        // Handle room archiving
        let roomToArchive = null;
        
        function archiveRoom(roomId) {
            roomToArchive = roomId;
            const modal = new bootstrap.Modal(document.getElementById('archiveModal'));
            modal.show();
        }
        
        document.getElementById('confirmArchive').addEventListener('click', function() {
            if (!roomToArchive) return;
            
            fetch('ajax/rooms.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `remove_room=1&room_id=${roomToArchive}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Room archived successfully!');
                    window.location.reload();
                } else {
                    alert('Failed to archive room: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while archiving the room.');
            })
            .finally(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('archiveModal'));
                modal.hide();
            });
        });
    </script>
</body>
</html>
