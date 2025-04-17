// JAVASCRIPT CHECK
console.log('about.js loaded');

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
  grabCursor: true,
  centeredSlides: true,
  slidesPerView: "auto",
  spaceBetween: 30,
  loop: true,
  autoplay: {
    delay: 3000,
    disableOnInteraction: false,
  },
  speed: 1000,
});