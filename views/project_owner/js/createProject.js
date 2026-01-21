const descriptionField = document.getElementById("description");
const charCount = document.getElementById("descCharCount");

if (descriptionField && charCount) {
	descriptionField.addEventListener("input", function () {
		charCount.textContent = this.value.length;
	});
}


const imageInput = document.getElementById("cover_image");
const imagePreview = document.getElementById("imagePreview");
const previewImg = document.getElementById("previewImg");

if (imageInput) {
	imageInput.addEventListener("change", function (e) {
		const file = e.target.files[0];

		if (file) {
			if (file.size > 5 * 1024 * 1024) {
				alert("File size must be less than 5MB");
				this.value = "";
				return;
			}

			
			if (!file.type.match("image.*")) {
				alert("Please select an image file");
				this.value = "";
				return;
			}

			
			const reader = new FileReader();
			reader.onload = function (e) {
				previewImg.src = e.target.result;
				imagePreview.style.display = "block";
			};
			reader.readAsDataURL(file);
		}
	});
}



