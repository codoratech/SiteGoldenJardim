<?php
require_once __DIR__ . '/bootstrap.php';
require_login();
require_once __DIR__ . '/../conexao/banco.php';
require_once __DIR__ . '/dashboard_data.php';
$stock_products = dashboard_query($con, 'SELECT ID_Produto, Pro_Nome FROM tb_produtos ORDER BY Pro_Nome');
?>
<div>
 <label for="stockProduct" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Produto</label>
 <select name="TB_Produtos_ID_Produto" id="stockProduct" class="w-full border rounded-xl px-4 py-2.5 text-sm">
  <option value="">Sem vínculo · registro legado ou avulso</option>
  <?php foreach ($stock_products as $product): ?><option value="<?= dashboard_escape($product['ID_Produto']) ?>" <?= ($form_stock['TB_Produtos_ID_Produto'] ?? '') == $product['ID_Produto'] ? 'selected' : '' ?>><?= dashboard_escape($product['Pro_Nome']) ?></option><?php endforeach; ?>
 </select>
 <p class="text-xs text-slate-400 mt-2">Somente movimentos vinculados entram no saldo e nos alertas.</p>
</div>
<div>
 <label for="stockType" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Tipo de movimentação</label>
 <select name="Est_Tipo" id="stockType" required class="w-full border rounded-xl px-4 py-2.5 text-sm">
  <?php if (!empty($form_stock['Est_Tipo']) && !in_array($form_stock['Est_Tipo'], ['Entrada','Saida'], true)): ?><option value="<?= dashboard_escape($form_stock['Est_Tipo']) ?>" selected><?= dashboard_escape($form_stock['Est_Tipo']) ?> · legado sem vínculo</option><?php endif; ?>
  <option value="Entrada" <?= ($form_stock['Est_Tipo'] ?? '') === 'Entrada' ? 'selected' : '' ?>>Entrada</option>
  <option value="Saida" <?= ($form_stock['Est_Tipo'] ?? '') === 'Saida' ? 'selected' : '' ?>>Saída</option>
 </select>
</div>
<div><label for="stockQuantity" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Quantidade</label><input id="stockQuantity" type="number" name="Est_Quantidade" min="0" max="2147483647" step="1" required value="<?= dashboard_escape($form_stock['Est_Quantidade'] ?? '') ?>" class="w-full border rounded-xl px-4 py-2.5 text-sm" placeholder="0"></div>
