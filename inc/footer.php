<footer>
<div class="container-fluid bg-white mt-5">
  <div class="row">
    <div class="col-lg-4 p-4">
      <h3 class="h-font fw-bold fs-3 mb-2"><?php echo $settings_r['site_title'] ?></h3>
      <p>
        <?php echo $settings_r['site_about'] ?>
      </p>
    </div>
    <div class="col-lg-4 p-4">
      <h5 class="mb-3">Links</h5>
      <a href="index.php" class="d-inline-block mb-2 text-dark text-decoration-none">Home</a> <br>
      <a href="rooms.php" class="d-inline-block mb-2 text-dark text-decoration-none">Rooms</a> <br>
      <a href="facilities.php" class="d-inline-block mb-2 text-dark text-decoration-none">Facilities</a> <br>
      <a href="contact.php" class="d-inline-block mb-2 text-dark text-decoration-none">Contact us</a> <br>
      <a href="about.php" class="d-inline-block mb-2 text-dark text-decoration-none">About</a>
    </div>
    <div class="col-lg-4 p-4">
        <h5 class="mb-3">Follow us</h5>
        <?php 
          if($contact_r['tw']!=''){
            echo<<<data
              <a href="$contact_r[tw]" class="d-inline-block text-dark text-decoration-none mb-2">
                <i class="bi bi-twitter me-1"></i> Twitter
              </a><br>
            data;
          }
        ?>
        <a href="<?php echo $contact_r['fb'] ?>" class="d-inline-block text-dark text-decoration-none mb-2">
          <i class="bi bi-facebook me-1"></i> Facebook
        </a><br>
        <a href="<?php echo $contact_r['insta'] ?>" class="d-inline-block text-dark text-decoration-none">
          <i class="bi bi-instagram me-1"></i> Instagram
        </a><br>
    </div>
  </div>
</div>

