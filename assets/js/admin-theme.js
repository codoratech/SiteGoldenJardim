(() => {
  let theme = 'dark';
  try { theme = localStorage.getItem('admin-theme') === 'light' ? 'light' : 'dark'; } catch (_) {}
  document.documentElement.dataset.theme = theme;
  document.documentElement.classList.toggle('dark', theme === 'dark');
})();
