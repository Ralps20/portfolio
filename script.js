// ----- Mobile nav toggle -----
const navToggle = document.getElementById('navToggle');
const navLinks = document.getElementById('navLinks');

if (navToggle && navLinks) {
  navToggle.addEventListener('click', () => {
    const isOpen = navLinks.classList.toggle('open');
    navToggle.setAttribute('aria-expanded', isOpen);
  });

  // Close mobile menu after tapping a link (but not the theme button)
  navLinks.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      navLinks.classList.remove('open');
      navToggle.setAttribute('aria-expanded', 'false');
    });
  });
}

// ----- Active nav link on scroll -----
const sections = document.querySelectorAll('section[id]');
const navItems = document.querySelectorAll('[data-nav]');

const navObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    const id = entry.target.getAttribute('id');
    const link = document.querySelector(`[data-nav][href="#${id}"]`);
    if (!link) return;
    if (entry.isIntersecting) {
      navItems.forEach(item => item.classList.remove('active'));
      link.classList.add('active');
    }
  });
}, { rootMargin: '-45% 0px -45% 0px' });

sections.forEach(section => navObserver.observe(section));

// ----- Scroll reveal animations -----
const revealEls = document.querySelectorAll('.reveal');

const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('in-view');
      revealObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.15 });

revealEls.forEach(el => revealObserver.observe(el));

// ----- Back to top button -----
const toTopBtn = document.getElementById('toTop');
if (toTopBtn) {
  toTopBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

// ----- Footer year -----
const yearEl = document.getElementById('year');
if (yearEl) yearEl.textContent = new Date().getFullYear();

// ----- Hero terminal typewriter (signature moment) -----
const terminalEl = document.getElementById('terminalLine');
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const terminalMessage = 'currently building: profit_radar.system';

if (terminalEl) {
  if (prefersReducedMotion) {
    terminalEl.textContent = terminalMessage;
  } else {
    let i = 0;
    const type = () => {
      if (i <= terminalMessage.length) {
        terminalEl.textContent = terminalMessage.slice(0, i);
        i++;
        setTimeout(type, 38);
      }
    };
    setTimeout(type, 500);
  }
}

// ----- Theme toggle (light / dark), remembered between visits -----
const THEME_KEY = 'cvmb-theme';
const themeToggle = document.getElementById('themeToggle');
const root = document.documentElement;

function applyTheme(theme) {
  if (theme === 'light') {
    root.setAttribute('data-theme', 'light');
  } else {
    root.removeAttribute('data-theme');
  }
  if (themeToggle) {
    const isLight = theme === 'light';
    themeToggle.textContent = isLight ? 'Dark' : 'Light';
    themeToggle.setAttribute('aria-pressed', String(isLight));
  }
}

let storedTheme = null;
try { storedTheme = localStorage.getItem(THEME_KEY); } catch (e) { /* private mode */ }
applyTheme(storedTheme === 'light' ? 'light' : 'dark');

if (themeToggle) {
  themeToggle.addEventListener('click', () => {
    const next = root.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
    applyTheme(next);
    try { localStorage.setItem(THEME_KEY, next); } catch (e) { /* private mode */ }
  });
}

// ----- Scroll progress bar -----
const progressBar = document.getElementById('progressBar');

function updateProgress() {
  if (!progressBar) return;
  const scrollTop = window.scrollY;
  const docHeight = document.documentElement.scrollHeight - window.innerHeight;
  const pct = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
  progressBar.style.width = pct + '%';
}

updateProgress();
window.addEventListener('scroll', updateProgress, { passive: true });
window.addEventListener('resize', updateProgress);

// ----- Project filter by stack tag -----
const filterBtns = document.querySelectorAll('.filter-btn');
const projectCards = document.querySelectorAll('.project[data-tags]');
const projectEmpty = document.getElementById('projectEmpty');

filterBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    const filter = btn.getAttribute('data-filter');

    filterBtns.forEach(b => b.classList.remove('is-active'));
    btn.classList.add('is-active');

    let visibleCount = 0;
    projectCards.forEach(card => {
      const tags = (card.getAttribute('data-tags') || '').split(' ');
      const show = filter === 'all' || tags.includes(filter);
      card.classList.toggle('is-hidden', !show);
      if (show) visibleCount++;
    });

    if (projectEmpty) projectEmpty.hidden = visibleCount !== 0;
  });
});

// ----- Copy email to clipboard -----
const copyEmailBtn = document.getElementById('copyEmailBtn');
const contactEmail = document.getElementById('contactEmail');

if (copyEmailBtn && contactEmail) {
  const defaultLabel = copyEmailBtn.textContent;
  copyEmailBtn.addEventListener('click', () => {
    const address = contactEmail.getAttribute('href').replace('mailto:', '');

    const showResult = (label, ms) => {
      copyEmailBtn.textContent = label;
      setTimeout(() => { copyEmailBtn.textContent = defaultLabel; }, ms);
    };

    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(address)
        .then(() => showResult('Copied', 2000))
        .catch(() => showResult('Copy failed', 2000));
    } else {
      showResult('Copy failed', 2000);
    }
  });
}