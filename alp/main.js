// Carousel
const wrap = document.getElementsByClassName('img-wrap')[0];
if (wrap) {
  const images = wrap.getElementsByTagName('img');

  function startCarousel() {
    setInterval(() => {
      wrap.style.transition = "transform 0.5s ease-in-out";
      wrap.style.transform = "translateX(-100%)";

      setTimeout(() => {
        wrap.style.transition = "none";
        wrap.appendChild(wrap.children[0]);
        wrap.style.transform = "translateX(0)";
      }, 500);
    }, 3000);
  }

  document.addEventListener("DOMContentLoaded", startCarousel);
}

document.getElementById('hamburger')?.addEventListener('click', function() {
  const menu = document.getElementById('mobileMenu');
  if (menu) menu.classList.toggle('hidden');
});

document.getElementById('sidebarToggle')?.addEventListener('click', function() {
  const sidebar = document.getElementById('adminSidebar');
  if (sidebar) sidebar.classList.toggle('hidden');
  const overlay = document.getElementById('sidebarOverlay');
  if (overlay) overlay.classList.toggle('hidden');
});
document.getElementById('sidebarOverlay')?.addEventListener('click', function() {
  const sidebar = document.getElementById('adminSidebar');
  if (sidebar) sidebar.classList.toggle('hidden');
  this.classList.toggle('hidden');
});

(function() {
  const toggle = document.getElementById('darkToggle');
  const html = document.documentElement;

  if (localStorage.getItem('dark') === '1') {
    html.classList.add('dark');
  }

  if (toggle) {
    const icon = toggle.querySelector('.material-symbols-outlined');
    if (icon) icon.textContent = html.classList.contains('dark') ? 'light_mode' : 'dark_mode';

    toggle.addEventListener('click', function() {
      html.classList.toggle('dark');
      localStorage.setItem('dark', html.classList.contains('dark') ? '1' : '0');
      if (icon) icon.textContent = html.classList.contains('dark') ? 'light_mode' : 'dark_mode';
    });
  }
})();

// Back to Top
(function() {
  const btn = document.getElementById('backToTop');
  if (!btn) return;

  window.addEventListener('scroll', function() {
    btn.classList.toggle('show', window.scrollY > 300);
  }, { passive: true });

  btn.addEventListener('click', function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
})();
