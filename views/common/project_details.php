<?php
    session_start();
    require_once("../../models/projects.php");
    require_once("../../models/project_applications.php");
    require_once("../../models/project_members.php");

    if (!isset($_SESSION['user'])) {
        header("Location: ../login.php");
        exit();
    }

    $projectId = $_GET['id'] ?? null;
    if (!$projectId) {
        header("Location: home.php");
        exit();
    }

    $project = getProjectById($projectId);
    if (!$project) {
        header("Location: home.php");
        exit();
    }

    $isAdmin = $_SESSION['user']['role'] === 'admin';
    $isOwner = $_SESSION['user']['user_id'] == $project['owner_id'];
    $isApplicant = $_SESSION['user']['role'] === 'project_applicant';
    
    // Check if applicant already applied
    $hasApplied = false;
    if ($isApplicant) {
        $hasApplied = hasUserApplied($_SESSION['user']['user_id'], $projectId);
    }
    
    // Check if user is a member and get members list
    $isMember = isProjectMember($projectId, $_SESSION['user']['user_id']);
    $members = getProjectMembers($projectId); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['title']); ?> - Project Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./css/projectDetailsStyle.css">
</head>
<body>
    <?php include('../partials/navbar.php'); ?>

    <div class="main-content">
        <?php if (isset($_SESSION['project_success_message'])): ?>
            <div class="success-alert">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['project_success_message']; ?>
            </div>
            <?php unset($_SESSION['project_success_message']); ?>
        <?php endif; ?>

        <div class="project-details-container">
            <div class="details-header">
                <div class="header-left">
                    <a href="javascript:history.back()" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
                    <h1><?php echo htmlspecialchars($project['title']); ?></h1>
                    <span class="status-badge <?php echo $project['status']; ?>">
                        <i class="fas fa-circle"></i> <?php echo ucfirst($project['status']); ?>
                    </span>
                </div>
                
                <?php if ($isAdmin || $isOwner): ?>
                <div class="header-actions">
                    <button class="btn-edit" onclick="editProject(<?php echo $project['project_id']; ?>)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn-delete" onclick="deleteProject(<?php echo $project['project_id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
                <?php endif; ?>
            </div>

            <div class="project-content">
                <div class="project-image">
                    <?php 
                    $coverImage = (!empty($project['cover_image']) && $project['cover_image'] !== 'default_project.png') 
                        ? "../../resources/projects/{$project['cover_image']}" 
                        : "../../resources/default_project.png";
                    ?>
                    <img src="<?php echo htmlspecialchars($coverImage); ?>" 
                         alt="<?php echo htmlspecialchars($project['title']); ?>">
                </div>

                <div class="project-info-grid">
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-user"></i> Project Owner
                        </div>
                        <div class="info-value">
                            <?php echo htmlspecialchars($project['owner_name']); ?>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-users"></i> Team Size
                        </div>
                        <div class="info-value">
                            <?php echo $project['max_members']; ?> members
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-calendar"></i> Created Date
                        </div>
                        <div class="info-value">
                            <?php echo date('M d, Y', strtotime($project['created_at'])); ?>
                        </div>
                    </div>

                    <div class="info-card full-width">
                        <div class="info-label">
                            <i class="fas fa-info-circle"></i> Description
                        </div>
                        <div class="info-value description">
                            <?php echo htmlspecialchars($project['description']); ?>
                        </div>
                    </div>

                    <div class="info-card full-width">
                        <div class="info-label">
                            <i class="fas fa-code"></i> Required Skills
                        </div>
                        <div class="info-value">
                            <div class="skills-tags">
                                <?php 
                                $skills = explode(',', $project['required_skills']);
                                foreach ($skills as $skill): 
                                    $skill = trim($skill);
                                ?>
                                    <span class="skill-tag"><?php echo htmlspecialchars($skill); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    </div>
                </div>
            </div>
            
            <?php if ($isOwner || $isMember): ?>
            <div class="members-section">
                <h3><i class="fas fa-users"></i> Team Members (<?php echo count($members); ?>)</h3>
                <?php if (empty($members)): ?>
                    <p class="no-members">No members yet. Accept join requests to build your team!</p>
                <?php else: ?>
                    <div class="members-grid">
                        <?php foreach ($members as $member): ?>
                            <div class="member-card" data-member-id="<?php echo $member['user_id']; ?>">
                                <div class="member-info">
                                    <h4><i class="fas fa-user"></i> <?php echo htmlspecialchars($member['member_name']); ?></h4>
                                    <p class="member-email"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($member['member_email']); ?></p>
                                    <p class="member-joined">
                                        <i class="fas fa-calendar-alt"></i>
                                        Joined <?php echo date('M d, Y', strtotime($member['joined_at'])); ?>
                                    </p>
                                </div>
                                <?php if ($isOwner): ?>
                                    <button class="btn-remove-member" onclick="removeMember(<?php echo $member['user_id']; ?>, '<?php echo htmlspecialchars($member['member_name']); ?>')" title="Remove member">
                                        <i class="fas fa-times"></i>
                                    </button>
                                <?php elseif ($isMember && $member['user_id'] == $_SESSION['user']['user_id']): ?>
                                    <button class="btn-leave-project" onclick="leaveProject('<?php echo htmlspecialchars($member['member_name']); ?>')" title="Leave project">
                                        <i class="fas fa-sign-out-alt"></i> Leave
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($isApplicant && $project['status'] === 'active' && !$isOwner && !$isMember): ?>
            <div class="join-section">
                <h3><i class="fas fa-user-plus"></i> Interested in Joining?</h3>
                <?php if ($hasApplied): ?>
                    <button class="btn-join" disabled>
                        <i class="fas fa-check-circle"></i> Already Applied
                    </button>
                <?php else: ?>
                    <button class="btn-join" onclick="showJoinModal()">
                        <i class="fas fa-user-plus"></i> Send Join Request
                    </button>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Join Request Modal -->
    <div id="joinModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-paper-plane"></i> Send Join Request</h2>
                <span class="close" onclick="closeJoinModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="joinMessage">Message to Project Owner:</label>
                    <textarea id="joinMessage" placeholder="Tell the project owner why you want to join this project..." maxlength="500" oninput="updateCharCount()"></textarea>
                    <div class="char-count">
                        <span id="charCount">0</span> / 500 characters (minimum 20)
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeJoinModal()">Cancel</button>
                <button class="btn-send" onclick="sendJoinRequest(<?php echo $projectId; ?>)">Send Request</button>
            </div>
        </div>
    </div>

    <?php include('../partials/footer.php'); ?>

    <script src="js/project_details.js"></script>
</body>
</html>
