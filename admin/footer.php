 </main>
 <footer class="admin-footer">© <?= date('Y') ?> Golden Jardim <span>Paisagismo & horticultura</span></footer>
</div>
<script src="../assets/js/admin-layout.js?v=3"></script>
<?php if ($page_entity): ?>
<dialog id="deleteModal" class="delete-dialog" aria-labelledby="deleteModalTitle" aria-describedby="deleteModalText"><div class="delete-dialog-content"><?= admin_icon('alert') ?><h2 id="deleteModalTitle">Excluir registro?</h2><p id="deleteModalText">Tem certeza que deseja excluir este registro?</p><div class="dialog-actions"><button id="cancelDeleteBtn" class="secondary-action" type="button">Cancelar</button><button id="confirmDeleteBtn" class="danger-action" type="button">Sim, excluir</button></div></div></dialog>
<script src="../assets/js/admin-manage.js?v=1"></script>
<?php else: ?><script src="../assets/js/admin-forms.js?v=2"></script><?php endif; ?>
<?php if ($current_page === 'dashboard.php'): ?>
<script src="../assets/vendor/chart.umd.min.js" defer></script>
<script src="../assets/js/dashboard.js?v=2" defer></script>
<?php endif; ?>
<?php if (!empty($site_page)): ?><script src="../assets/js/admin-site.js?v=3"></script><?php endif; ?>
</body>
</html>
