<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - PROFILE</title>
</head>
<body class="bg-light">

  <?php 
    require('inc/header.php'); 

    if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
      redirect('index.php');
    }

    $u_exist = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1",[$_SESSION['uId']],'s');

    if(mysqli_num_rows($u_exist)==0){
      redirect('index.php');
    }

    $u_fetch = mysqli_fetch_assoc($u_exist);
    $is_verified = (int)$u_fetch['is_verified'];
  ?>

  <?php if($is_verified == 0): ?>
  <div class="alert alert-warning text-center mb-0 rounded-0 py-2" style="font-size:15px;">
    <i class="bi bi-exclamation-triangle-fill me-1"></i>
    <?php if(!empty($_SESSION['booking_blocked'])): unset($_SESSION['booking_blocked']); ?>
      <strong>Booking requires a verified email.</strong> Please verify your email first to continue booking.
    <?php else: ?>
      <strong>Your email is not verified.</strong> Please check your inbox for the verification link, or
    <?php endif; ?>
    <button id="resendVerifyBtn" class="btn btn-sm btn-warning ms-2 fw-semibold" style="font-size:13px;">
      <span id="resendBtnText">Resend Verification Email</span>
      <span id="resendSpinner" class="spinner-border spinner-border-sm d-none ms-1"></span>
    </button>
  </div>
  <?php endif; ?>

  <div class="container">
    <div class="row">

      <div class="col-12 my-5 px-4">
        <h2 class="fw-bold">PROFILE
          <?php if($is_verified == 1): ?>
            <span class="badge bg-success ms-2" style="font-size:14px;vertical-align:middle;">
              <i class="bi bi-patch-check-fill me-1"></i>Verified
            </span>
          <?php else: ?>
            <span class="badge bg-warning text-dark ms-2" style="font-size:14px;vertical-align:middle;">
              <i class="bi bi-exclamation-circle me-1"></i>Unverified
            </span>
          <?php endif; ?>
        </h2>
        <div style="font-size: 14px;">
          <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
          <span class="text-secondary"> > </span>
          <a href="#" class="text-secondary text-decoration-none">PROFILE</a>
        </div>
      </div>

      
      <div class="col-12 mb-5 px-4">
        <div class="bg-white p-3 p-md-4 rounded shadow-sm">
          <form id="info-form">
            <h5 class="mb-3 fw-bold">Basic Information</h5>
            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label">Name</label>
                <input name="name" type="text" value="<?php echo $u_fetch['name'] ?>" class="form-control shadow-none" required>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Phone Number</label>
                <input name="phonenum" type="number" value="<?php echo $u_fetch['phonenum'] ?>" class="form-control shadow-none" required>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Date of birth</label>
                <input name="dob" type="date" value="<?php echo $u_fetch['dob'] ?>" class="form-control shadow-none" required>
              </div>
              <div class="col-md-8 mb-4">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control shadow-none" rows="1" required><?php echo $u_fetch['address'] ?></textarea>
              </div>
            </div>
            <button type="submit" class="btn text-white custom-bg shadow-none">Save Changes</button>
          </form>
        </div>
      </div>

      <div class="col-md-4 mb-5 px-4">
        <div class="bg-white p-3 p-md-4 rounded shadow-sm">
          <form id="profile-form">
            <h5 class="mb-3 fw-bold">Picture</h5>
            <img src="<?php echo USERS_IMG_PATH.$u_fetch['profile'] ?>" class="rounded-circle img-fluid mb-3">

            <label class="form-label">New Picture</label>
            <input name="profile" type="file" accept=".jpg, .jpeg, .png, .webp" class="mb-4 form-control shadow-none" required>

            <button type="submit" class="btn text-white custom-bg shadow-none">Save Changes</button>
          </form>
        </div>
      </div>


      <div class="col-md-8 mb-5 px-4">
        <div class="bg-white p-3 p-md-4 rounded shadow-sm">
          <form id="pass-form">
            <h5 class="mb-3 fw-bold">Change Password</h5>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">New Password</label>
                <input name="new_pass" type="password" class="form-control shadow-none" required>
              </div>
              <div class="col-md-6 mb-4">
                <label class="form-label">Confirm Password</label>
                <input name="confirm_pass" type="password" class="form-control shadow-none" required>
              </div>
            </div>
            <button type="submit" class="btn text-white custom-bg shadow-none">Save Changes</button>
          </form>
        </div>
      </div>

      <div class="col-md-8 mb-5 px-4">
        <div class="bg-white p-3 p-md-4 rounded shadow-sm border border-danger">
          <h5 class="mb-3 fw-bold text-danger">Danger Zone</h5>
          <p class="text-muted mb-3">Once you delete your account, there is no going back. Please be certain.</p>
          <button class="btn btn-danger" id="deleteAccountBtn">
            <i class="bi bi-trash"></i> Delete Account
          </button>
        </div>
      </div>

    </div>
  </div>


  <?php require('inc/footer.php'); ?>

  <!-- Delete Account Confirmation Modal -->
  <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-danger">Confirm Account Deletion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="mb-0">Are you sure you want to delete your account? This action <strong>cannot be undone</strong> and all your data will be permanently removed.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
            <span class="spinner-border spinner-border-sm d-none" id="deleteSpinner"></span>
            Yes, Delete My Account
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>

    let info_form = document.getElementById('info-form');

    info_form.addEventListener('submit',function(e){
      e.preventDefault();

      let data = new FormData();
      data.append('info_form','');
      data.append('name',info_form.elements['name'].value);
      data.append('phonenum',info_form.elements['phonenum'].value);
      data.append('address',info_form.elements['address'].value);
      data.append('dob',info_form.elements['dob'].value);

      let xhr = new XMLHttpRequest();
      xhr.open("POST","ajax/profile.php",true);

      xhr.onload = function(){
        if(this.responseText == 'phone_already'){
          alert('error',"Phone number is already registered!");
        }
        else if(this.responseText == 0){
          alert('error',"No Changes Made!");
        }
        else{
          alert('success','Changes saved!');
        }
      }

      xhr.send(data);

    });

    
    let profile_form = document.getElementById('profile-form');

    profile_form.addEventListener('submit',function(e){
      e.preventDefault();

      let data = new FormData();
      data.append('profile_form','');
      data.append('profile',profile_form.elements['profile'].files[0]);

      let xhr = new XMLHttpRequest();
      xhr.open("POST","ajax/profile.php",true);

      xhr.onload = function()
      {
        if(this.responseText == 'inv_img'){
          alert('error',"Only JPG, WEBP & PNG images are allowed!");
        }
        else if(this.responseText == 'upd_failed'){
          alert('error',"Image upload failed!");
        }
        else if(this.responseText == 0){
          alert('error',"Updation failed!");
        }
        else{
          window.location.href=window.location.pathname;
        }
      }

      xhr.send(data);
    });


    let pass_form = document.getElementById('pass-form');

    pass_form.addEventListener('submit',function(e){
      e.preventDefault();

      let new_pass = pass_form.elements['new_pass'].value;
      let confirm_pass = pass_form.elements['confirm_pass'].value;

      if(new_pass!=confirm_pass){
        alert('error','Password do not match!');
        return false;
      }


      let data = new FormData();
      data.append('pass_form','');
      data.append('new_pass',new_pass);
      data.append('confirm_pass',confirm_pass);

      let xhr = new XMLHttpRequest();
      xhr.open("POST","ajax/profile.php",true);

      xhr.onload = function()
      {
        if(this.responseText == 'mismatch'){
          alert('error',"Password do not match!");
        }
        else if(this.responseText == 0){
          alert('error',"Updation failed!");
        }
        else{
          alert('success','Changes saved!');
          pass_form.reset();
        }
      }

      xhr.send(data);
    });

    // Delete Account Functionality
    let deleteAccountBtn = document.getElementById('deleteAccountBtn');
    let deleteAccountModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
    let confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    let deleteSpinner = document.getElementById('deleteSpinner');

    deleteAccountBtn.addEventListener('click', function() {
      deleteAccountModal.show();
    });

    // Resend Verification Email
    var resendBtn = document.getElementById('resendVerifyBtn');
    if(resendBtn){
      resendBtn.addEventListener('click', function(){
        var btnText = document.getElementById('resendBtnText');
        var spinner = document.getElementById('resendSpinner');
        resendBtn.disabled = true;
        spinner.classList.remove('d-none');
        btnText.textContent = 'Sending...';

        var data = new FormData();
        data.append('resend_verification','');

        var xhr = new XMLHttpRequest();
        xhr.open('POST','ajax/login_register.php',true);
        xhr.onload = function(){
          spinner.classList.add('d-none');
          resendBtn.disabled = false;
          if(this.responseText === 'sent'){
            btnText.textContent = 'Sent!';
            Swal.fire({icon:'success',title:'Email Sent!',text:'A new verification link has been sent to your email.',timer:3000,showConfirmButton:false});
          } else if(this.responseText === 'mail_unavailable'){
            btnText.textContent = 'Resend Verification Email';
            Swal.fire({icon:'error',title:'Email Not Available',text:'Verification email is not configured on the server right now.'});
          } else if(this.responseText.startsWith('mail_failed|')){
            btnText.textContent = 'Resend Verification Email';
            Swal.fire({icon:'error',title:'Failed',text:this.responseText.split('|').slice(1).join('|')});
          } else if(this.responseText === 'already_verified'){
            btnText.textContent = 'Resend Verification Email';
            Swal.fire({icon:'info',title:'Already Verified',text:'Your account is already verified. Please refresh the page.'});
          } else {
            btnText.textContent = 'Resend Verification Email';
            Swal.fire({icon:'error',title:'Failed',text:'Could not send email. Please try again later.'});
          }
        };
        xhr.onerror = function(){
          spinner.classList.add('d-none');
          resendBtn.disabled = false;
          btnText.textContent = 'Resend Verification Email';
          Swal.fire({icon:'error',title:'Error',text:'Network error. Please try again.'});
        };
        xhr.send(data);
      });
    }

    confirmDeleteBtn.addEventListener('click', function() {
      // Show spinner and disable button
      deleteSpinner.classList.remove('d-none');
      confirmDeleteBtn.disabled = true;

      fetch('ajax/delete_account.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          window.location.href = 'index.php';
        } else {
          alert('error', data.message || 'Error deleting account. Please try again.');
          deleteSpinner.classList.add('d-none');
          confirmDeleteBtn.disabled = false;
          deleteAccountModal.hide();
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('error', 'An unexpected error occurred. Please try again.');
        deleteSpinner.classList.add('d-none');
        confirmDeleteBtn.disabled = false;
        deleteAccountModal.hide();
      });
    });

  </script>


</body>
</html>
