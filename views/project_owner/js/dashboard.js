
        function acceptRequest(applicationId) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../../controllers/acceptJoinRequestControl.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function () {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        window.location.reload();
                    } else {
                        window.location.reload();
                    }
                } else {
                    window.location.reload();
                }
            };

            xhr.onerror = function () {
                window.location.reload();
            };

            xhr.send('application_id=' + applicationId);
        }

        function rejectRequest(applicationId) {
            if (confirm('Are you sure you want to reject this join request?')) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "../../controllers/rejectJoinRequestControl.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                xhr.onload = function () {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            window.location.reload();
                        } else {
                            window.location.reload();
                        }
                    } else {
                        window.location.reload();
                    }
                };

                xhr.onerror = function () {
                    window.location.reload();
                };

                xhr.send('application_id=' + applicationId);
            }
        }

        function editProject(projectId) {
            window.location.href = '../common/editProject.php?id=' + projectId;
        }

        function viewProject(projectId) {
            window.location.href = '../common/project_details.php?id=' + projectId;
        }

        function deleteProject(projectId, projectTitle) {
            if (confirm('Are you sure you want to delete project "' + projectTitle + '"? This action cannot be undone.')) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "../../controllers/deleteProjectControl.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                xhr.onload = function () {
                    window.location.reload();
                };

                xhr.onerror = function () {
                    window.location.reload();
                };

                xhr.send('project_id=' + projectId);
            }
        }

        
        function loadOwnerData() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "../../controllers/fetch_owner_stats.php", true);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    const data = JSON.parse(xhr.responseText);
                    
                    
                    document.getElementById('totalProjects').textContent = data.total_projects;
                    document.getElementById('activeProjects').textContent = data.active_projects;
                    document.getElementById('closedProjects').textContent = data.closed_projects;
                    
                   
                    updateJoinRequestsTable(data.join_requests);
                    
                   
                    updateProjectsTable(data.projects);
                }
            };

            xhr.send();
        }

        function updateJoinRequestsTable(requests) {
            const tbody = document.getElementById('requestsTableBody');
            if (!tbody) return;

            if (requests.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="no-requests"><i class="fas fa-inbox"></i><p>No pending join requests</p></td></tr>';
                return;
            }

            let html = '';
            requests.forEach(request => {
                html += `
                    <tr>
                        <td class="applicant-name">
                            <i class="fas fa-user"></i>
                            ${request.applicant_name}
                        </td>
                        <td>${request.project_title}</td>
                        <td>${formatDate(request.applied_at)}</td>
                        <td><div class="message-preview">${request.message}</div></td>
                        <td class="action-buttons">
                            <button class="btn-accept" onclick="acceptRequest(${request.application_id})">
                                <i class="fas fa-check"></i> Accept
                            </button>
                            <button class="btn-reject" onclick="rejectRequest(${request.application_id})">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        function updateProjectsTable(projects) {
            const tbody = document.getElementById('projectsTableBody');
            if (!tbody) return;

            if (projects.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="no-projects"><i class="fas fa-folder-open"></i><p>You haven\'t created any projects yet</p></td></tr>';
                return;
            }

            let html = '';
            projects.forEach(project => {
                const isFull = project.member_count >= project.max_members;
                const membersClass = isFull ? 'members-full' : '';
                
                html += `
                    <tr>
                        <td class="project-title">
                            <i class="fas fa-project-diagram"></i>
                            ${project.title}
                        </td>
                        <td>
                            <span class="status-badge ${project.status}">
                                ${capitalize(project.status)}
                            </span>
                        </td>
                        <td>${formatDate(project.created_at)}</td>
                        <td class="members-count">
                            <span class="${membersClass}">
                                ${project.member_count || 0} / ${project.max_members}
                            </span>
                        </td>
                        <td class="action-buttons">
                            ${project.status === 'active' ? `
                                <button class="btn-edit" onclick="editProject(${project.project_id})">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn-view" onclick="viewProject(${project.project_id})">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            ` : `
                                <button class="btn-edit" disabled title="Closed projects cannot be edited">
                                    <i class="fas fa-ban"></i> Closed
                                </button>
                            `}
                            <button class="btn-delete" onclick="deleteProject(${project.project_id}, '${project.title}')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        

        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        // Initial load
        loadOwnerData();
        
        // Auto-refresh every 5 seconds
        setInterval(loadOwnerData, 5000);
    