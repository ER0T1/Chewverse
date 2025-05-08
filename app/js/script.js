function previewImage(event) {
  const file = event.target.files[0];
  if (file && file.type.startsWith("image/")) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const preview = document.getElementById("avatar-preview");
      preview.innerHTML = `<img src="${e.target.result}" alt="Avatar" style="max-height:100%; max-width:100%; border-radius: 1rem;">`;
    };
    reader.readAsDataURL(file);
  }
}