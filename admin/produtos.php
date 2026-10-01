<?php
require_once __DIR__ . "/bootstrap.php";
require_login();
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['acao'] ?? '') !== 'excluir') {
    foreach (['Pro_Nome', 'Pro_Preco'] as $field) {
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
    if (!inventory_execute($con, 'DELETE FROM tb_produtos WHERE ID_Produto = ?', 'i', [$id])) {
        $msg_erro = "Não foi possível excluir: este produto possui registros vinculados. Detalhe: " . database_error($con);
    } else {
        $msg = "Produto excluído com sucesso!";
    }

    if (empty($msg_erro)) {
        header("Location: produtos.php?ok=1");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_produto'])) {
    $id = intval($_POST['id_produto']);
    $nome = trim($_POST['Pro_Nome']);
    $desc = $_POST['Pro_Descricao'] ?? '';
    $minimum = inventory_minimum();
    $preco = max(0, floatval(str_replace(',', '.', str_replace(['R$', ' '], '', $_POST['Pro_Preco']))));
    
    if (inventory_execute($con, 'UPDATE tb_produtos SET Pro_Nome=?, Pro_Descricao=?, Pro_Preco=?, Pro_EstoqueMinimo=? WHERE ID_Produto=?', 'ssdii', [$nome, $desc, $preco, $minimum, $id])) {
        header("Location: produtos.php?ok=update");
        exit();
    } else {
        $msg_erro = "Erro ao atualizar: " . database_error($con);
    }
}

if (isset($_GET['ok'])) {
    if ($_GET['ok'] == 'update') $msg = "Produto atualizado com sucesso!";
    if ($_GET['ok'] == '1')      $msg = "Produto excluído com sucesso!";
}

$edit_produto = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $edit_produto = dashboard_query($con, 'SELECT * FROM tb_produtos WHERE ID_Produto = ?', 'i', [$id])[0] ?? null;
}

$produtos = dashboard_query($con, 'SELECT * FROM tb_produtos ORDER BY ID_Produto DESC');

include("header.php");
?>

<div class="mb-8">
    <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-1">Produtos Existentes</h1>
    <p class="text-slate-400 text-sm">Visualize, edite ou gerencie os produtos do catálogo da Golden Jardim.</p>
</div>

<?php if(!empty($msg)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if(!empty($msg_erro)): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm"><?= htmlspecialchars($msg_erro) ?></div>
<?php endif; ?>

<?php if($edit_produto): ?>
<div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 mb-8 max-w-3xl">
    <h3 class="font-display font-bold text-lg mb-4">Editar Produto (#<?= $edit_produto['ID_Produto'] ?>)</h3>
    <form action="produtos.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?= csrf_field() ?>
        <input type="hidden" name="id_produto" value="<?= $edit_produto['ID_Produto'] ?>">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Nome do Produto</label>
            <input type="text" name="Pro_Nome" required value="<?= htmlspecialchars($edit_produto['Pro_Nome']) ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Preço (R$)</label>
            <input type="text" name="Pro_Preco" required value="<?= $edit_produto['Pro_Preco'] ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="0.00">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Descrição</label>
            <input type="text" name="Pro_Descricao" value="<?= htmlspecialchars($edit_produto['Pro_Descricao']) ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>
        <div class="md:col-span-2">
            <label for="productMinimum" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Estoque mínimo (opcional)</label>
            <input id="productMinimum" type="number" min="0" max="2147483647" step="1" name="Pro_EstoqueMinimo" value="<?= dashboard_escape($edit_produto['Pro_EstoqueMinimo'] ?? '') ?>" class="w-full border rounded-xl px-4 py-2.5 text-sm" placeholder="Deixe vazio para não monitorar">
            <p class="text-xs text-slate-400 mt-2">O alerta aparece quando o saldo de entradas e saídas chega ao mínimo.</p>
        </div>
        <div class="md:col-span-2 flex items-center gap-3 pt-2">
            <button type="submit" class="bg-[#b7f052] text-[#0f1710] font-bold py-2.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider">Atualizar Produto</button>
            <a href="produtos.php" class="bg-white/10 text-slate-300 font-bold py-2.5 px-6 rounded-xl hover:bg-white/25 transition-all text-xs uppercase tracking-wider">Cancelar</a>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="bg-[#121c14] border border-white/10 rounded-2xl overflow-hidden mb-8">
    <div class="p-6 border-b border-white/10 flex items-center justify-between">
        <h3 class="font-display font-bold text-lg">Lista de Produtos Cadastrados</h3>
        <a href="cadastrar_produto.php" class="bg-[#b7f052] text-[#0f1710] font-bold py-2 px-4 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider">+ Novo Produto</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-white/10 bg-black/20 text-xs uppercase tracking-wider text-slate-400">
                    <th class="p-4">ID</th>
                    <th class="p-4">Nome</th>
                    <th class="p-4">Descrição</th>
                    <th class="p-4">Preço</th>
                    <th class="p-4 text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-sm">
                <?php foreach($produtos as $row): ?>
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="p-4 font-mono text-xs text-[#b7f052]">#<?= $row['ID_Produto'] ?></td>
                    <td class="p-4 font-bold text-white"><?= htmlspecialchars($row['Pro_Nome']) ?></td>
                    <td class="p-4 text-slate-300"><?= htmlspecialchars($row['Pro_Descricao']) ?></td>
                    <td class="p-4 font-mono text-emerald-400">R$ <?= number_format($row['Pro_Preco'], 2, ',', '.') ?></td>
                    <td class="p-4 text-center space-x-2">
                        <a href="produtos.php?acao=editar&id=<?= $row['ID_Produto'] ?>" class="text-blue-400 hover:underline text-xs">Editar</a>
                        <form action="produtos.php" method="POST" class="inline" data-delete-form data-confirm-msg="Tem certeza que deseja excluir este produto?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="acao" value="excluir">
                            <input type="hidden" name="id" value="<?= $row['ID_Produto'] ?>">
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
