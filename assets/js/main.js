(() => {
  const toggle = document.querySelector('.site-header__toggle');
  const nav = document.querySelector('.site-header__nav');
  if (toggle && nav) {
    const closeMenu = () => {
      toggle.setAttribute('aria-expanded', 'false');
      nav.classList.remove('is-open');
    };
    toggle.addEventListener('click', () => {
      const next = toggle.getAttribute('aria-expanded') !== 'true';
      toggle.setAttribute('aria-expanded', String(next));
      nav.classList.toggle('is-open', next);
    });
    nav.addEventListener('click', (event) => {
      if (event.target.closest('a')) closeMenu();
    });
    document.addEventListener('click', (event) => {
      if (!toggle.contains(event.target) && !nav.contains(event.target)) closeMenu();
    });
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
        closeMenu();
        toggle.focus();
      }
    });
  }

  const filters = document.querySelectorAll('[data-bng-filter]');
  const rows = document.querySelectorAll('[data-bng-segment]');
  const count = document.querySelector('[data-bng-count]');
  if (filters.length && rows.length) {
    filters.forEach((button) => {
      button.addEventListener('click', () => {
        const segment = button.dataset.bngFilter;
        let visible = 0;
        filters.forEach((filter) => {
          const active = filter === button;
          filter.classList.toggle('is-active', active);
          filter.setAttribute('aria-pressed', String(active));
        });
        rows.forEach((row) => {
          const show = segment === 'all' || row.dataset.bngSegment === segment;
          row.hidden = !show;
          if (show) visible++;
        });
        if (count) count.textContent = visible + ' / ' + rows.length;
      });
    });
  }
})();
