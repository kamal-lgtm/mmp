document.addEventListener('DOMContentLoaded', function() {

  // ===== GESTION DE LA POSITION DE SCROLL ET FILTRE =====
  
  // Restaurer la position ET le filtre lors du retour sur index.php
  if (window.location.pathname.includes('index.php') || window.location.pathname.endsWith('/')) {
    const savedPosition = sessionStorage.getItem('scrollPosition');
    const savedFilter = sessionStorage.getItem('activeFilter');
    
    // Restaurer le filtre d'abord
    if (savedFilter && savedFilter !== 'all') {
      setTimeout(() => {
        const filterLink = document.querySelector(`[data-filter="${savedFilter}"]`);
        if (filterLink) {
          filterLink.click();
        }
      }, 50);
    }
    
    // Puis restaurer la position
    if (savedPosition) {
      setTimeout(() => {
        window.scrollTo({
          top: parseInt(savedPosition),
          behavior: 'smooth'
        });
        sessionStorage.removeItem('scrollPosition');
      }, savedFilter ? 200 : 100);
    }
  }

  // Sauvegarder la position ET le filtre avant de naviguer vers un projet
  const projectLinks = document.querySelectorAll('a[href*="project.php"]');
  projectLinks.forEach(link => {
    link.addEventListener('click', () => {
      sessionStorage.setItem('scrollPosition', window.pageYOffset.toString());
      
      // Sauvegarder le filtre actuel
      const activeFilter = document.querySelector('.filters a.active');
      if (activeFilter) {
        sessionStorage.setItem('activeFilter', activeFilter.dataset.filter);
      }
    });
  });

  // ===== BOUTON RETOUR =====
  const btnRetour = document.querySelector('.btn-retour');
  if (btnRetour) {
    btnRetour.addEventListener('click', (e) => {
      e.preventDefault();
      sessionStorage.setItem('returningFromProject', 'true');
      
      if (document.referrer && document.referrer.includes('index.php')) {
        window.location.href = document.referrer;
      } else {
        window.location.href = 'index.php';
      }
    });
  }

  // ===== FILTRES =====
  const filters = document.querySelectorAll('.filters a');
  const cards = document.querySelectorAll('.card');
  
  filters.forEach(f => f.addEventListener('click', e => {
    e.preventDefault();
    
    // Mise à jour immédiate de l'interface
    filters.forEach(x => x.classList.remove('active'));
    f.classList.add('active');
    
    const cat = f.dataset.filter;
    
    // Sauvegarder le filtre sélectionné
    sessionStorage.setItem('activeFilter', cat);
    
    // Filtrage instantané
    cards.forEach(card => {
      const cardCategory = card.dataset.category;
      const shouldShow = cat === 'all' || cardCategory === cat;
      card.style.display = shouldShow ? 'block' : 'none';
    });
    
    // Scroll vers le haut
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }));

  // ===== LIGHTBOX =====
  const triggers = document.querySelectorAll('.lightbox-trigger');
  const lightbox = document.getElementById('lightbox');
  
  if (lightbox && triggers.length > 0) {
    const content = lightbox.querySelector('.lightbox-content');
    const close = lightbox.querySelector('.close');
    const prev = lightbox.querySelector('.prev');
    const next = lightbox.querySelector('.next');
    
    let currentIndex = 0;

    function openLightbox(index) {
      sessionStorage.setItem('lightboxScrollPosition', window.pageYOffset.toString());
      
      currentIndex = index;
      const trigger = triggers[currentIndex];
      const type = trigger.dataset.type;
      const src = trigger.dataset.src;
      
      // Vider le contenu
      content.innerHTML = '';
      
      // Créer l'élément avec indicateur de chargement
      if (type === 'youtube') {
        // Ajouter un indicateur de chargement
        const loading = document.createElement('div');
        loading.style.cssText = `
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          color: white;
          font-size: 1.2rem;
          z-index: 10;
        `;
        loading.textContent = 'Chargement de la vidéo...';
        content.appendChild(loading);
        
        const iframe = document.createElement('iframe');
        iframe.src = `https://www.youtube.com/embed/${src}?autoplay=1`;
        iframe.style.width = '80vw';
        iframe.style.height = '45vw';
        iframe.style.maxWidth = '90vw';
        iframe.style.maxHeight = '90vh';
        iframe.allowFullscreen = true;
        iframe.frameBorder = '0';
        
        // Supprimer le loading quand l'iframe est chargée
        iframe.onload = () => {
          if (loading.parentNode) {
            loading.remove();
          }
        };
        
        content.appendChild(iframe);
      } else if (type === 'image') {
        const img = document.createElement('img');
        img.src = src;
        img.style.maxWidth = '90vw';
        img.style.maxHeight = '90vh';
        content.appendChild(img);
      } else if (type === 'video') {
        const video = document.createElement('video');
        video.src = src;
        video.controls = true;
        video.autoplay = true;
        video.style.maxWidth = '90vw';
        video.style.maxHeight = '90vh';
        content.appendChild(video);
      }
      
      // Afficher la lightbox
      lightbox.style.display = 'flex';
      lightbox.style.opacity = '1';
      document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
      lightbox.style.opacity = '0';
      document.body.style.overflow = '';
      
      setTimeout(() => {
        lightbox.style.display = 'none';
        content.innerHTML = '';
        
        // Restaurer la position de scroll
        const savedPos = sessionStorage.getItem('lightboxScrollPosition');
        if (savedPos) {
          window.scrollTo(0, parseInt(savedPos));
          sessionStorage.removeItem('lightboxScrollPosition');
        }
      }, 300);
    }

    function showNext() {
      currentIndex = (currentIndex + 1) % triggers.length;
      openLightbox(currentIndex);
    }

    function showPrev() {
      currentIndex = (currentIndex - 1 + triggers.length) % triggers.length;
      openLightbox(currentIndex);
    }

    // Événements des triggers
    triggers.forEach((trigger, index) => {
      trigger.addEventListener('click', function(e) {
        e.preventDefault();
        openLightbox(index);
      });
    });
    
    // Contrôles de la lightbox
    if (close) {
      close.addEventListener('click', closeLightbox);
    }

    if (next) {
      next.addEventListener('click', function(e) {
        e.stopPropagation();
        showNext();
      });
    }

    if (prev) {
      prev.addEventListener('click', function(e) {
        e.stopPropagation();
        showPrev();
      });
    }
    
    // Navigation clavier
    document.addEventListener('keydown', function(e) {
      if (lightbox.style.display === 'flex') {
        if (e.key === 'Escape') {
          closeLightbox();
        } else if (e.key === 'ArrowRight') {
          showNext();
        } else if (e.key === 'ArrowLeft') {
          showPrev();
        }
      }
    });
    
    // Fermer au clic sur l'arrière-plan
    lightbox.addEventListener('click', function(e) {
      if (e.target === lightbox) {
        closeLightbox();
      }
    });

    // Swipe gestures pour mobile
    let touchStartX = 0;
    let touchStartY = 0;
    
    lightbox.addEventListener('touchstart', (e) => {
      touchStartX = e.touches[0].clientX;
      touchStartY = e.touches[0].clientY;
    }, { passive: true });
    
    lightbox.addEventListener('touchend', (e) => {
      const touchEndX = e.changedTouches[0].clientX;
      const touchEndY = e.changedTouches[0].clientY;
      
      const deltaX = touchEndX - touchStartX;
      const deltaY = touchEndY - touchStartY;
      const minSwipeDistance = 100;
      
      if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > minSwipeDistance) {
        if (deltaX > 0) {
          showPrev(); // Swipe droite = précédent
        } else {
          showNext(); // Swipe gauche = suivant
        }
      } else if (deltaY > minSwipeDistance) {
        closeLightbox(); // Swipe bas = fermer
      }
    }, { passive: true });
  }

  // ===== SCROLL TO TOP BUTTON =====
  const scrollToTopBtn = document.createElement('button');
  scrollToTopBtn.innerHTML = '↑';
  scrollToTopBtn.className = 'scroll-to-top';
  scrollToTopBtn.setAttribute('aria-label', 'Retour en haut');
  
  document.body.appendChild(scrollToTopBtn);
  
  function toggleScrollButton() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    if (scrollTop > 300) {
      scrollToTopBtn.style.opacity = '1';
      scrollToTopBtn.style.transform = 'scale(1)';
      scrollToTopBtn.style.pointerEvents = 'auto';
    } else {
      scrollToTopBtn.style.opacity = '0';
      scrollToTopBtn.style.transform = 'scale(0)';
      scrollToTopBtn.style.pointerEvents = 'none';
    }
  }
  
  toggleScrollButton();
  window.addEventListener('scroll', toggleScrollButton);
  
  scrollToTopBtn.addEventListener('click', function() {
    window.scrollTo({ 
      top: 0, 
      behavior: 'smooth' 
    });
    
    // Effet de feedback
    this.style.transform = 'scale(0.9)';
    setTimeout(() => {
      const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
      this.style.transform = currentScroll > 300 ? 'scale(1)' : 'scale(0)';
    }, 150);
  });

  // ===== GESTION DES ERREURS D'IMAGES =====
  document.querySelectorAll('img').forEach(img => {
    img.addEventListener('error', function() {
      if (!this.hasAttribute('data-error-handled')) {
        this.setAttribute('data-error-handled', 'true');
        this.src = 'assets/img/placeholder.svg';
        this.alt = 'Image non disponible';
      }
    });
  });

  console.log('Portfolio loaded successfully!');
});