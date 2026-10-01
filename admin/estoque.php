<?php
require_once __DIR__ . "/bootstrap.php";
require_login();
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['acao'] ?? '') !== 'excluir') {
    foreach (['Est_Tipo', 'Est_Quantidade'] as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') reject_request('Preencha todos os campos obrigatórios.');
    }
}

include("../conexao/banco.php");
require_once __DIR__ . "/inventory_helpers.php";

$msg = "";
$msg_erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'excluir' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    if ($id < 1) reject_request('Registro inválido.');
    if (!inventory_execute($con, 'DELETE FROM tb_estoque WHERE ID_Estoque = ?', 'i', [$id])) {
        $msg_erro = "Não foi possível excluir: este registro possui dependências. Detalhe: " . database_error($con);
    } else {
        $msg = "Registro excluído com sucesso!";
    }

    if (empty($msg_erro)) {
        header("Location: estoque.php?ok=1");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_estoque'])) {
    $id = intval($_POST['id_estoque']);
    [$tipo, $qtd, $produto_id] = inventory_movement($con);
    
    if (inventory_execute($con, 'UPDATE tb_estoque SET Est_Tipo=?, Est_Quantidade=?, TB_Produtos_ID_Produto=? WHERE ID_Estoque=?', 'siii', [$tipo, $qtd, $produto_id, $id])) {
        header("Location: estoque.php?ok=update");
        exit();
    } else {
        $msg_erro = "Erro ao atualizar: " . database_error($con);
    }
}

if (isset($_GET['ok'])) {
    if ($_GET['ok'] == 'update') $msg = "Estoque atualizado com sucesso!";
    if ($_GET['ok'] == '1')      $msg = "Registro excluído com sucesso!";
}

$edit_estoque = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $edit_estoque = dashboard_query($con, 'SELECT * FROM tb_estoque WHERE ID_Estoque = ?', 'i', [$id])[0] ?? null;
}

$estoque = dashboard_query($con, 'SELECT e.*, p.Pro_Nome FROM tb_estoque e LEFT JOIN tb_produtos p ON p.ID_Produto = e.TB_Produtos_ID_Produto ORDER BY e.ID_Estoque DESC');

include("header.php");
?>

<div class="mb-8">
    <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-1">Estoque Existente</h1>
    <p class="text-slate-400 text-sm">Visualize, edite ou gerencie os registros e movimentações de estoque.</p>
</div>

<?php if(!empty($msg)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if(!empty($msg_erro)): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm"><?= htmlspecialchars($msg_erro) ?></div>
<?php endif; ?>

<?php if($edit_estoque): ?>
<div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 mb-8 max-w-3xl">
    <h3 class="font-display font-bold text-lg mb-4">Editar Movimentação (#<?= $edit_estoque['ID_Estoque'] ?>)</h3>
    <form action="estoque.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?= csrf_field() ?>
        <input type="hidden" name="id_estoque" value="<?= $edit_estoque['ID_Estoque'] ?>">
        <?php $form_stock = $edit_estoque; include __DIR__ . '/stock_fields.php'; ?>
        <div class="md:col-span-2 flex items-center gap-3 pt-2">
            <button type="submit" class="bg-[#b7f052] text-[#0f1710] font-bold py-2.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider">Atualizar</button>
            <a href="estoque.php" class="bg-white/10 text-slate-300 font-bold py-2.5 px-6 rounded-xl hover:bg-white/25 transition-all text-xs uppercase tracking-wider">Cancelar</a>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="bg-[#121c14] border border-white/10 rounded-2xl overflow-hidden mb-8">
    <div class="p-6 border-b border-white/10 flex items-center justify-between">
        <h3 class="font-display font-bold text-lg">Registros de Estoque</h3>
        <a href="cadastrar_estoque.php" class="bg-[#b7f052] text-[#0f1710] font-bold py-2 px-4 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider">+ Nova Movimentação</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-white/10 bg-black/20 text-xs uppercase tracking-wider text-slate-400">
                    <th class="p-4">ID</th>
                    <th class="p-4">Tipo / Item</th>
                    <th class="p-4">Produto</th>
                    <th class="p-4">Quantidade</th>
                    <th class="p-4">Data da Movimentação</th>
                    <th class="p-4 text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-sm">
                <?php foreach($estoque as $row): ?>
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="p-4 font-mono text-xs text-[#b7f052]">#<?= $row['ID_Estoque'] ?></td>
                    <td class="p-4 font-bold text-white"><?= htmlspecialchars($row['Est_Tipo']) ?></td>
                    <td class="p-4 text-slate-300"><?= dashboard_escape($row['Pro_Nome'] ?? 'Sem vínculo') ?></td>
                    <td class="p-4 font-mono <?= $row['Est_Quantidade'] >= 0 ? 'text-emerald-400' : 'text-red-400' ?>"><?= $row['Est_Quantidade'] ?></td>
                    <td class="p-4 text-slate-300"><?= date('d/m/Y H:i', strtotime($row['Est_DataMovimentacao'])) ?></td>
                    <td class="p-4 text-center space-x-2">
                        <a href="estoque.php?acao=editar&id=<?= $row['ID_Estoque'] ?>" class="text-blue-400 hover:underline text-xs">Editar</a>
                        <form action="estoque.php" method="POST" class="inline" data-delete-form data-confirm-msg="Tem certeza que deseja excluir este registro de estoque?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="acao" value="excluir">
                            <input type="hidden" name="id" value="<?= $row['ID_Estoque'] ?>">
                            <button type="submit" class="text-red-400 hover:underline text-xs">Excluir</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("footer.php"); ?>
