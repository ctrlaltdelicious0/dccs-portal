// JAVASCRIPT CHECK
console.log('kindergarten_program.js loaded');

// ====================

// Initialize Lenis
const lenis = new Lenis();

// Use requestAnimationFrame to continuously update the scroll
function raf(time) {
  lenis.raf(time);
  requestAnimationFrame(raf);
}

requestAnimationFrame(raf);

// ====================

// NAVBAR TOGGLE
document.getElementById('menu_open_button').addEventListener('click', function () {
  document.querySelector('.menu_container').style.display = 'block';
  document.querySelector('header').style.display = 'none'; // Hide the header
});

document.getElementById('menu_close_button').addEventListener('click', function () {
  document.querySelector('.menu_container').style.display = 'none';
  document.querySelector('header').style.display = 'flex'; // Show the header
});

// ====================

// SWIPER CAROUSEL
var swiper = new Swiper(".mySwiper", {
  slidesPerView: "auto",
  loop: true,
  autoplay: {
    delay: 3000,
    disableOnInteraction: false,
  },
  speed: 1000,
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
});