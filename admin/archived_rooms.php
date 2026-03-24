<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();

// Get all archived rooms
$query = "SELECT ar.*, 
          (SELECT image FROM archived_room_images WHERE room_id = ar.id LIMIT 1) as thumbnail,
          (SELECT GROUP_CONCAT(f.name) 
           FROM archived_room_features arf 
           JOIN features f ON arf.features_id = f.id 
           WHERE arf.room_id = ar.id) as features,
          (SELECT GROUP_CONCAT(f.name) 
           FROM archived_room_facilities arf 
           JOIN facilities f ON arf.facilities_id = f.id 
           WHERE arf.room_id = ar.id) as facilities
          FROM archived_rooms ar 
          ORDER BY ar.archived_at DESC";
$res = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Archived Rooms</title>
  <?php require('inc/links.php'); ?>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <style>
    .room-image {
      width: 100%;
      height: 150px;
      object-fit: cover;
      border-radius: 4px;
    }
    .action-btns .btn {
      margin: 0 2px;
    }
    .feature-badge {
      margin: 2px;
    }
  </style>
</head>
<body class="bg-light">
  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h3 class="mb-0">Archived Rooms</h3>
          <a href="rooms.php" class="btn btn-dark">
            <i class="bi bi-arrow-left"></i> Back to Active Rooms
          </a>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <?php if(mysqli_num_rows($res) == 0): ?>
              <div class="text-center py-5">
                <i class="bi bi-archive fs-1 text-muted"></i>
                <h5 class="mt-3">No archived rooms found</h5>
                <p class="text-muted">There are currently no rooms in the archive.</p>
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover border" id="archiveTable">
                  <thead>
                    <tr class="bg-dark text-light">
                      <th>#</th>
                      <th>Room</th>
                      <th>Details</th>
                      <th>Price/Night</th>
                      <th>Status</th>
                      <th>Archived On</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    $i = 1;
                    while($room = mysqli_fetch_assoc($res)): 
                      $features = !empty($room['features']) ? explode(',', $room['features']) : [];
                      $facilities = !empty($room['facilities']) ? explode(',', $room['facilities']) : [];
                      $status = $room['status'] == 1 ? 'Active' : 'Inactive';
                      $status_class = $room['status'] == 1 ? 'success' : 'danger';
                    ?>
                      <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                          <div class="d-flex align-items-center">
                            <?php if(!empty($room['thumbnail'])): ?>
                              <img src="../images/<?php echo htmlspecialchars($room['thumbnail']); ?>" class="room-image me-3" alt="<?php echo htmlspecialchars($room['name']); ?>"
                                   onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                              <div class="bg-light d-flex align-items-center justify-content-center me-3" style="width:100px;height:100px;display:none!important">
                                <i class="bi bi-image text-muted" style="font-size:2rem"></i>
                              </div>
                            <?php else: ?>
                              <div class="bg-light d-flex align-items-center justify-content-center me-3" style="width: 100px; height: 100px;">
                                <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                              </div>
                            <?php endif; ?>
                            <div>
                              <h6 class="mb-1"><?php echo htmlspecialchars($room['name']); ?></h6>
                              <small class="text-muted">ID: <?php echo $room['room_id']; ?></small>
                            </div>
                          </div>
                        </td>
                        <td>
                          <div class="mb-2">
                            <span class="badge bg-primary"><?php echo $room['adult']; ?> Adults, <?php echo $room['children']; ?> Children</span>
                            <span class="badge bg-secondary"><?php echo $room['area']; ?> sq.ft</span>
                          </div>
                          <div class="features">
                            <?php foreach(array_slice($features, 0, 3) as $feature): ?>
                              <span class="badge bg-info text-dark feature-badge"><?php echo htmlspecialchars(trim($feature)); ?></span>
                            <?php endforeach; ?>
                            <?php if(count($features) > 3): ?>
                              <span class="badge bg-light text-dark">+<?php echo (count($features) - 3); ?> more</span>
                            <?php endif; ?>
                          </div>
                        </td>
                        <td>₱<?php echo number_format($room['price'], 2); ?></td>
                        <td><span class="badge bg-<?php echo $status_class; ?>"><?php echo $status; ?></span></td>
                        <td><?php echo (!empty($room['archived_at']) && $room['archived_at'] !== '0000-00-00 00:00:00') ? date('M d, Y', strtotime($room['archived_at'])) : 'N/A'; ?></td>
                        <td>
                          <div class="btn-group action-btns">
                            <button class="btn btn-sm btn-success" onclick="restoreRoom(<?php echo $room['id']; ?>)">
                              <i class="bi bi-arrow-counterclockwise"></i> Restore
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteRoom(<?php echo $room['id']; ?>)">
                              <i class="bi bi-trash"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Restore Confirmation Modal -->
  <div class="modal fade" id="restoreModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="restoreModalLabel">Restore Room</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to restore this room?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success" id="confirmRestore">Restore Room</button>
        </div>
      </div>
    </div>
  </div>

  <?php require('inc/scripts.php'); ?>
  
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  
  <script>
  $(document).ready(function() {
    // Initialize DataTable
    $('#archiveTable').DataTable({
      pageLength: 10,
      order: [[5, 'desc']], // Sort by archived date by default
      columnDefs: [
        { orderable: false, targets: [6] } // Make actions column not sortable
      ]
    });
    
    // Search functionality
    $('#search_input').on('keyup', function() {
      $('#archiveTable').DataTable().search(this.value).draw();
    });
    
    $('#search_btn').on('click', function() {
      $('#archiveTable').DataTable().search($('#search_input').val()).draw();
    });
  });
  
  let roomToRestore = null;
  
  function restoreRoom(roomId) {
    roomToRestore = roomId;
    $('#restoreModal').modal('show');
  }
  
  function deleteRoom(roomId) {
    if(confirm('Are you sure you want to permanently delete this room? This action cannot be undone.')) {
      // AJAX call to delete the room
      alert('Delete functionality will be implemented here');
    }
  }
  
  $('#confirmRestore').on('click', function() {
    if(!roomToRestore) return;
    
    // Show loading state
    const $btn = $(this);
    const $originalText = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Restoring...');
    
    // AJAX call to restore the room
    $.ajax({
      url: 'ajax/restore_room.php',
      type: 'POST',
      data: { room_id: roomToRestore },
      dataType: 'json',
      success: function(response) {
        if(response.status === 'success') {
          alert('Room restored successfully!');
          location.reload();
        } else {
          alert('Error: ' + (response.message || 'Failed to restore room'));
        }
      },
      error: function() {
        alert('An error occurred. Please try again.');
      },
      complete: function() {
        $btn.prop('disabled', false).html($originalText);
        $('#restoreModal').modal('hide');
      }
    });
  });
  </script>
  

</body>
</html>
