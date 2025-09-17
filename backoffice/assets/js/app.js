// app.js

// --- Filtres page accueil ---
document.querySelectorAll('.filter-link').forEach(link => {
  link.addEventListener('click', e => {
    e.preventDefault();
    const cat = link.getAttribute('data-category');
    document.querySelectorAll('.card').forEach(card => {
      if (cat === 'all' || card.dataset.category === cat) {
        card.style.display = 'block';
      } else {
        card.style.display = 'none';
      }
    });
  });
});

// --- Lightbox page projet ---
const lightbox = document.createElement('div');
lightbox.className = 'lightbox';
lightbox.innerHTML = `
  <span class="close">&times;</span>
  <span class="prev">&#10094;</span>
  <div class="lightbox-content"></div>
  <span class="next">&#10095;</span>
`;
document.body.appendChild(lightbox);

const content = lightbox.querySelector('.lightbox-content');
const closeBtn = lightbox.querySelector('.close');
const prevBtn = lightbox.querySelector('.prev');
const nextBtn = lightbox.querySelector('.next');
let items = [];
let currentIndex = 0;

function openLightbox(index) {
  currentIndex = index;
  showItem();
  lightbox.style.display = 'flex';
}

function showItem() {
  const el = items[currentIndex];
  const type = el.dataset.type;
  const src = el.dataset.src;
  if (type === 'image') {
    content.innerHTML = `<img src="${src}" loading="lazy">`;
  } else if (type === 'video') {
    content.innerHTML = `<video src="${src}" controls autoplay></video>`;
  } else if (type === 'youtube') {
    content.innerHTML = `<iframe src="https://www.youtube.com/embed/${src}?autoplay=1" frameborder="0" allowfullscreen></iframe>`;
  }
}

function closeLightbox() {
  lightbox.style.display = 'none';
  content.innerHTML = '';
}

function nextItem() {
  currentIndex = (currentIndex + 1) % items.length;
  showItem();
}
function prevItem() {
  currentIndex = (currentIndex - 1 + items.length) % items.length;
  showItem();
}

closeBtn.onclick = closeLightbox;
nextBtn.onclick = nextItem;
prevBtn.onclick = prevItem;
window.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeLightbox();
  if (e.key === 'ArrowRight') nextItem();
  if (e.key === 'ArrowLeft') prevItem();
});

// attach triggers
document.querySelectorAll('.lightbox-trigger').forEach((el, i) => {
  items.push(el);
  el.addEventListener('click', e => {
    e.preventDefault();
    openLightbox(i);
  });
});
