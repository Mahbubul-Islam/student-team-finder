let currentSearchQuery = "";

function loadProjects(searchQuery = "") {
	currentSearchQuery = searchQuery;
	const xhr = new XMLHttpRequest();
	const url = searchQuery
		? `../../controllers/fetch_projects.php?search=${encodeURIComponent(searchQuery)}`
		: "../../controllers/fetch_projects.php";

	xhr.open("GET", url, true);

	xhr.onload = function () {
		if (xhr.status === 200) {
			const projects = JSON.parse(xhr.responseText);
			const container = document.getElementById("projectsContainer");

			if (projects.length === 0) {
				container.innerHTML = `
                    <div class="no-projects">
                        <i class="fas fa-folder-open"></i>
                        <p>No projects available at the moment</p>
                    </div>
                `;
				return;
			}

			let html = "";
			projects.forEach(function (project) {
				const coverImage =
					project.cover_image && project.cover_image !== "default_project.png"
						? `../../resources/projects/${project.cover_image}`
						: "../../resources/default_project.png";

				const isActive = project.status === "active";
				const cardClass = isActive ? "project-card" : "project-card closed";
				const onClick = isActive
					? `onclick="window.location.href='project_details.php?id=${project.project_id}'"`
					: "";

				const description =
					project.description.length > 80
						? project.description.substring(0, 80) + "..."
						: project.description;

				html += `
                    <div class="${cardClass}" ${onClick}>
                        <div class="card-image">
                            <img src="${coverImage}" alt="${project.title}">
                            <span class="status-tag ${project.status}">
                                ${project.status.charAt(0).toUpperCase() + project.status.slice(1)}
                            </span>
                        </div>
                        <div class="card-content">
                            <h3 class="project-title">${project.title}</h3>
                            <p class="project-description">${description}</p>
                            <div class="project-info">
                                <span class="info-item">
                                    <i class="fas fa-user"></i> ${project.owner_name}
                                </span>
                                <span class="info-item">
                                    <i class="fas fa-users"></i> ${project.max_members} members
                                </span>
                            </div>
                        </div>
                    </div>
                `;
			});

			container.innerHTML = html;
		} else {
			console.error("Error fetching projects:", xhr.status);
			document.getElementById("projectsContainer").innerHTML = `
                <div class="no-projects">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>Error loading projects</p>
                </div>
            `;
		}
	};

	xhr.send();
}

function searchProjects() {
	const searchInput = document.getElementById("searchInput");
	const clearBtn = document.getElementById("clearSearch");
	const query = searchInput.value.trim();

	// Show/hide clear button
	if (query.length > 0) {
		clearBtn.style.display = "flex";
	} else {
		clearBtn.style.display = "none";
	}

	// Search immediately
	loadProjects(query);
}

function clearSearch() {
	const searchInput = document.getElementById("searchInput");
	const clearBtn = document.getElementById("clearSearch");

	searchInput.value = "";
	clearBtn.style.display = "none";
	currentSearchQuery = "";
	loadProjects();
}

loadProjects();

setInterval(function () {
	loadProjects(currentSearchQuery);
}, 5000);
