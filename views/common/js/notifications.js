function markAsRead(notificationId) {
	const xhr = new XMLHttpRequest();
	xhr.open("POST", "../../controllers/markNotificationControl.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

	xhr.onload = function () {
		if (xhr.status === 200) {
			const response = JSON.parse(xhr.responseText);
			if (response.success) {
				
				const notificationItem = document.querySelector(
					`[data-notification-id="${notificationId}"]`,
				);
				if (notificationItem) {
					notificationItem.classList.remove("unread");
					notificationItem.classList.add("read");

					
					const markReadBtn = notificationItem.querySelector(".btn-mark-read");
					if (markReadBtn) {
						markReadBtn.remove();
					}
				}

				
				updateNotificationBadge();
			}
		}
	};

	xhr.onerror = function () {
		// Silent fail
	};

	xhr.send(`notification_id=${notificationId}`);
}

function markAllAsRead() {
	const xhr = new XMLHttpRequest();
	xhr.open("POST", "../../controllers/markAllNotificationsControl.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

	xhr.onload = function () {
		if (xhr.status === 200) {
			const response = JSON.parse(xhr.responseText);
			if (response.success) {
				window.location.reload();
			} else {
				window.location.reload();
			}
		} 
		else {
			window.location.reload();
		}
	};

	xhr.onerror = function () {
		window.location.reload();
	};

	xhr.send();
}

function deleteNotification(notificationId) {
	const xhr = new XMLHttpRequest();
	xhr.open("POST", "../../controllers/deleteNotificationControl.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

	xhr.onload = function () {
		if (xhr.status === 200) {
			const response = JSON.parse(xhr.responseText);
			if (response.success) {
				// Animate and remove notification
				const notificationItem = document.querySelector(
					`[data-notification-id="${notificationId}"]`,
				);
				if (notificationItem) {
					notificationItem.style.transition = "all 0.3s ease";
					notificationItem.style.opacity = "0";
					notificationItem.style.transform = "translateX(-20px)";

					setTimeout(() => {
						notificationItem.remove();

						// Check if no notifications left
						const notificationsList = document.querySelector(
							".notifications-list",
						);
						if (notificationsList && notificationsList.children.length === 0) {
							window.location.reload();
						}
					}, 300);
				}

				// Update notification badge
				updateNotificationBadge();
			}
		}
	};

	xhr.onerror = function () {
		// Silent fail
	};

	xhr.send(`notification_id=${notificationId}`);
}

function updateNotificationBadge() {
	const xhr = new XMLHttpRequest();
	xhr.open("GET", "../../controllers/getNotificationCountControl.php", true);

	xhr.onload = function () {
		if (xhr.status === 200) {
			const response = JSON.parse(xhr.responseText);
			if (response.success) {
				const badge = document.querySelector(".notification-badge");
				if (response.count > 0) {
					if (badge) {
						badge.textContent = response.count;
					}
				} else if (badge) {
					badge.remove();
				}
			}
		}
	};

	xhr.send();
}
