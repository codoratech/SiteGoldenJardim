<?php
include("../conexao/banco.php");

$msg = "";
$msg_erro = "";

if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if (!mysqli_query($con, "DELETE FROM TB_Servicos WHERE ID_Servico = $id")) {
        $msg_erro = "Não foi possível excluir: este serviço possui registros vinculados. Detalhe: " . mysqli_error($con);
    } else {
        $msg = "Serviço excluído com sucesso!";
    }

    if (empty($msg_erro)) {
        header("Location: servicos.php?ok=1");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_servico'])) {
    $id = intval($_POST['id_servico']);
    $nome = mysqli_real_escape_string($con, $_POST['Ser_Nome']);
    $desc = mysqli_real_escape_string($con, $_POST['Ser_Descricao']);
    $preco = max(0, floatval(str_replace(',', '.', str_replace(['R$', ' '], '', $_POST['Ser_Preco']))));
    
    if (mysqli_query($con, "UPDATE TB_Servicos SET Ser_Nome='$nome', Ser_Descricao='$desc', Ser_Preco=$preco WHERE ID_Servico=$id")) {
        header("Location: servicos.php?ok=update");
        exit();
    } else {
        $msg_erro = "Erro ao atualizar: " . mysqli_error($con);
    }
}

if (isset($_GET['ok'])) {
    if ($_GET['ok'] == 'update') $msg = "Serviço atualizado com sucesso!";
    if ($_GET['ok'] == '1')      $msg = "Serviço excluído com sucesso!";
}

$edit_servico = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = mysqli_query($con, "SELECT * FROM TB_Servicos WHERE ID_Servico = $id");
    $edit_servico = mysqli_fetch_assoc($res);
}

$servicos = mysqli_query($con, "SELECT * FROM TB_Servicos ORDER BY ID_Servico DESC");

include("header.php");
?>

<div class="mb-8">
    <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-1">Serviços Existentes</h1>
    <p class="text-slate-400 text-sm">Visualize, edite ou gerencie os serviços prestados pela Golden Jardim.</p>
</div>

<?php if(!empty($msg)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if(!empty($msg_erro)): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm"><?= htmlspecialchars($msg_erro) ?></div>
<?php endif; ?>

<?php if($edit_servico): ?>
<div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 mb-8 max-w-3xl">
    <h3 class="font-display font-bold text-lg mb-4">Editar Serviço (#<?= $edit_servico['ID_Servico'] ?>)</h3>
    <form action="servicos.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="hidden" name="id_servico" value="<?= $edit_servico['ID_Servico'] ?>">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Nome do Serviço</label>
            <input type="text" name="Ser_Nome" required value="<?= htmlspecialchars($edit_servico['Ser_Nome']) ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Preço Base (R$)</label>
            <input type="text" name="Ser_Preco" required value="<?= $edit_servico['Ser_Preco'] ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="0.00">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Descrição</label>
            <input type="text" name="Ser_Descricao" value="<?= htmlspecialchars($edit_servico['Ser_Descricao']) ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>
        <div class="md:col-span-2 flex items-center gap-3 pt-2">
            <button type="submit" class="bg-[#b7f052] text-[#0f1710] font-bold py-2.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider">Atualizar Serviço</button>
            <a href="servicos.php" class="bg-white/10 text-slate-300 font-bold py-2.5 px-6 rounded-xl hover:bg-white/25 transition-all text-xs uppercase tracking-wider">Cancelar</a>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="bg-[#121c14] border border-white/10 rounded-2xl overflow-hidden mb-8">
    <div class="p-6 border-b border-white/10 flex items-center justify-between">
        <h3 class="font-display font-bold text-lg">Lista de Serviços Cadastrados</h3>
        <a href="cadastrar_servico.php" class="bg-[#b7f052] text-[#0f1710] font-bold py-2 px-4 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider">+ Novo Serviço</a>
    </div>
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
                        <a href="servicos.php?acao=excluir&id=<?= $row['ID_Servico'] ?>" class="text-red-400 hover:underline text-xs" data-confirm-msg="Tem certeza que deseja excluir este serviço?">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("footer.php"); ?>
