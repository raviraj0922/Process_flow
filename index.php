<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stage Wise Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        <!--    Favicons-->
        <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicons/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicons/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicons/favicon.ico">
    <link rel="manifest" href="assets/img/favicons/manifest.json">
    <meta name="msapplication-TileImage" content="assets/img/favicons/favicon.ico">
    <meta name="theme-color" content="#ffffff">

    <!--    Stylesheets-->
    <link href="assets/css/theme.css" rel="stylesheet" />
</head>

<body>

    <!--    Main Content-->
    <main class="main" id="top">
      <nav class="navbar navbar-expand-lg navbar-light sticky-top" data-navbar-on-scroll="data-navbar-on-scroll">
        <div class="container">
          <a class="navbar-brand" href="index.php">
            <img src="assets/img/logo.jpeg" height="50" alt="logo" />
          </a>
          <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"> </span>
          </button> -->
          </div>
        </div>
      </nav>

      <!-- <section> begin -->
        <section class="vh-100 d-flex align-items-center justify-content-center" style="background-color: #0d6efd;">
          <div class="container">
              <div class="row align-items-center">
                  <!-- Left Column -->
                  <div class="col-lg-6 text-white">
                      <h1 class="fw-bold mb-4">Get Started</h1>
                      <ol class="ps-3">
                          <li class="mb-3">
                              <h5 class="fw-bold">Sign Up</h5>
                              <p>Create your account and check the submitted questions.</p>
                          </li>
                          <li class="mb-3">
                              <h5 class="fw-bold">Stage wise submitted responce</h5>
                              <p>Question and answers details.</p>
                          </li>
                          <li>
                              <h5 class="fw-bold">Start Applying</h5>
                              <p>Manage your data.</p>
                          </li>
                      </ol>
                  </div>
      
                  <!-- Right Column -->
                  <div class="col-lg-6">
                    <div class="card shadow-lg p-4 rounded-4">
                        <div class="card-body">
                            <h2 class="fw-bold mb-4">Start your journey</h2>
                            <form id="otp-form">
                                <div class="mb-3">
                                    <label for="email" class="form-label visually-hidden">Email</label>
                                    <input type="email" class="form-control" id="email" placeholder="Email" required>
                                </div>
                                <button type="button" class="btn btn-warning w-100 fw-bold" id="get-otp-btn" onclick="sendOTP(event)">Submit to Get OTP</button>
                                <div class="mt-3" id="otp-section" style="display: none;">
                                    <label for="otp" class="form-label visually-hidden">OTP</label>
                                    <input type="text" class="form-control mb-3" id="otp" placeholder="Enter OTP" required>
                                    <button type="button" class="btn btn-primary w-100 fw-bold" onclick="verifyOTP(event)">Verify OTP</button>
                                </div>
                            </form>
                            <p class="text-center mt-4 mb-0">Already completed the journey? <a href="login-registration.php" class="text-decoration-none fw-bold">LOGIN</a></p>
                        </div>
                    </div>
                </div>
                
              </div>
          </div>
      </section>
      <script>
        function sendOTP(event) {
    event.preventDefault();
    let email = document.getElementById("email").value;

    fetch("otp_handler.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=send_otp&email=${email}`
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === "success") {
            document.getElementById("otp-section").style.display = "block";
        }
    })
    .catch(error => console.error("Error:", error));
}

document.getElementById("otp-form").addEventListener("submit", function (e) {
    e.preventDefault(); // Form submit hone se roko
    var email = document.getElementById("email").value;
    var otp = document.getElementById("otp").value;

    fetch("otp_handler.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=verify_otp&email=${email}&otp=${otp}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert(data.message);
            window.location.href = data.redirect; // ✅ Redirect to stage-registration.php
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error("Error:", error));
});
      </script>
      <!-- <section> close -->

      <!-- <section> begin -->
      <section class="pb-2 pb-lg-5">

        <div class="container">
          <div class="row border-top border-top-secondary pt-7">
            <div class="col-lg-3 col-md-6 mb-4 mb-md-6 mb-lg-0 mb-sm-2 order-1 order-md-1 order-lg-1"><img class="mb-4" src="assets/img/logo.jpeg" width="184" alt="" /></div>
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0 order-3 order-md-3 order-lg-2">
              <p class="fs-2 mb-lg-4">Quick Links</p>
              <ul class="list-unstyled mb-0">
                <li class="mb-1"><a class="link-900 text-secondary text-decoration-none" href="#!">About us</a></li>
                <li class="mb-1"><a class="link-900 text-secondary text-decoration-none" href="#!">Blog</a></li>
                <li class="mb-1"><a class="link-900 text-secondary text-decoration-none" href="#!">Contact</a></li>
                <li class="mb-1"><a class="link-900 text-secondary text-decoration-none" href="#!">FAQ</a></li>
              </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0 order-4 order-md-4 order-lg-3">
              <p class="fs-2 mb-lg-4">Legal stuff</p>
              <ul class="list-unstyled mb-0">
                <li class="mb-1"><a class="link-900 text-secondary text-decoration-none" href="#!">Disclaimer</a></li>
                <li class="mb-1"><a class="link-900 text-secondary text-decoration-none" href="#!">Financing</a></li>
                <li class="mb-1"><a class="link-900 text-secondary text-decoration-none" href="#!">Privacy Policy</a></li>
                <li class="mb-1"><a class="link-900 text-secondary text-decoration-none" href="#!">Terms of Service</a></li>
              </ul>
            </div>
            <div class="col-lg-3 col-md-6 col-6 mb-4 mb-lg-0 order-2 order-md-2 order-lg-4">
              <p class="fs-2 mb-lg-4">
                knowing you're always on the best energy deal.</p>
              <form class="mb-3">
                <input class="form-control" type="email" placeholder="Enter your phone Number" aria-label="phone" />
              </form>
              <button class="btn btn-warning fw-medium py-1">Sign up Now</button>
            </div>
          </div>
        </div><!-- end of .container-->

      </section>
      <!-- <section> close -->

      <!-- <section> begin -->
      <section class="text-center py-0">

        <div class="container">
          <div class="container border-top py-3">
            <div class="row justify-content-between">
              <div class="col-12 col-md-auto mb-1 mb-md-0">
                <p class="mb-0">&copy; 2025 Stagewise Application</p>
              </div>
              <div class="col-12 col-md-auto">
                <p class="mb-0">
                  Made with<span class="fas fa-heart mx-1 text-danger"> </span>by<a class="text-decoration-none ms-1" href="#" target="_blank">In House IT</a></p>
              </div>
            </div>
          </div>
        </div><!-- end of .container-->

      </section>
      <!-- <section> close -->


    </main>
    <!--    End of Main Content-->

    <!--    JavaScripts-->
    <script>
          // Get all navbar links
      const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

      // Highlight the active navbar link based on the current page URL
      function setActiveNavLink() {
        const currentPage = window.location.pathname.split('/').pop(); // Get the current file name
        navLinks.forEach(link => {
          if (link.getAttribute('href') === currentPage) {
            link.classList.add('active'); // Add the 'active' class to the matching link
          } else {
            link.classList.remove('active'); // Remove the 'active' class from non-matching links
          }
        });
      }

      // Run the function on page load
      document.addEventListener('DOMContentLoaded', setActiveNavLink);

    </script>
    <script src="vendors/@popperjs/popper.min.js"></script>
    <script src="vendors/bootstrap/bootstrap.min.js"></script>
    <script src="vendors/is/is.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
    <script src="vendors/fontawesome/all.min.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="utils.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&amp;family=Volkhov:wght@700&amp;display=swap" rel="stylesheet">
  </body>

</html>