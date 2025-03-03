<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "username", "password", "databse");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get logged-in user's email
$user_email = $_SESSION['user_email'];

// Validate stage session (must be between 1 and 5)
if (!isset($_SESSION['stage']) || $_SESSION['stage'] < 1 || $_SESSION['stage'] > 5) {
    $_SESSION['stage'] = 1; // Reset to Stage 1 if invalid
}

$current_stage = $_SESSION['stage'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['previous'])) {
      $_SESSION['stage'] = max(1, $_SESSION['stage'] - 1);
  } else {
      foreach ($_POST['answers'] as $question_id => $answer) {
          $stmt = $conn->prepare("INSERT INTO user_answers (user_email, question_id, answer) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE answer=?");
          $stmt->bind_param("siss", $user_email, $question_id, $answer, $answer);
          $stmt->execute();
          $stmt->close();
      }

      if ($_SESSION['stage'] < 5) {
          $_SESSION['stage']++;
      } else {
          header("Location: user_result.php");
          exit();
      }
  }

  // Force session persistence before redirecting
  session_write_close();
  
  header("Location: user_form.php");
  exit();
}

// Fetch questions for the current stage
$stmt = $conn->prepare("SELECT id, question_text, answer_type, custom_options FROM questions WHERE stage = ?");
$stmt->bind_param("i", $current_stage);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en-US" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--    Document Title-->
    <title>Student Dashboard</title>

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
        <div class="container"><a class="navbar-brand" href="index.html"><img src="assets/img/logo.jpeg" height="50" alt="logo" /></a>
          <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"> </span></button> -->
         
        </div>
      </nav>

      <!-- <section> begin -->
      <section class="vh-100 d-flex pt-5 mb-6 justify-content-center" id="feature" style="background-color: #0d6efd;">
    <div class="container mt-5">
        <h2 class="text-center text-white">Stage <?php echo $current_stage; ?> Questions</h2>
        
        <div class="card">
            <div class="card-body">
                <form method="post">
                    <?php foreach ($questions as $question): ?>
                        <p><strong><?php echo htmlspecialchars($question['question_text']); ?></strong></p>
                        <?php if ($question['answer_type'] == 'yes_no'): ?>
                            <label><input type="radio" name="answers[<?php echo $question['id']; ?>]" value="Yes" required> Yes</label>
                            <label><input type="radio" name="answers[<?php echo $question['id']; ?>]" value="No" required> No</label>
                        <?php elseif ($question['answer_type'] == 'custom'): ?>
                            <?php $options = explode(',', $question['custom_options']); ?>
                            <?php foreach ($options as $option): ?>
                                <label><input type="radio" name="answers[<?php echo $question['id']; ?>]" value="<?php echo trim($option); ?>" required> <?php echo trim($option); ?></label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <hr>
                    <?php endforeach; ?>

                    <div class="d-flex justify-content-between">
                        <button type="submit" name="previous" class="btn btn-warning" <?php echo ($current_stage == 1) ? 'disabled' : ''; ?>>Previous Stage</button>
                        <button type="submit" class="btn btn-primary"><?php echo ($current_stage == 5) ? 'Submit Final' : 'Next Stage'; ?></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-3">
            <a href="user_dashboard.php" class="btn btn-light">Back to Dashboard</a>
        </div>
    </div>
</section>
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
    <script src="vendors/@popperjs/popper.min.js"></script>
    <script src="vendors/bootstrap/bootstrap.min.js"></script>
    <script src="vendors/is/is.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
    <script src="vendors/fontawesome/all.min.js"></script>
    <script src="assets/js/theme.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&amp;family=Volkhov:wght@700&amp;display=swap" rel="stylesheet">

    <script src="script.js"></script>
  </body>

</html>