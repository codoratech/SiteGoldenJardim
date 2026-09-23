<?php
include("header.php");
include("../conexao/banco.php");

$msg = "";
if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    mysqli_query($con, "DELETE FROM TB_Servicos WHERE ID_Servico = $id");
    header("Location: servicos.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = mysqli_real_escape_string($con, $_POST['Ser_Nome']);
    $desc = mysqli_real_escape_string($con, $_POST['Ser_Descricao']);
    $preco = floatval(str_replace(',', '.', $_POST['Ser_Preco']));
    
    if (isset($_POST['id_servico']) && !empty($_POST['id_servico'])) {
        $id = intval($_POST['id_servico']);
        mysqli_query($con, "UPDATE TB_Servicos SET Ser_Nome='$nome', Ser_Descricao='$desc', Ser_Preco=$preco WHERE ID_Servico=$id");
        $msg = "Serviço atualizado com sucesso!";
    } else {
        mysqli_query($con, "INSERT INTO TB_Servicos (Ser_Nome, Ser_Descricao, Ser_Preco) VALUES ('$nome', '$desc', $preco)");
        $msg = "Serviço cadastrado com sucesso!";
    }
}

$edit_servico = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = mysqli_query($con, "SELECT * FROM TB_Servicos WHERE ID_Servico = $id");
    $edit_servico = mysqli_fetch_assoc($res);
}

$servicos = mysqli_query($con, "SELECT * FROM TB_Servicos ORDER BY ID_Servico DESC");
?>

<div class="mb-8">
    <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-1">Gerenciamento de Serviços</h1>
    <p class="text-slate-400 text-sm">Serviços de paisagismo, poda e manutenção prestados pela Golden Jardim.</p>
</div>

<?php if(!empty($msg)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= $msg ?></div>
<?php endif; ?>

<div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 mb-8">
    <h3 class="font-display font-bold text-lg mb-4"><?= $edit_servico ? 'Editar Serviço' : 'Novo Serviço' ?></h3>
    <form action="servicos.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="hidden" name="id_servico" value="<?= (isset($edit_servico['ID_Servico']) ? $edit_servico['ID_Servico'] : '') ?>">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Nome do Serviço</label>
            <input type="text" name="Ser_Nome" required value="<?= (isset($edit_servico['Ser_Nome']) ? $edit_servico['Ser_Nome'] : '') ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Preço Base (R$)</label>
            <input type="text" name="Ser_Preco" required value="<?= (isset($edit_servico['Ser_Preco']) ? $edit_servico['Ser_Preco'] : '') ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="0.00">
        </div>
        <div class="md:col-span-3">
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Descrição</label>
            <input type="text" name="Ser_Descricao" value="<?= (isset($edit_servico['Ser_Descricao']) ? $edit_servico['Ser_Descricao'] : '') ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>
        <div class="md:col-span-3 flex items-center gap-3 mt-2">
            <button type="submit" class="bg-[#b7f052] text-[#0f1710] font-bold py-2.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider"><?= $edit_servico ? 'Atualizar' : 'Salvar Serviço' ?></button>
            <?php if($edit_servico): ?><a href="servicos.php" class="bg-white/10 text-slate-300 font-bold py-2.5 px-6 rounded-xl hover:bg-white/25 transition-all text-xs uppercase tracking-wider">Cancelar</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-[#121c14] border border-white/10 rounded-2xl overflow-hidden">
    <div class="p-6 border-b border-white/10"><h3 class="font-display font-bold text-lg">Serviços Cadastrados</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-white/10 bg-black/20 text-xs uppercase tracking-wider text-slate-400">
                    <th class="p-4">ID</th>
                    <th class="p-4">Nome</th>
                    <th class="p-4">Descrição</th>
                    <th class="p-4">Preço Base</th>
                    <th class="p-4 text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-sm">
                <?php while($row = mysqli_fetch_assoc($servicos)): ?>
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="p-4 font-mono text-xs text-[#b7f052]">#<?= $row['ID_Servico'] ?></td>
                    <td class="p-4 font-bold text-white"><?= htmlspecialchars($row['Ser_Nome']) ?></td>
                    <td class="p-4 text-slate-300"><?= htmlspecialchars($row['Ser_Descricao']) ?></td>
                    <td class="p-4 font-mono text-emerald-400">R$ <?= number_format($row['Ser_Preco'], 2, ',', '.') ?></td>
                    <td class="p-4 text-center space-x-2">
                        <a href="servicos.php?acao=editar&id=<?= $row['ID_Servico'] ?>" class="text-blue-400 hover:underline text-xs">Editar</a>
                        <a href="servicos.php?acao=excluir&id=<?= $row['ID_Servico'] ?>" onclick="return confirm('Excluir serviço?');" class="text-red-400 hover:underline text-xs">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("footer.php"); ?>
