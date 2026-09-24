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
        const mainWrapper = document.getElementById('mainWrapper');
        const openSidebar = document.getElementById('openSidebar');
        const closeSidebar = document.getElementById('closeSidebar');

        if(openSidebar && sidebar) {
            openSidebar.addEventListener('click', () => {
                if (window.innerWidth >= 1024) {
                    sidebar.classList.toggle('-translate-x-full');
                    if (mainWrapper) {
                        mainWrapper.classList.toggle('lg:pl-64');
                        mainWrapper.classList.toggle('lg:pl-0');
                    }
                } else {
                    sidebar.classList.remove('-translate-x-full');
                }
            });
        }
        if(closeSidebar && sidebar) {
            closeSidebar.addEventListener('click', () => sidebar.classList.add('-translate-x-full'));
        }

        // Phone formatting & validation (max 11 digits, numbers only, mask (XX) XXXXX-XXXX)
        const phoneInputs = document.querySelectorAll('input[name*="Telefone"], input[name*="telefone"]');
        phoneInputs.forEach(input => {
            input.addEventListener('input', function() {
                let v = this.value.replace(/\D/g, '').substring(0, 11);
                if (v.length > 10) {
                    this.value = v.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
                } else if (v.length > 6) {
                    this.value = v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
                } else if (v.length > 2) {
                    this.value = v.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
                } else if (v.length > 0) {
                    this.value = v.replace(/^(\d*)/, '($1');
                } else {
                    this.value = '';
                }
            });
        });

        // Currency formatting on blur (R$)
        const currencyInputs = document.querySelectorAll('input[name*="Preco"], input[name*="preço"], input[name*="Valor"], input[name*="valor"]');
        currencyInputs.forEach(input => {
            if (input.value && !input.value.includes('R$')) {
                let num = parseFloat(input.value.replace(',', '.'));
                if (!isNaN(num)) {
                    num = Math.max(0, num);
                    input.value = 'R$ ' + num.toFixed(2).replace('.', ',');
                }
            }

            input.addEventListener('input', function() {
                let val = this.value.replace(/-/g, '');
                if (val !== this.value) {
                    this.value = val;
                }
            });

            input.addEventListener('focus', function() {
                let val = this.value.replace('R$', '').trim();
                this.value = val.replace(/-/g, '');
            });

            input.addEventListener('blur', function() {
                let clean = this.value.replace('R$', '').replace(/\s/g, '').replace(',', '.').replace(/-/g, '');
                let num = parseFloat(clean);
                if (!isNaN(num)) {
                    num = Math.max(0, num);
                    this.value = 'R$ ' + num.toFixed(2).replace('.', ',');
                } else if (this.value.trim() === '') {
                    this.value = '';
                }
            });
        });

        // Strip R$ on form submit
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', () => {
                currencyInputs.forEach(input => {
                    let clean = input.value.replace('R$', '').replace(/\s/g, '').replace(',', '.').replace(/-/g, '');
                    let num = parseFloat(clean);
                    input.value = !isNaN(num) ? Math.max(0, num).toFixed(2) : '0.00';
                });
            });
        });

        // Custom Delete Confirmation Modal
        const deleteModalHTML = `
        <div id="deleteModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center hidden">
            <div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-10 h-10 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center font-bold text-lg">⚠️</span>
                    <div>
                        <h3 class="font-display font-bold text-lg text-white">Confirmação de Exclusão</h3>
                        <p class="text-xs text-slate-400" id="deleteModalText">Tem certeza que deseja excluir este registro?</p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 mt-6">
                    <button type="button" id="cancelDeleteBtn" class="bg-white/10 text-slate-300 font-bold py-2 px-4 rounded-xl hover:bg-white/20 transition-all text-xs uppercase tracking-wider">Cancelar</button>
                    <a id="confirmDeleteBtn" href="#" class="bg-red-500 text-white font-bold py-2 px-4 rounded-xl hover:bg-red-600 transition-all text-xs uppercase tracking-wider">Sim, Excluir</a>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', deleteModalHTML);

        const deleteModal = document.getElementById('deleteModal');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
        const deleteModalText = document.getElementById('deleteModalText');

        document.querySelectorAll('a[href*="acao=excluir"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                let customMsg = this.getAttribute('data-confirm-msg');
                if (!customMsg && this.onclick) {
                    let onclickStr = this.getAttribute('onclick');
                    let match = onclickStr && onclickStr.match(/confirm\('([^']+)'\)/);
                    if (match) customMsg = match[1];
                }
                deleteModalText.textContent = customMsg || 'Tem certeza que deseja excluir este registro?';
                confirmDeleteBtn.href = this.href;
                deleteModal.classList.remove('hidden');
            });
        });

        cancelDeleteBtn.addEventListener('click', () => {
            deleteModal.classList.add('hidden');
        });

        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal) {
                deleteModal.classList.add('hidden');
            }
        });

        // Logins dropdown toggle
        const loginsDropdownBtn = document.getElementById('loginsDropdownBtn');
        const loginsSubmenu = document.getElementById('loginsSubmenu');
        const loginsArrow = document.getElementById('loginsArrow');
        if (loginsDropdownBtn && loginsSubmenu) {
            loginsDropdownBtn.addEventListener('click', () => {
                loginsSubmenu.classList.toggle('hidden');
                loginsArrow.classList.toggle('rotate-180');
            });
        }
    </script>
</body>
</html>
