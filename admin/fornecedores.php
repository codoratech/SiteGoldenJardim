<?php
include("header.php");
include("../conexao/banco.php");

$msg = "";
if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    mysqli_query($con, "DELETE FROM TB_Fornecedores WHERE ID_Fornecedor = $id");
    header("Location: fornecedores.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = mysqli_real_escape_string($con, $_POST['For_Nome']);
    $telefone = mysqli_real_escape_string($con, $_POST['For_Telefone']);
    $endereco = mysqli_real_escape_string($con, $_POST['For_Endereco']);
    
    if (isset($_POST['id_fornecedor']) && !empty($_POST['id_fornecedor'])) {
        $id = intval($_POST['id_fornecedor']);
        mysqli_query($con, "UPDATE TB_Fornecedores SET For_Nome='$nome', For_Telefone='$telefone', For_Endereco='$endereco' WHERE ID_Fornecedor=$id");
        $msg = "Fornecedor atualizado com sucesso!";
    } else {
        mysqli_query($con, "INSERT INTO TB_Fornecedores (For_Nome, For_Telefone, For_Endereco) VALUES ('$nome', '$telefone', '$endereco')");
        $msg = "Fornecedor cadastrado com sucesso!";
    }
}

$edit_fornecedor = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = mysqli_query($con, "SELECT * FROM TB_Fornecedores WHERE ID_Fornecedor = $id");
    $edit_fornecedor = mysqli_fetch_assoc($res);
}

$fornecedores = mysqli_query($con, "SELECT * FROM TB_Fornecedores ORDER BY ID_Fornecedor DESC");
?>

<div class="mb-8">
    <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-1">Gerenciamento de Fornecedores</h1>
    <p class="text-slate-400 text-sm">Fornecedores de mudas, insumos, ferramentas e equipamentos.</p>
</div>

<?php if(!empty($msg)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= $msg ?></div>
<?php endif; ?>

<div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 mb-8">
    <h3 class="font-display font-bold text-lg mb-4"><?= $edit_fornecedor ? 'Editar Fornecedor' : 'Novo Fornecedor' ?></h3>
    <form action="fornecedores.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="hidden" name="id_fornecedor" value="<?= $edit_fornecedor['ID_Fornecedor'] ?? '' ?>">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Nome do Fornecedor</label>
            <input type="text" name="For_Nome" required value="<?= $edit_fornecedor['For_Nome'] ?? '' ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Telefone</label>
            <input type="text" name="For_Telefone" value="<?= $edit_fornecedor['For_Telefone'] ?? '' ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Endereço</label>
            <input type="text" name="For_Endereco" value="<?= $edit_fornecedor['For_Endereco'] ?? '' ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>
        <div class="md:col-span-3 flex items-center gap-3 mt-2">
            <button type="submit" class="bg-[#b7f052] text-[#0f1710] font-bold py-2.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider"><?= $edit_fornecedor ? 'Atualizar' : 'Salvar Fornecedor' ?></button>
            <?php if($edit_fornecedor): ?><a href="fornecedores.php" class="bg-white/10 text-slate-300 font-bold py-2.5 px-6 rounded-xl hover:bg-white/25 transition-all text-xs uppercase tracking-wider">Cancelar</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-[#121c14] border border-white/10 rounded-2xl overflow-hidden">
    <div class="p-6 border-b border-white/10"><h3 class="font-display font-bold text-lg">Fornecedores Cadastrados</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-white/10 bg-black/20 text-xs uppercase tracking-wider text-slate-400">
                    <th class="p-4">ID</th>
                    <th class="p-4">Nome</th>
                    <th class="p-4">Telefone</th>
                    <th class="p-4">Endereço</th>
                    <th class="p-4 text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-sm">
                <?php while($row = mysqli_fetch_assoc($fornecedores)): ?>
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="p-4 font-mono text-xs text-[#b7f052]">#<?= $row['ID_Fornecedor'] ?></td>
                    <td class="p-4 font-bold text-white"><?= htmlspecialchars($row['For_Nome']) ?></td>
                    <td class="p-4 text-slate-300"><?= htmlspecialchars($row['For_Telefone']) ?></td>
                    <td class="p-4 text-slate-300"><?= htmlspecialchars($row['For_Endereco']) ?></td>
                    <td class="p-4 text-center space-x-2">
                        <a href="fornecedores.php?acao=editar&id=<?= $row['ID_Fornecedor'] ?>" class="text-blue-400 hover:underline text-xs">Editar</a>
                        <a href="fornecedores.php?acao=excluir&id=<?= $row['ID_Fornecedor'] ?>" onclick="return confirm('Excluir fornecedor?');" class="text-red-400 hover:underline text-xs">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("footer.php"); ?>
