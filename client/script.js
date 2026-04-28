/* ============================================================
   WildSpace — Shared JavaScript
   Page transitions, staggered reveals, micro-interactions
   ============================================================ */

// ── Page Transition ──────────────────────────────────────────
const overlay = document.getElementById('page-transition');

function navigateTo(url) {
  overlay.classList.add('active');
  setTimeout(() => { window.location.href = url; }, 360);
}

// Intercept all in-app anchor clicks
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('a[data-nav]').forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      navigateTo(link.href);
    });
  });

  // Fade in from overlay on page load
  overlay.classList.remove('active');

  // ── Staggered field reveals ────────────────────────────────
  const fields = document.querySelectorAll('.field, .field-check');
  fields.forEach((el, i) => {
    setTimeout(() => el.classList.add('revealed'), 350 + i * 90);
  });

  // ── Intersection Observer for landing page sections ────────
  const io = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        // stagger feature cards
        if (entry.target.classList.contains('features-grid')) {
          entry.target.querySelectorAll('.feature-card').forEach((card, i) => {
            setTimeout(() => card.classList.add('visible'), i * 120);
          });
        }
      }
    });
  }, { threshold: 0.15 });

  document.querySelectorAll('.section-label, .section-title, .features-grid').forEach(el => io.observe(el));

  // ── Button press physics ───────────────────────────────────
  document.querySelectorAll('.btn-primary, .hero-cta, .nav-btn').forEach(btn => {
    btn.addEventListener('mousedown', () => {
      btn.style.transform = 'scale(0.97)';
    });
    ['mouseup', 'mouseleave'].forEach(ev => {
      btn.addEventListener(ev, () => {
        btn.style.transform = '';
      });
    });
  });

  // ── Auth form validation (light) ──────────────────────────
  const form = document.querySelector('.auth-form');
  if (form) {
    form.addEventListener('submit', e => {
      e.preventDefault();
      const btn = form.querySelector('.btn-primary');
      btn.textContent = '...';
      btn.style.opacity = '0.8';

      // simulate network delay then navigate
      setTimeout(() => {
        const dest = form.dataset.dest;
        if (dest) navigateTo(dest);
      }, 800);
    });
  }
});