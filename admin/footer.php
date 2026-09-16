        </main>
        <footer class="py-6 px-8 border-t border-white/10 text-center text-xs text-slate-400">
            © 2026 Golden Jardim • Painel Administrativo de Alta Precisão.
        </footer>
    </div>

    <script>
        const html = document.documentElement;
        const themeToggle = document.querySelectorAll('#themeToggle');
        const sunIcon = document.querySelectorAll('#sunIcon');
        const moonIcon = document.querySelectorAll('#moonIcon');

        function updateThemeUI(isDark) {
            sunIcon.forEach(el => el.classList.toggle('hidden', isDark));
            moonIcon.forEach(el => el.classList.toggle('hidden', !isDark));
        }

        themeToggle.forEach(btn => {
            btn.addEventListener('click', () => {
                if (html.classList.contains('dark')) {
                    html.classList.remove('dark');
                    updateThemeUI(false);
                    localStorage.setItem('admin-theme', 'light');
                } else {
                    html.classList.add('dark');
                    updateThemeUI(true);
                    localStorage.setItem('admin-theme', 'dark');
                }
            });
        });

        if (localStorage.getItem('admin-theme') === 'light') {
            html.classList.remove('dark');
            updateThemeUI(false);
        } else {
            html.classList.add('dark');
            updateThemeUI(true);
        }

        const sidebar = document.getElementById('sidebar');
        const openSidebar = document.getElementById('openSidebar');
        const closeSidebar = document.getElementById('closeSidebar');

        if(openSidebar && sidebar) {
            openSidebar.addEventListener('click', () => sidebar.classList.remove('-translate-x-full'));
        }
        if(closeSidebar && sidebar) {
            closeSidebar.addEventListener('click', () => sidebar.classList.add('-translate-x-full'));
        }
    </script>
</body>
</html>
