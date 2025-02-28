<?php
session_start();
$conn = new mysqli("localhost", "username", "password", "databse");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user session exists
if (!isset($_SESSION['email'])) {
    header("Location: form.php");
    exit();
}

$email = $_SESSION['email'];
$total_stages = 5;

// Fetch all questions grouped by stage
$query = "SELECT id, stage, question_text, answer_type FROM questions ORDER BY stage, id";
$result = $conn->query($query);

$questions_by_stage = [];
while ($row = $result->fetch_assoc()) {
    $questions_by_stage[$row['stage']][] = [
        'id' => $row['id'],
        'question_text' => $row['question_text'],
        'answer_type' => $row['answer_type']
    ];
}

// Fetch user's answers
$query = "SELECT q.stage, ua.question_id, ua.answer, q.answer_type 
          FROM user_answers ua 
          JOIN questions q ON ua.question_id = q.id 
          WHERE ua.user_email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$user_answers = [];
while ($row = $result->fetch_assoc()) {
    $user_answers[$row['stage']][$row['question_id']] = [
        'answer' => $row['answer'],
        'answer_type' => $row['answer_type']
    ];
}
$stmt->close();

// Calculate completion percentage
$stage_completion = [];
$total_percentage = 0;

for ($stage = 1; $stage <= $total_stages; $stage++) {
    $valid_questions = 0;
    $answered_questions = 0;

    if (isset($questions_by_stage[$stage])) {
        foreach ($questions_by_stage[$stage] as $question) {
            $question_id = $question['id'];
            $answer_type = $question['answer_type'];
            $answer = isset($user_answers[$stage][$question_id]) ? $user_answers[$stage][$question_id]['answer'] : null;

            // Only count "Yes" and custom answers
            if ($answer !== "No") {
                $valid_questions++;

                // Count if answered (ignores empty responses)
                if (!empty($answer)) {
                    $answered_questions++;
                }
            }
        }
    }

    // Calculate completion percentage
    $completion = ($valid_questions > 0) ? ($answered_questions / $valid_questions) * 100 : 100;
    $stage_completion[$stage] = round($completion, 2);
    $total_percentage += $completion;
}

// Calculate overall completion
$overall_completion = round($total_percentage / $total_stages, 2);

// Handle final submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("INSERT INTO registrations (email, completed_at) VALUES (?, NOW()) ON DUPLICATE KEY UPDATE completed_at=NOW()");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->close();

    session_destroy();
    header("Location: stages.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en-US" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--    Document Title-->
    <title>Final Review</title>

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
        <div class="container"><a class="navbar-brand" href="index.php"><img src="assets/img/logo.jpeg" height="50" alt="logo" /></a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"> </span></button>
         
        </div>
      </nav>

    <!-- <section> begin -->
    <section class="pt-5 mb-6" id="feature">
        <div class="container mt-5">
            <h2>Final Review of Your Responses</h2>

            <?php foreach ($questions_by_stage as $stage => $questions): ?>
                <h3>Stage <?php echo $stage; ?></h3>
                <ul>
                    <?php foreach ($questions as $q): 
                        $answer = isset($user_answers[$stage][$q['id']]) ? $user_answers[$stage][$q['id']]['answer'] : "Not Answered"; ?>
                        <li><strong><?php echo $q['question_text']; ?><br></strong>Answer: <?php echo $answer; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>

            <!-- <h3>Stage Completion Progress</h3> -->
            <!-- <ul>
                <?php foreach ($stage_completion as $stage => $percentage): ?>
                    <li><strong>Stage <?php echo $stage; ?>:</strong> <?php echo $percentage; ?>%</li>
                <?php endforeach; ?>
            </ul> -->

            <!-- <h3>Overall Completion: <?php echo $overall_completion; ?>%</h3> -->

            <form method="post">
                <button type="submit" class="btn btn-primary">Submit Registration</button>
                &nbsp;&nbsp;&nbsp;&nbsp;
            <a href="stages.php" class="btn btn-danger">Back to Stage</a>
            </form>
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