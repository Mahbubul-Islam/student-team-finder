document.getElementById("cover_image").addEventListener("change", function (e) {
	const file = e.target.files[0];
	if (file) {
		if (file.size > 5 * 1024 * 1024) {
			alert("File size must be less than 5MB");
			this.value = "";
			return;
		}

		const allowedTypes = ["image/png", "image/jpg", "image/jpeg"];
		if (!allowedTypes.includes(file.type)) {
			alert("Only PNG and JPG images are allowed");
			this.value = "";
			return;
		}

		const reader = new FileReader();
		reader.onload = function (event) {
			document.getElementById("coverPreview").src = event.target.result;
		};
		reader.onerror = function () {
			alert("Cover photo uploaded (preview not available)");
		};
		reader.readAsDataURL(file);
	}
});
