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

function showJoinModal() {
	document.getElementById("joinModal").style.display = "block";
}

function closeJoinModal() {
	document.getElementById("joinModal").style.display = "none";
	document.getElementById("joinMessage").value = "";
	document.getElementById("charCount").textContent = "0";
}

function updateCharCount() {
	const message = document.getElementById("joinMessage").value;
	const count = message.length;
	document.getElementById("charCount").textContent = count;
}

function sendJoinRequest(projectId) {
	const message = document.getElementById("joinMessage").value.trim();

	if (!message) {
		return;
	}

	if (message.length < 20) {
		return;
	}

	if (message.length > 500) {
		return;
	}

	const xhr = new XMLHttpRequest();
	xhr.open("POST", "../../controllers/joinProjectControl.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

	xhr.onload = function () {
		if (xhr.status === 200) {
			const response = JSON.parse(xhr.responseText);
			closeJoinModal();
			window.location.reload();
		} else {
			window.location.reload();
		}
	};

	xhr.onerror = function () {
		window.location.reload();
	};

	xhr.send(
		"project_id=" + projectId + "&message=" + encodeURIComponent(message),
	);
}

// close modal while user click outside the modal
window.onclick = function (event) {
	const modal = document.getElementById("joinModal");
	if (event.target == modal) {
		closeJoinModal();
	}
};

function removeMember(userId, memberName) {
	if (
		confirm(`Are you sure you want to remove ${memberName} from this project?`)
	) {
		const urlParams = new URLSearchParams(window.location.search);
		const projectId = urlParams.get("id");

		const xhr = new XMLHttpRequest();
		xhr.open("POST", "../../controllers/removeMemberControl.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

		xhr.onload = function () {
			if (xhr.status === 200) {
				const response = JSON.parse(xhr.responseText);
				if (response.success) {
					alert(response.message);
					const card = document.querySelector(`[data-member-id="${userId}"]`);
					if (card) {
						card.style.transition = "all 0.3s ease";
						card.style.opacity = "0";
						card.style.transform = "scale(0.8)";
						setTimeout(() => {
							window.location.reload();
						}, 300);
					}
				} else {
					alert("Error: " + response.message);
				}
			} else {
				alert("Error removing member");
			}
		};

		xhr.onerror = function () {
			alert("Request failed");
		};

		xhr.send(`project_id=${projectId}&user_id=${userId}`);
	}
}

function leaveProject(memberName) {
	if (confirm(`Are you sure you want to leave this project?`)) {
		const urlParams = new URLSearchParams(window.location.search);
		const projectId = urlParams.get("id");

		const xhr = new XMLHttpRequest();
		xhr.open("POST", "../../controllers/leaveProjectControl.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

		xhr.onload = function () {
			if (xhr.status === 200) {
				const response = JSON.parse(xhr.responseText);
				if (response.success) {
					alert(response.message);
					window.location.href = "home.php";
				} else {
					alert("Error: " + response.message);
				}
			} else {
				alert("Error leaving project");
			}
		};

		xhr.onerror = function () {
			alert("Request failed");
		};

		xhr.send(`project_id=${projectId}`);
	}
}
