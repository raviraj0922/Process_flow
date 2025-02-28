<?php
session_start();

// Database Connection
$conn = new mysqli("localhost", "username", "password", "databse");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination logic
$limit = 5; // Number of users per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Fetch users with pagination
$query = "SELECT id, email FROM users_otp ORDER BY id ASC LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

// Count total users
$count_query = "SELECT COUNT(*) AS total FROM users_otp";
$count_result = $conn->query($count_query);
$total_users = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_users / $limit);

// Fetch user answers if a user is selected
$user_answers = [];
$selected_email = "";
if (isset($_GET['view_answers'])) {
    $selected_email = $_GET['view_answers'];

    $query = "SELECT q.stage, q.question_text, ua.answer 
              FROM user_answers ua 
              JOIN questions q ON ua.question_id = q.id 
              WHERE ua.user_email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $selected_email);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $user_answers[$row['stage']][] = [
            'question_text' => $row['question_text'],
            'answer' => $row['answer']
        ];
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en-US" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--    Document Title-->
    <title>Manage Users</title>

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
      <section class="pt-5 mb-6" id="feature">
        <div class="container mt-5">
            <h2 class="text-center">Registered Users</h2>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <a href="?view_answers=<?php echo urlencode($user['email']); ?>&page=<?php echo $page; ?>" class="btn btn-info btn-sm">View Answers</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Pagination Controls -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo ($page - 1); ?>" class="btn btn-secondary">Previous</a>
                <?php endif; ?>&nbsp;&nbsp;
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo ($page + 1); ?>" class="btn btn-secondary">Next</a>
                <?php endif; ?>
            </div>

            <a href="admin_dashboard.php" class="btn btn-primary mt-3">Back to Dashboard</a>

            <!-- Show user answers dynamically -->
            <?php if (!empty($selected_email)) { ?>
                <h2 class="mt-5">User Submitted Answers (<?php echo htmlspecialchars($selected_email); ?>)</h2>
                <?php if (!empty($user_answers)) { ?>
                    <?php foreach ($user_answers as $stage => $questions): ?>
                        <h3>Stage <?php echo $stage; ?></h3>
                        <ul>
                            <?php foreach ($questions as $q): ?>
                                <li><strong><?php echo htmlspecialchars($q['question_text']); ?>:</strong> <?php echo htmlspecialchars($q['answer']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endforeach; ?>
                <?php } else { ?>
                    <p class="alert alert-warning">User did not submit any answers.</p>
                <?php } ?>
            <?php } ?>
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