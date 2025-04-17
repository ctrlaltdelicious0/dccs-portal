// JAVASCRIPT CHECK
console.log('index.js loaded');

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

// CARD FACILITIES
const $cardsWrapper = document.querySelector('#cards');
const $cards = document.querySelectorAll('.card__content');

// Pass the number of cards to the CSS because it needs it to add some extra padding.
// Without this extra padding, the last card won’t move with the group but slide over it.
const numCards = $cards.length;
$cardsWrapper.style.setProperty('--numcards', numCards);

// Each card should only shrink when it’s at the top.
// We can’t use exit on the els for this (as they are sticky)
// but can track $cardsWrapper instead.
const viewTimeline = new ViewTimeline({
  subject: $cardsWrapper,
  axis: 'block',
});

$cards.forEach(($card, index0) => {
  const index = index0 + 1;
  const reverseIndex = numCards - index0;
  const reverseIndex0 = numCards - index;

  // Scroll-Linked Animation
  $card.animate(
    {
      // Earlier cards shrink more than later cards
      transform: [`scale(1)`, `scale(${1 - (0.1 * reverseIndex0)}`],
    },
    {
      timeline: viewTimeline,
      fill: 'forwards',
      rangeStart: `exit-crossing ${CSS.percent(index0 / numCards * 100)}`,
      rangeEnd: `exit-crossing ${CSS.percent(index / numCards * 100)}`,
    }
  );
});

// ====================

// PROGRAMS REDIRECT
// REDIRECT TO KINDERGARTEN PROGRAM PAGE
document.addEventListener('DOMContentLoaded', function() {
  const kindergartenLink = document.querySelector('.acad_programs_1');
  if (kindergartenLink) {
    kindergartenLink.addEventListener('click', function() {
      window.location.href = 'kindergarten_program.html';
    });
  }
});