<h6 class="text-center bg-dark text-white p-3 m-0">Designed and Developed by Keith Eimreh C. Lara</h6>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<script>

  (function(){
    const configured = "<?php echo rtrim(SITE_URL, '/'); ?>";
    const parsedPath = "<?php echo rtrim(parse_url(SITE_URL, PHP_URL_PATH) ?? '/SE', '/'); ?>";
    const path = parsedPath || '';
    const normalized = configured.replace(/^https?:\/\/[^\/]+/,'');
    const basePath = normalized || path || '';
    window.APP_BASE_URL = window.location.origin + basePath + '/';
  })();

  function alert(type,msg,position='body')
  {
    let bs_class = 'alert-danger';
    if(type === 'success'){
      bs_class = 'alert-success';
    }
    else if(type === 'warning'){
      bs_class = 'alert-warning';
    }
    else if(type === 'info'){
      bs_class = 'alert-info';
    }
    let element = document.createElement('div');
    element.innerHTML = `
      <div class="alert ${bs_class} alert-dismissible fade show" role="alert">
        <strong class="me-3">${msg}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    `;

    if(position=='body'){
      document.body.append(element);
      element.classList.add('custom-alert');
    }
    else{
      document.getElementById(position).appendChild(element);
    }
    setTimeout(remAlert, 3000);
  }

  function remAlert(){
    const activeAlert = document.getElementsByClassName('alert')[0];
    if(activeAlert){
      activeAlert.remove();
    }
  }

  function setActive()
  {
    let navbar = document.getElementById('nav-bar');
    let a_tags = navbar.getElementsByTagName('a');

    for(i=0; i<a_tags.length; i++)
    {
      let file = a_tags[i].href.split('/').pop();
      let file_name = file.split('.')[0];

      if(document.location.href.indexOf(file_name) >= 0){
        a_tags[i].classList.add('active');
      }

    }
  }

  let register_form = document.getElementById('register-form');

  if(register_form) register_form.addEventListener('submit', (e)=>{
    e.preventDefault();

    let data = new FormData();

    const requiredFields = ['name','username','email','phonenum','address','dob','pass','cpass','profile'];
    for(const field of requiredFields){
      if(!register_form.elements[field]){
        alert('error',`Registration form is missing the "${field}" field.`);
        return;
      }
    }

    data.append('name',register_form.elements['name'].value);
    data.append('username',register_form.elements['username'].value);
    data.append('email',register_form.elements['email'].value);
    data.append('phonenum',register_form.elements['phonenum'].value);
    data.append('address',register_form.elements['address'].value);
    data.append('dob',register_form.elements['dob'].value);
    data.append('pass',register_form.elements['pass'].value);
    data.append('cpass',register_form.elements['cpass'].value);
    data.append('profile',register_form.elements['profile'].files[0]);
    data.append('register','');

    if(document.activeElement){ document.activeElement.blur(); }
    var myModal = document.getElementById('registerModal');
    var modal = bootstrap.Modal.getInstance(myModal);
    modal.hide();

    let xhr = new XMLHttpRequest();
    xhr.open("POST","ajax/login_register.php",true);

    xhr.onload = function(){
      if(this.responseText == 'pass_mismatch'){
        alert('error',"Password Mismatch!");
      }
      else if(this.responseText == 'email_already'){
        alert('error',"Email is already registered!");
      }
      else if(this.responseText == 'username_already'){
        alert('error',"Username is already taken!");
      }
      else if(this.responseText == 'phone_already'){
        alert('error',"Phone number is already registered!");
      }
      else if(this.responseText == 'inv_img'){
        alert('error',"Only JPG, WEBP & PNG images are allowed!");
      }
      else if(this.responseText == 'upd_failed'){
        alert('error',"Image upload failed!");
      }
      else if(this.responseText == 'mail_unavailable'){
        alert('error',"Registration is unavailable right now because verification email is not configured on the server.");
      }
      else if(this.responseText.startsWith('mail_failed|')){
        alert('error', this.responseText.split('|').slice(1).join('|'));
      }
      else if(this.responseText == 'ins_failed'){
        alert('error',"Registration failed! Server down!");
      }
      else if(this.responseText == 'verify_email'){
        alert('success',"Registration successful! Please check your email and click the verification link to activate your account.");
        register_form.reset();
      }
      else if(this.responseText.trim() === '1'){
        alert('success',"Registration successful!");
        register_form.reset();
      }
      else{
        alert('error', this.responseText ? this.responseText : "Registration failed!");
      }
    }

    xhr.send(data);
  });

  let login_form = document.getElementById('login-form');

  if(login_form) login_form.addEventListener('submit', (e)=>{
    e.preventDefault();

    let data = new FormData();

    data.append('email_mob',login_form.elements['email_mob'].value);
    data.append('pass',login_form.elements['pass'].value);
    data.append('login','');

    if(document.activeElement){ document.activeElement.blur(); }
    var myModal = document.getElementById('loginModal');
    var modal = bootstrap.Modal.getInstance(myModal);
    modal.hide();

    let xhr = new XMLHttpRequest();
    xhr.open("POST","ajax/login_register.php",true);

    xhr.onload = function(){
      if(this.responseText == 'inv_email_mob'){
        alert('error',"Invalid Email or Mobile Number!");
      }
      else if(this.responseText == 'not_verified'){
        alert('warning',"Your email is not verified. Please check your inbox for the verification link. You can still log in, but booking features will be restricted.");
      }
      else if(this.responseText == 'inactive'){
        alert('error',"Account Suspended! Please contact Admin.");
      }
      else if(this.responseText == 'invalid_pass'){
        alert('error',"Incorrect Password!");
      }
      else if(this.responseText == 'invalid_credentials'){
        alert('error',"Invalid email, username, mobile, or password!");
      }
      else if(this.responseText.trim() === '1'){
        // success login: SweetAlert then reload
        Swal.fire({
          icon: 'success',
          title: 'Welcome back!',
          text: 'Login successful',
          timer: 1200,
          showConfirmButton: false
        }).then(()=>{
          let fileurl = window.location.href.split('/').pop().split('?').shift();
          if(fileurl == 'room_details.php'){
            window.location = window.location.href;
          }
          else{
            window.location = window.location.pathname;
          }
        });
      }
      else{
        alert('error',"Login failed! Please try again.");
      }
    }

    xhr.send(data);
  });

  let forgot_form = document.getElementById('forgot-form');

  if(forgot_form) forgot_form.addEventListener('submit', (e)=>{
    e.preventDefault();

    let data = new FormData();

    data.append('email',forgot_form.elements['email'].value);
    data.append('forgot_pass','');

    if(document.activeElement){ document.activeElement.blur(); }
    var myModal = document.getElementById('forgotModal');
    var modal = bootstrap.Modal.getInstance(myModal);
    modal.hide();

    let xhr = new XMLHttpRequest();
    xhr.open("POST","ajax/login_register.php",true);

    xhr.onload = function(){
      if(this.responseText == 'inv_email'){
        alert('error',"No account found with that email address.");
      }
      else if(this.responseText == 'inactive'){
        alert('error',"Account Suspended! Please contact Admin.");
      }
      else if(this.responseText == 'mail_unavailable'){
        alert('error',"Password reset email is not available right now because SMTP is not configured on the server.");
      }
      else if(this.responseText.startsWith('mail_failed|')){
        alert('error', this.responseText.split('|').slice(1).join('|'));
      }
      else if(this.responseText == 'upd_failed'){
        alert('error',"Account recovery failed. Please try again later.");
      }
      else if(this.responseText.trim() === '1'){
        alert('success',"Password reset link sent! Please check your email.");
        forgot_form.reset();
      }
      else{
        alert('error',"Something went wrong. Please try again.");
      }
    }

    xhr.send(data);
  });

  var USER_IS_VERIFIED = <?php echo (isset($_SESSION['login']) && $_SESSION['login'] && isset($_SESSION['is_verified'])) ? (int)$_SESSION['is_verified'] : -1; ?>;

  function checkLoginToBook(status,room_id){
    if(!status){
      alert('error','Please login to book a room!');
    }
    else if(USER_IS_VERIFIED === 0){
      Swal.fire({
        icon: 'warning',
        title: 'Email Not Verified',
        text: 'Please verify your email address before making a booking.',
        confirmButtonText: 'Go to Profile',
        confirmButtonColor: '#c8a951'
      }).then(function(){
        window.location.href = 'profile.php';
      });
    }
    else{
      window.location.href='confirm_booking.php?id='+room_id;
    }
  }

  setActive();

  // logout confirmation
  document.addEventListener('click', function(e){
    const target = e.target.closest('#logout-link');
    if(target){
      e.preventDefault();
      Swal.fire({
        title: 'Logout?',
        text: 'Are you sure you want to logout?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, logout',
        cancelButtonText: 'Cancel'
      }).then((result)=>{
        if(result.isConfirmed){
          window.location.href = target.getAttribute('href');
        }
      });
    }
  });

  // NAVBAR SCROLL EFFECT - Global for all pages
  (function(){
    const navbar = document.getElementById('nav-bar');
    if(!navbar) return;

    // Check if page has a hero section (index.php)
    const hasHero = document.querySelector('.hero-section');
    
    function updateNavbar() {
      if(window.scrollY > 100) {
        navbar.classList.remove('navbar-dark-mode');
        navbar.classList.add('navbar-light-mode');
      } else {
        if(hasHero) {
          navbar.classList.remove('navbar-light-mode');
          navbar.classList.add('navbar-dark-mode');
        } else {
          navbar.classList.remove('navbar-dark-mode');
          navbar.classList.add('navbar-light-mode');
        }
      }
    }

    // Initial state
    if(hasHero) {
      navbar.classList.add('navbar-dark-mode');
    } else {
      navbar.classList.add('navbar-light-mode');
    }

    window.addEventListener('scroll', updateNavbar);
  })();

</script>

<?php 
  if(isset($_SESSION['login']) && $_SESSION['login'] === true){
    $notif_script_path = __DIR__ . '/../js/notifications.js';
    $notif_version = file_exists($notif_script_path) ? filemtime($notif_script_path) : time();
    echo '<script src="'.SITE_URL.'js/notifications.js?v='.$notif_version.'"></script>';
  }
?>
