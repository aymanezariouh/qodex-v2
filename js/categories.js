const menuToggle = document.getElementById("menuToggle");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");
const mainContent = document.getElementById("mainContent");

menuToggle.addEventListener("click", function () {
  sidebar.classList.toggle("active");
  overlay.classList.toggle("active");

  if (window.innerWidth > 768) {
    sidebar.classList.toggle("hidden");
    mainContent.classList.toggle("expanded");
  }
});

overlay.addEventListener("click", function () {
  sidebar.classList.remove("active");
  overlay.classList.remove("active");
});
const navLinks = document.querySelectorAll(".nav-links a");
navLinks.forEach((link) => {
  link.addEventListener("click", function () {
    if (window.innerWidth <= 768) {
      sidebar.classList.remove("active");
      overlay.classList.remove("active");
    }
  });
});
