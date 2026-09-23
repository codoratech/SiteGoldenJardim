<?php
include("header.php");
include("../conexao/banco.php");

$msg = "";
if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    mysqli_query($con, "DELETE FROM TB_Produtos WHERE ID_Produto = $id");
    header("Location: produtos.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = mysqli_real_escape_string($con, $_POST['Pro_Nome']);
    $desc = mysqli_real_escape_string($con, $_POST['Pro_Descricao']);
    $preco = floatval(str_replace(',', '.', $_POST['Pro_Preco']));
    
    if (isset($_POST['id_produto']) && !empty($_POST['id_produto'])) {
        $id = intval($_POST['id_produto']);
        mysqli_query($con, "UPDATE TB_Produtos SET Pro_Nome='$nome', Pro_Descricao='$desc', Pro_Preco=$preco WHERE ID_Produto=$id");
        $msg = "Produto atualizado com sucesso!";
    } else {
        mysqli_query($con, "INSERT INTO TB_Produtos (Pro_Nome, Pro_Descricao, Pro_Preco) VALUES ('$nome', '$desc', $preco)");
        $msg = "Produto cadastrado com sucesso!";
    }
}

$edit_produto = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = mysqli_query($con, "SELECT * FROM TB_Produtos WHERE ID_Produto = $id");
    $edit_produto = mysqli_fetch_assoc($res);
}

$produtos = mysqli_query($con, "SELECT * FROM TB_Produtos ORDER BY ID_Produto DESC");
?>

<div class="mb-8">
    <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-1">Gerenciamento de Produtos</h1>
    <p class="text-slate-400 text-sm">Catálogo de produtos e insumos da Golden Jardim.</p>
</div>

<?php if(!empty($msg)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= $msg ?></div>
<?php endif; ?>

<div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 mb-8">
    <h3 class="font-display font-bold text-lg mb-4"><?= $edit_produto ? 'Editar Produto' : 'Novo Produto' ?></h3>
    <form action="produtos.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="hidden" name="id_produto" value="<?= (isset($edit_produto['ID_Produto']) ? $edit_produto['ID_Produto'] : '') ?>">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Nome do Produto</label>
            <input type="text" name="Pro_Nome" required value="<?= (isset($edit_produto['Pro_Nome']) ? $edit_produto['Pro_Nome'] : '') ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Preço (R$)</label>
            <input type="text" name="Pro_Preco" required value="<?= (isset($edit_produto['Pro_Preco']) ? $edit_produto['Pro_Preco'] : '') ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="0.00">
        </div>
        <div class="md:col-span-3">
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Descrição</label>
            <input type="text" name="Pro_Descricao" value="<?= (isset($edit_produto['Pro_Descricao']) ? $edit_produto['Pro_Descricao'] : '') ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>
        <div class="md:col-span-3 flex items-center gap-3 mt-2">
            <button type="submit" class="bg-[#b7f052] text-[#0f1710] font-bold py-2.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider"><?= $edit_produto ? 'Atualizar' : 'Salvar Produto' ?></button>
            <?php if($edit_produto): ?><a href="produtos.php" class="bg-white/10 text-slate-300 font-bold py-2.5 px-6 rounded-xl hover:bg-white/25 transition-all text-xs uppercase tracking-wider">Cancelar</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-[#121c14] border border-white/10 rounded-2xl overflow-hidden">
    <div class="p-6 border-b border-white/10"><h3 class="font-display font-bold text-lg">Produtos Cadastrados</h3></div>
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
                <?php while($row = mysqli_fetch_assoc($produtos)): ?>
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="p-4 font-mono text-xs text-[#b7f052]">#<?= $row['ID_Produto'] ?></td>
                    <td class="p-4 font-bold text-white"><?= htmlspecialchars($row['Pro_Nome']) ?></td>
                    <td class="p-4 text-slate-300"><?= htmlspecialchars($row['Pro_Descricao']) ?></td>
                    <td class="p-4 font-mono text-emerald-400">R$ <?= number_format($row['Pro_Preco'], 2, ',', '.') ?></td>
                    <td class="p-4 text-center space-x-2">
                        <a href="produtos.php?acao=editar&id=<?= $row['ID_Produto'] ?>" class="text-blue-400 hover:underline text-xs">Editar</a>
                        <a href="produtos.php?acao=excluir&id=<?= $row['ID_Produto'] ?>" onclick="return confirm('Excluir produto?');" class="text-red-400 hover:underline text-xs">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("footer.php"); ?>
