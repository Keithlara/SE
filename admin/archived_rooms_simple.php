<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();

header('Location: Archives.php?tab=rooms');
exit;

// Fetch all archived rooms
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
          WHERE r.`is_archived` = 1 AND r.`removed` = 1
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
    <title>Archived Rooms - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .room-card {
            transition: transform 0.2s;
            margin-bottom: 20px;
        }
        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .room-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        .features-list, .facilities-list {
            list-style: none;
            padding-left: 0;
        }
        .features-list li, .facilities-list li {
            margin-bottom: 5px;
        }
        .badge-custom {
            background-color: #6c757d;
            color: white;
            margin-right: 5px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <?php require('inc/header.php'); ?>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Archived Rooms</h2>
            <a href="rooms.php" class="btn btn-dark">
                <i class="bi bi-arrow-left"></i> Back to Active Rooms
            </a>
        </div>

        <?php if(empty($rooms)): ?>
            <div class="alert alert-info">
                No archived rooms found.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach($rooms as $room): 
                    // Get room images
                    $img_query = "SELECT * FROM `room_images` WHERE `room_id` = {$room['id']} ORDER BY `thumb` DESC LIMIT 1";
                    $img_result = mysqli_query($con, $img_query);
                    $image = $img_result && mysqli_num_rows($img_result) > 0 
                        ? ROOMS_IMG_PATH . mysqli_fetch_assoc($img_result)['image'] 
                        : 'images/default_room.jpg';
                ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card room-card h-100">
                            <img src="<?php echo $image; ?>" class="card-img-top room-image" alt="Room Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($room['name']); ?></h5>
                                <p class="text-muted">
                                    <i class="bi bi-arrows-angle-expand"></i> <?php echo $room['area']; ?> sq.ft |
                                    <i class="bi bi-people"></i> <?php echo $room['adult']; ?> Adults, <?php echo $room['children']; ?> Children
                                </p>
                                <h6 class="text-primary mb-3">$<?php echo $room['price']; ?> per night</h6>
                                
                                <?php if(!empty($room['features'])): ?>
                                    <h6>Features:</h6>
                                    <div class="mb-3">
                                        <?php 
                                        $features = explode(',', $room['features']);
                                        foreach($features as $feature): 
                                        ?>
                                            <span class="badge bg-secondary mb-1"><?php echo trim($feature); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if(!empty($room['facilities'])): ?>
                                    <h6>Facilities:</h6>
                                    <div class="mb-3">
                                        <?php 
                                        $facilities = explode(',', $room['facilities']);
                                        foreach($facilities as $facility): 
                                        ?>
                                            <span class="badge bg-info text-dark mb-1"><?php echo trim($facility); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="d-grid gap-2">
                                    <button onclick="restoreRoom(<?php echo $room['id']; ?>)" class="btn btn-success">
                                        <i class="bi bi-arrow-counterclockwise"></i> Restore Room
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Restore Confirmation Modal -->
    <div class="modal fade" id="restoreModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Restore Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to restore this room? It will be moved back to the active rooms list.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmRestore">Yes, Restore</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let selectedRoomId = null;

        function restoreRoom(roomId) {
            selectedRoomId = roomId;
            const modal = new bootstrap.Modal(document.getElementById('restoreModal'));
            modal.show();
        }

        document.getElementById('confirmRestore').addEventListener('click', function() {
            if (!selectedRoomId) return;
            
            fetch('ajax/archived_rooms.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `restore_room=1&room_id=${selectedRoomId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Room restored successfully!');
                    window.location.reload();
                } else {
                    alert('Failed to restore room: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while restoring the room.');
            })
            .finally(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('restoreModal'));
                modal.hide();
            });
        });
    </script>
</body>
</html>
