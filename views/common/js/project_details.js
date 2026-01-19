function editProject(projectId) {
	window.location.href = "editProject.php?id=" + projectId;
}

function deleteProject(projectId, projectTitle) {
	if (
		confirm(
			'Are you sure you want to delete project "' +
				projectTitle +
				'"? This action cannot be undone.',
		)
	) {
		const xhr = new XMLHttpRequest();
		xhr.open("POST", "../../controllers/deleteProjectControl.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

		xhr.onload = function () {
			if (xhr.status === 200) {
				const response = JSON.parse(xhr.responseText);
				if (response.success) {
					alert("Project deleted successfully");
					window.location.href = "home.php";
				} else {
					alert("Error: " + response.message);
				}
			} else {
				alert("Error deleting project");
			}
		};

		xhr.onerror = function () {
			alert("Request failed");
		};

		xhr.send("project_id=" + projectId);
	}
}
