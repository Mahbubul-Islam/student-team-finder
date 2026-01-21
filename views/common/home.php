<?php
require_once('../../controllers/authCheck.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Student Team Finder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/homeStyle.css">
</head>
<body>
    <?php include('../partials/navbar.php'); ?>

    <div class="main-content">
        <div class="home-header">
            <h1><i class="fas fa-home"></i> All Projects</h1>
            <p>Explore and join exciting student projects</p>
        </div>

        <div class="search-container">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search projects by title, description, or skills..." oninput="searchProjects()">
                <button class="btn-clear" id="clearSearch" onclick="clearSearch()" style="display: none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="projects-container" id="projectsContainer">
            <!-- Projects will be loaded here -->
        </div>
    </div>

    <?php include('../partials/footer.php'); ?>

    <script src="js/home.js"></script>
</body>
</html>
