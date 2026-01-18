<!DOCTYPE html>
<html>
<head>
    <style>

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            padding: 8px 15px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            font-weight: 500;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="#" class="navbar-brand">
                🎓 Student Team Finder
            </a>

            <div class="navbar-toggle" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <ul class="navbar-menu" id="navbarMenu">
                <li><a href="<?php echo getBasePath(); ?>home.php" class="navbar-link">Home</a></li>
                <li><a href="<?php echo getBasePath(); ?>dashboard.php" class="navbar-link">Dashboard</a></li>
                <li><a href="<?php echo getBasePath(); ?>profile.php" class="navbar-link">Profile</a></li>
                
                <li class="user-profile">
                    
                    <span><?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
                </li>

                <li>
                    <form method="post" action="<?php echo getBasePath(); ?>../logout.php" style="margin: 0;">
                        <button type="submit" class="logout-btn">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <script>
        function toggleMenu() {
            const menu = document.getElementById('navbarMenu');
            menu.classList.toggle('active');
        }

       
        document.addEventListener('click', function(event) {
            const navbar = document.querySelector('.navbar');
            const menu = document.getElementById('navbarMenu');
            
            if (!navbar.contains(event.target)) {
                menu.classList.remove('active');
            }
        });

        
        const currentPage = window.location.pathname.split('/').pop();
        const navLinks = document.querySelectorAll('.navbar-link');
        
        navLinks.forEach(link => {
            const linkPage = link.getAttribute('href').split('/').pop();
            if (linkPage === currentPage) {
                link.classList.add('active');
            }
        });
    </script>
</body>
</html>

<?php
function getBasePath() {
    $role = $_SESSION['user']['role'] ?? '';
    
    switch($role) {
        case 'admin':
            return '../../views/admin/';
        case 'project_owner':
            return '../../views/project_owner/';
        case 'project_applicant':
            return '../../views/project_applicant/';
        default:
            return '../';
    }
}

// // Helper function to get the correct image path
// function getImagePath($imageName) {
//     $role = $_SESSION['user']['role'] ?? '';
    
//     switch($role) {
//         case 'admin':
//             return '../../resources/admin/image/' . $imageName;
//         case 'project_owner':
//             return '../../resources/project_owner/image/' . $imageName;
//         case 'project_applicant':
//             return '../../resources/project_applicant/image/' . $imageName;
//         default:
//             return '../../resources/default/' . $imageName;
//     }
// }
?>
