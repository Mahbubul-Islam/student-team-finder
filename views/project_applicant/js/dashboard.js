function loadApplicantData() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "../../controllers/fetch_applicant_stats.php", true);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    const data = JSON.parse(xhr.responseText);
                    
                    
                    document.getElementById('pendingCount').textContent = data.pending;
                    document.getElementById('acceptedCount').textContent = data.accepted;
                    document.getElementById('rejectedCount').textContent = data.rejected;
                    
                    
                    updateApplicationsTable(data.applications);
                }
            };

            xhr.send();
        }

        function updateApplicationsTable(applications) {
            const tbody = document.getElementById('applicationsTableBody');
            if (!tbody) return;

            if (applications.length === 0) {
                const noAppsSection = document.querySelector('.applications-section');
                if (noAppsSection) {
                    noAppsSection.innerHTML = `
                        <div class="section-header">
                            <h2><i class="fas fa-list"></i> My Applications</h2>
                        </div>
                        <div class="no-applications">
                            <i class="fas fa-inbox"></i>
                            <p>You haven't applied to any projects yet</p>
                            <a href="../common/home.php" class="btn-browse">
                                <i class="fas fa-search"></i> Browse Projects
                            </a>
                        </div>
                    `;
                }
                return;
            }

            let html = '';
            applications.forEach(app => {
                const statusIcon = app.status === 'pending' ? 'clock' : 
                                 (app.status === 'accepted' ? 'check-circle' : 'times-circle');
                
                html += `
                    <tr>
                        <td class="project-title">
                            <i class="fas fa-project-diagram"></i>
                            ${app.project_title}
                        </td>
                        <td>${app.owner_name}</td>
                        <td>
                            <span class="status-badge ${app.project_status}">
                                ${capitalize(app.project_status)}
                            </span>
                        </td>
                        <td>
                            <span class="application-badge ${app.status}">
                                <i class="fas fa-${statusIcon}"></i>
                                ${capitalize(app.status)}
                            </span>
                        </td>
                        <td>${formatDate(app.applied_at)}</td>
                        <td class="action-buttons">
                            <button class="btn-view" onclick="window.location.href='../common/project_details.php?id=${app.project_id}'">
                                <i class="fas fa-eye"></i> View
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

        
        loadApplicantData();
        
        // Auto-refresh every 5 seconds
        setInterval(loadApplicantData, 5000);