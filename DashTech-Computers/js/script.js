(() => {
  'use strict';
  const onReady = (callback) => document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', callback) : callback();

  onReady(() => {
    const typing = document.getElementById('typing');
    if (typing) {
      const words = ['That Grow Businesses.', 'That Increase Sales.', 'That Attract Customers.', 'Delivered In 5 Working Days.'];
      let word = 0, character = 0, deleting = false;
      const type = () => {
        const current = words[word];
        typing.textContent = current.substring(0, character);
        character += deleting ? -1 : 1;
        if (!deleting && character > current.length) { deleting = true; setTimeout(type, 1400); return; }
        if (deleting && character < 0) { deleting = false; character = 0; word = (word + 1) % words.length; }
        setTimeout(type, deleting ? 45 : 90);
      };
      type();
    }

    document.querySelectorAll('a[href^="#"]').forEach((anchor) => anchor.addEventListener('click', (event) => {
      const target = document.querySelector(anchor.getAttribute('href'));
      if (target) { event.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
    }));

    document.querySelectorAll('.faq-question').forEach((question) => question.addEventListener('click', () => {
      const answer = question.nextElementSibling;
      const icon = question.querySelector('span');
      const open = answer && answer.style.display === 'block';
      if (answer) answer.style.display = open ? 'none' : 'block';
      if (icon) icon.textContent = open ? '+' : '-';
    }));

    const loader = document.getElementById('loader');
    if (loader) window.addEventListener('load', () => { loader.style.display = 'none'; });
    const topButton = document.getElementById('topBtn');
    if (topButton) {
      window.addEventListener('scroll', () => { topButton.style.display = window.scrollY > 300 ? 'block' : 'none'; });
      topButton.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    }
    const darkMode = document.getElementById('darkMode');
    if (darkMode) darkMode.addEventListener('click', () => document.body.classList.toggle('dark'));

    const menu = document.getElementById('mobile-menu');
    const navbar = document.getElementById('navbar');
    if (menu && navbar) {
      const setMenuState = (open) => {
        navbar.classList.toggle('active', open);
        document.body.classList.toggle('menu-open', open);
        menu.setAttribute('aria-expanded', String(open));
        menu.setAttribute('aria-label', open ? 'Close navigation menu' : 'Open navigation menu');
        const icon = menu.querySelector('i');
        if (icon) icon.className = `fa-solid ${open ? 'fa-xmark' : 'fa-bars'}`;
      };
      menu.addEventListener('click', () => setMenuState(!navbar.classList.contains('active')));
      navbar.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setMenuState(false)));
      document.addEventListener('keydown', (event) => { if (event.key === 'Escape') setMenuState(false); });
      document.addEventListener('click', (event) => {
        if (navbar.classList.contains('active') && !navbar.contains(event.target) && !menu.contains(event.target)) setMenuState(false);
      });
      window.addEventListener('resize', () => { if (window.innerWidth > 992) setMenuState(false); });
    }

    const cookieBanner = document.getElementById('cookie-banner');
    const acceptCookies = document.getElementById('acceptCookies');
    if (cookieBanner && !localStorage.getItem('cookiesAccepted')) cookieBanner.style.display = 'flex';
    if (acceptCookies) acceptCookies.addEventListener('click', () => {
      localStorage.setItem('cookiesAccepted', 'yes');
      if (cookieBanner) cookieBanner.style.display = 'none';
    });
  });
})();
