const perfilImg = document.querySelector(".perfil-img");
const dropdown = document.querySelector(".perfil-dropdown");

perfilImg.addEventListener("click", () => {
  dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
});

// Cierra si haces click fuera
window.addEventListener("click", (e) => {
  if (!e.target.closest(".perfil")) {
    dropdown.style.display = "none";
  }
});
