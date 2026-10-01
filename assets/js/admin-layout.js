(() => {
  const root = document.documentElement;
  const sidebar = document.getElementById('sidebar');
  const toggle = document.getElementById('openSidebar');
  const overlay = document.getElementById('sidebarOverlay');
  const mobile = matchMedia('(max-width: 1023px)');
  const setMenu = open => {
    sidebar.classList.toggle('mobile-open', open);
    sidebar.inert = mobile.matches && !open;
    overlay.hidden = !open;
    toggle.setAttribute('aria-expanded', String(open));
    document.body.classList.toggle('menu-open', open);
    if (open) document.getElementById('closeSidebar').focus();
  };
  toggle.addEventListener('click', () => {
    if (mobile.matches) setMenu(!sidebar.classList.contains('mobile-open'));
    else {
      const collapsed = sidebar.classList.toggle('collapsed');
      document.getElementById('mainWrapper').classList.toggle('sidebar-collapsed', collapsed);
      toggle.setAttribute('aria-expanded', String(!collapsed));
    }
  });
  [overlay, document.getElementById('closeSidebar')].forEach(el => el.addEventListener('click', () => { setMenu(false); toggle.focus(); }));
  const syncMenu = () => {
    setMenu(false);
    toggle.setAttribute('aria-expanded', String(!mobile.matches && !sidebar.classList.contains('collapsed')));
  };
  mobile.addEventListener('change', syncMenu); syncMenu();
  document.querySelectorAll('[id$="DropdownBtn"]').forEach(button => {
    button.addEventListener('click', () => {
      if (sidebar.classList.contains('collapsed')) {
        sidebar.classList.remove('collapsed');
        document.getElementById('mainWrapper').classList.remove('sidebar-collapsed');
        toggle.setAttribute('aria-expanded', 'true');
      }
      const submenu = document.getElementById(button.getAttribute('aria-controls'));
      const open = submenu.classList.toggle('is-open');
      button.setAttribute('aria-expanded', String(open)); submenu.inert = !open;
    });
  });
  document.getElementById('themeToggle').addEventListener('click', () => {
    const theme = root.dataset.theme === 'dark' ? 'light' : 'dark';
    root.dataset.theme = theme; root.classList.toggle('dark', theme === 'dark');
    try { localStorage.setItem('admin-theme', theme); } catch (_) {}
    document.dispatchEvent(new CustomEvent('admin:theme'));
  });
  const panels = [['notificationsToggle','notificationsPanel'],['userMenuToggle','userPanel']];
  const closePanels = () => panels.forEach(([button,panel]) => { document.getElementById(panel).hidden = true; document.getElementById(button).setAttribute('aria-expanded','false'); });
  panels.forEach(([buttonId,panelId]) => {
    const button = document.getElementById(buttonId), panel = document.getElementById(panelId);
    button.addEventListener('click', () => { const open = panel.hidden; closePanels(); panel.hidden = !open; button.setAttribute('aria-expanded',String(open)); });
  });
  document.addEventListener('click', event => { if (!event.target.closest('.popover-anchor')) closePanels(); });
  const search = document.getElementById('adminSearch'), results = document.getElementById('searchResults');
  const links = [...document.querySelectorAll('.sidebar-nav a')].map(a => ({name:a.textContent.trim(),url:a.getAttribute('href')}));
  const normalize = value => value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'');
  const closeSearch = () => { results.hidden = true; search.setAttribute('aria-expanded','false'); };
  search.addEventListener('input', () => {
    results.replaceChildren(); const query = normalize(search.value.trim());
    if (!query) { closeSearch(); return; }
    const found = links.filter(link => normalize(link.name).includes(query)).slice(0,6);
    found.forEach(link => { const a = document.createElement('a'); a.href = link.url; a.textContent = link.name; results.append(a); });
    if (!found.length) { const p = document.createElement('p'); p.textContent = 'Nenhuma tela encontrada.'; results.append(p); }
    results.hidden = false; search.setAttribute('aria-expanded','true');
  });
  search.addEventListener('keydown', event => { if (event.key === 'ArrowDown') results.querySelector('a')?.focus(); if (event.key === 'Enter') results.querySelector('a')?.click(); });
  document.addEventListener('click', e => { if (!e.target.closest('.quick-search')) closeSearch(); });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape') { closePanels(); closeSearch(); if (sidebar.classList.contains('mobile-open')) { setMenu(false); toggle.focus(); } }
    if (event.key === 'Tab' && mobile.matches && sidebar.classList.contains('mobile-open')) {
      const items = [...sidebar.querySelectorAll('a,button')].filter(el => el.getClientRects().length && !el.closest('[inert]'));
      const first = items[0], last = items[items.length-1];
      if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
      else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    }
  });
})();
