/* ============================================================
   Caffè Aroma — Animations & UI Interactions
   - Loader/splash screen (once per session)
   - Navbar: transparent → solid on scroll
   - Hero background Ken Burns
   - Intersection Observer scroll animations
   ============================================================ */

(function () {
  'use strict';

  /* ----------------------------------------------------------
     LOADER
  ---------------------------------------------------------- */
  const loader = document.getElementById('loader');
  if (loader) {
    const shown = sessionStorage.getItem('loaderShown');
    if (shown) {
      loader.classList.add('hidden');
    } else {
      window.addEventListener('load', () => {
        setTimeout(() => {
          loader.classList.add('hidden');
          sessionStorage.setItem('loaderShown', '1');
        }, 2200);
      });
    }
  }

  /* ----------------------------------------------------------
     NAVBAR SCROLL TRANSITION
  ---------------------------------------------------------- */
  const header = document.querySelector('header');
  if (header) {
    const onScroll = () => {
      header.classList.toggle('scrolled', window.scrollY > 60);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ----------------------------------------------------------
     HERO BG KEN BURNS
  ---------------------------------------------------------- */
  const heroBg = document.querySelector('.hero-bg');
  if (heroBg) {
    window.addEventListener('load', () => heroBg.classList.add('loaded'));
  }

  /* ----------------------------------------------------------
     INTERSECTION OBSERVER — fade-up
  ---------------------------------------------------------- */
  const fadeEls = document.querySelectorAll('.fade-up');
  if (fadeEls.length) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );
    fadeEls.forEach((el) => observer.observe(el));
  }

  /* ----------------------------------------------------------
     MENU SEARCH (debounced live filter)
     Used in pages/menu.php
  ---------------------------------------------------------- */
  const searchInput = document.getElementById('menu-search');
  if (searchInput) {
    let timer;
    searchInput.addEventListener('input', () => {
      clearTimeout(timer);
      timer = setTimeout(() => {
        const q = searchInput.value.trim().toLowerCase();
        document.querySelectorAll('.product-card').forEach((card) => {
          const name = card.dataset.name?.toLowerCase() ?? '';
          const cat  = card.dataset.category?.toLowerCase() ?? '';
          card.style.display = (!q || name.includes(q) || cat.includes(q))
            ? '' : 'none';
        });
      }, 200);
    });
  }

  /* ----------------------------------------------------------
     TOAST NOTIFICATIONS
  ---------------------------------------------------------- */
  window.showToast = function (message, type = 'success', duration = 4500) {
    let container = document.getElementById('toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toast-container';
      document.body.appendChild(container);
    }

    const icons = { success: '✓', error: '✕', info: 'ℹ' };
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML =
      `<span class="toast-icon">${icons[type] ?? '•'}</span><span>${message}</span>`;
    container.appendChild(toast);

    // Trigger animation on next frame
    requestAnimationFrame(() => requestAnimationFrame(() => toast.classList.add('show')));

    setTimeout(() => {
      toast.classList.remove('show');
      toast.style.opacity = '0';
      setTimeout(() => toast.remove(), 350);
    }, duration);
  };

  /* ----------------------------------------------------------
     ACTIVE NAV LINK (highlight current page)
  ---------------------------------------------------------- */
  const currentPath = window.location.pathname.split('/').pop();
  document.querySelectorAll('nav a').forEach((link) => {
    const href = link.getAttribute('href')?.split('/').pop();
    if (href === currentPath) link.classList.add('active');
  });

})();
