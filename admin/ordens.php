<?php
include("header.php");
include("../conexao/banco.php");

$msg = "";
if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    mysqli_query($con, "DELETE FROM TB_OrdensServico WHERE ID_Ordem = $id");
    header("Location: ordens.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cli_id = intval($_POST['TB_Clientes_ID_Cliente']);
    $status = mysqli_real_escape_string($con, $_POST['Or_Status']);
    $valor = floatval(str_replace(',', '.', $_POST['Ord_ValorTotal']));
    $obs = mysqli_real_escape_string($con, $_POST['Ord_Observacoes']);
    
    if (isset($_POST['id_ordem']) && !empty($_POST['id_ordem'])) {
        $id = intval($_POST['id_ordem']);
        mysqli_query($con, "UPDATE TB_OrdensServico SET TB_Clientes_ID_Cliente=$cli_id, Or_Status='$status', Ord_ValorTotal=$valor, Ord_Observacoes='$obs' WHERE ID_Ordem=$id");
        $msg = "Ordem de Serviço atualizada!";
    } else {
        mysqli_query($con, "INSERT INTO TB_OrdensServico (TB_Clientes_ID_Cliente, Or_Status, Ord_ValorTotal, Ord_Observacoes) VALUES ($cli_id, '$status', $valor, '$obs')");
        $msg = "Ordem de Serviço criada com sucesso!";
    }
}

$edit_ordem = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = mysqli_query($con, "SELECT * FROM TB_OrdensServico WHERE ID_Ordem = $id");
    $edit_ordem = mysqli_fetch_assoc($res);
}

$clientes = mysqli_query($con, "SELECT * FROM TB_Clientes ORDER BY cli_Nome ASC");
$ordens = mysqli_query($con, "SELECT o.*, c.cli_Nome FROM TB_OrdensServico o JOIN TB_Clientes c ON o.TB_Clientes_ID_Cliente = c.ID_Cliente ORDER BY o.ID_Ordem DESC");
?>

<div class="mb-8">
    <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-1">Ordens de Serviço</h1>
    <p class="text-slate-400 text-sm">Controle de execução e projetos de paisagismo e manutenção.</p>
</div>

<?php if(!empty($msg)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= $msg ?></div>
<?php endif; ?>

<div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 mb-8">
    <h3 class="font-display font-bold text-lg mb-4"><?= $edit_ordem ? 'Editar Ordem de Serviço' : 'Nova Ordem de Serviço' ?></h3>
    <form action="ordens.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="hidden" name="id_ordem" value="<?= (isset($edit_ordem['ID_Ordem']) ? $edit_ordem['ID_Ordem'] : '') ?>">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Cliente</label>
            <select name="TB_Clientes_ID_Cliente" required class="w-full bg-[#162418] border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
                <option value="">Selecione o cliente...</option>
                <?php while($c = mysqli_fetch_assoc($clientes)): ?>
                    <option value="<?= $c['ID_Cliente'] ?>" <?= ((isset($edit_ordem['TB_Clientes_ID_Cliente']) ? $edit_ordem['TB_Clientes_ID_Cliente'] : '')) == $c['ID_Cliente'] ? 'selected' : '' ?>><?= htmlspecialchars($c['cli_Nome']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Status</label>
            <select name="Or_Status" class="w-full bg-[#162418] border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
                <option value="Em Andamento" <?= ((isset($edit_ordem['Or_Status']) ? $edit_ordem['Or_Status'] : '')) == 'Em Andamento' ? 'selected' : '' ?>>Em Andamento</option>
                <option value="Concluído" <?= ((isset($edit_ordem['Or_Status']) ? $edit_ordem['Or_Status'] : '')) == 'Concluído' ? 'selected' : '' ?>>Concluído</option>
                <option value="Cancelado" <?= ((isset($edit_ordem['Or_Status']) ? $edit_ordem['Or_Status'] : '')) == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Valor Total (R$)</label>
            <input type="text" name="Ord_ValorTotal" required value="<?= (isset($edit_ordem['Ord_ValorTotal']) ? $edit_ordem['Ord_ValorTotal'] : '') ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="0.00">
        </div>
        <div class="md:col-span-3">
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Observações</label>
            <input type="text" name="Ord_Observacoes" value="<?= (isset($edit_ordem['Ord_Observacoes']) ? $edit_ordem['Ord_Observacoes'] : '') ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>
        <div class="md:col-span-3 flex items-center gap-3 mt-2">
            <button type="submit" class="bg-[#b7f052] text-[#0f1710] font-bold py-2.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider"><?= $edit_ordem ? 'Atualizar OS' : 'Criar OS' ?></button>
            <?php if($edit_ordem): ?><a href="ordens.php" class="bg-white/10 text-slate-300 font-bold py-2.5 px-6 rounded-xl hover:bg-white/25 transition-all text-xs uppercase tracking-wider">Cancelar</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-[#121c14] border border-white/10 rounded-2xl overflow-hidden">
    <div class="p-6 border-b border-white/10"><h3 class="font-display font-bold text-lg">Ordens de Serviço Registradas</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-white/10 bg-black/20 text-xs uppercase tracking-wider text-slate-400">
                    <th class="p-4">ID</th>
                    <th class="p-4">Cliente</th>
                    <th class="p-4">Data</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Valor Total</th>
                    <th class="p-4">Observações</th>
                    <th class="p-4 text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-sm">
                <?php while($row = mysqli_fetch_assoc($ordens)): ?>
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="p-4 font-mono text-xs text-[#b7f052]">#<?= $row['ID_Ordem'] ?></td>
                    <td class="p-4 font-bold text-white"><?= htmlspecialchars($row['cli_Nome']) ?></td>
                    <td class="p-4 text-slate-300"><?= date('d/m/Y H:i', strtotime($row['Or_DataServico'])) ?></td>
                    <td class="p-4"><span class="px-2.5 py-1 rounded-full text-xs bg-white/5 border border-white/10"><?= htmlspecialchars($row['Or_Status']) ?></span></td>
                    <td class="p-4 font-mono text-emerald-400">R$ <?= number_format($row['Ord_ValorTotal'], 2, ',', '.') ?></td>
                    <td class="p-4 text-slate-300"><?= htmlspecialchars($row['Ord_Observacoes']) ?></td>
                    <td class="p-4 text-center space-x-2">
                        <a href="ordens.php?acao=editar&id=<?= $row['ID_Ordem'] ?>" class="text-blue-400 hover:underline text-xs">Editar</a>
                        <a href="ordens.php?acao=excluir&id=<?= $row['ID_Ordem'] ?>" onclick="return confirm('Excluir OS?');" class="text-red-400 hover:underline text-xs">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("footer.php"); ?>
