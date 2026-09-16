<?php
include("header.php");
include("../conexao/banco.php");

$msg = "";
if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    mysqli_query($con, "DELETE FROM TB_Agendamentos WHERE ID_Agendamento = $id");
    header("Location: agendamentos.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cli_id = intval($_POST['TB_Clientes_ID_Cliente']);
    $os_id = !empty($_POST['TB_OrdensServico_ID_Ordem']) ? intval($_POST['TB_OrdensServico_ID_Ordem']) : "NULL";
    $data = mysqli_real_escape_string($con, $_POST['Age_DataAgendada']);
    $status = mysqli_real_escape_string($con, $_POST['Age_Status']);
    $obs = mysqli_real_escape_string($con, $_POST['Age_Observacoes']);
    
    if (isset($_POST['id_agendamento']) && !empty($_POST['id_agendamento'])) {
        $id = intval($_POST['id_agendamento']);
        mysqli_query($con, "UPDATE TB_Agendamentos SET TB_Clientes_ID_Cliente=$cli_id, TB_OrdensServico_ID_Ordem=$os_id, Age_DataAgendada='$data', Age_Status='$status', Age_Observacoes='$obs' WHERE ID_Agendamento=$id");
        $msg = "Agendamento atualizado!";
    } else {
        mysqli_query($con, "INSERT INTO TB_Agendamentos (TB_Clientes_ID_Cliente, TB_OrdensServico_ID_Ordem, Age_DataAgendada, Age_Status, Age_Observacoes) VALUES ($cli_id, $os_id, '$data', '$status', '$obs')");
        $msg = "Agendamento criado com sucesso!";
    }
}

$edit_agendamento = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = mysqli_query($con, "SELECT * FROM TB_Agendamentos WHERE ID_Agendamento = $id");
    $edit_agendamento = mysqli_fetch_assoc($res);
}

$clientes = mysqli_query($con, "SELECT * FROM TB_Clientes ORDER BY cli_Nome ASC");
$ordens = mysqli_query($con, "SELECT ID_Ordem FROM TB_OrdensServico ORDER BY ID_Ordem DESC");
$agendamentos = mysqli_query($con, "SELECT a.*, c.cli_Nome FROM TB_Agendamentos a JOIN TB_Clientes c ON a.TB_Clientes_ID_Cliente = c.ID_Cliente ORDER BY a.Age_DataAgendada DESC");
?>

<div class="mb-8">
    <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-1">Agenda de Serviços</h1>
    <p class="text-slate-400 text-sm">Controle de visitas, podas e instalações agendadas.</p>
</div>

<?php if(!empty($msg)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= $msg ?></div>
<?php endif; ?>

<div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 mb-8">
    <h3 class="font-display font-bold text-lg mb-4"><?= $edit_agendamento ? 'Editar Agendamento' : 'Novo Agendamento' ?></h3>
    <form action="agendamentos.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="hidden" name="id_agendamento" value="<?= $edit_agendamento['ID_Agendamento'] ?? '' ?>">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Cliente</label>
            <select name="TB_Clientes_ID_Cliente" required class="w-full bg-[#162418] border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
                <option value="">Selecione o cliente...</option>
                <?php while($c = mysqli_fetch_assoc($clientes)): ?>
                    <option value="<?= $c['ID_Cliente'] ?>" <?= ($edit_agendamento['TB_Clientes_ID_Cliente'] ?? '') == $c['ID_Cliente'] ? 'selected' : '' ?>><?= htmlspecialchars($c['cli_Nome']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">ID da OS (Opcional)</label>
            <input type="number" name="TB_OrdensServico_ID_Ordem" value="<?= $edit_agendamento['TB_OrdensServico_ID_Ordem'] ?? '' ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="Ex: 1">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Data e Hora Agendada</label>
            <input type="datetime-local" name="Age_DataAgendada" required value="<?= isset($edit_agendamento['Age_DataAgendada']) ? date('Y-m-d\TH:i', strtotime($edit_agendamento['Age_DataAgendada'])) : '' ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Status</label>
            <select name="Age_Status" class="w-full bg-[#162418] border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
                <option value="Agendado" <?= ($edit_agendamento['Age_Status'] ?? '') == 'Agendado' ? 'selected' : '' ?>>Agendado</option>
                <option value="Realizado" <?= ($edit_agendamento['Age_Status'] ?? '') == 'Realizado' ? 'selected' : '' ?>>Realizado</option>
                <option value="Cancelado" <?= ($edit_agendamento['Age_Status'] ?? '') == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Observações</label>
            <input type="text" name="Age_Observacoes" value="<?= $edit_agendamento['Age_Observacoes'] ?? '' ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>
        <div class="md:col-span-3 flex items-center gap-3 mt-2">
            <button type="submit" class="bg-[#b7f052] text-[#0f1710] font-bold py-2.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider"><?= $edit_agendamento ? 'Atualizar Agendamento' : 'Salvar Agendamento' ?></button>
            <?php if($edit_agendamento): ?><a href="agendamentos.php" class="bg-white/10 text-slate-300 font-bold py-2.5 px-6 rounded-xl hover:bg-white/25 transition-all text-xs uppercase tracking-wider">Cancelar</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-[#121c14] border border-white/10 rounded-2xl overflow-hidden">
    <div class="p-6 border-b border-white/10"><h3 class="font-display font-bold text-lg">Agendamentos Registrados</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-white/10 bg-black/20 text-xs uppercase tracking-wider text-slate-400">
                    <th class="p-4">ID</th>
                    <th class="p-4">Cliente</th>
                    <th class="p-4">OS Relacionada</th>
                    <th class="p-4">Data/Hora</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Observações</th>
                    <th class="p-4 text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-sm">
                <?php while($row = mysqli_fetch_assoc($agendamentos)): ?>
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="p-4 font-mono text-xs text-[#b7f052]">#<?= $row['ID_Agendamento'] ?></td>
                    <td class="p-4 font-bold text-white"><?= htmlspecialchars($row['cli_Nome']) ?></td>
                    <td class="p-4 font-mono text-xs"><?= $row['TB_OrdensServico_ID_Ordem'] ? '#'.$row['TB_OrdensServico_ID_Ordem'] : '-' ?></td>
                    <td class="p-4 text-slate-300"><?= date('d/m/Y H:i', strtotime($row['Age_DataAgendada'])) ?></td>
                    <td class="p-4"><span class="px-2.5 py-1 rounded-full text-xs bg-white/5 border border-white/10"><?= htmlspecialchars($row['Age_Status']) ?></span></td>
                    <td class="p-4 text-slate-300"><?= htmlspecialchars($row['Age_Observacoes']) ?></td>
                    <td class="p-4 text-center space-x-2">
                        <a href="agendamentos.php?acao=editar&id=<?= $row['ID_Agendamento'] ?>" class="text-blue-400 hover:underline text-xs">Editar</a>
                        <a href="agendamentos.php?acao=excluir&id=<?= $row['ID_Agendamento'] ?>" onclick="return confirm('Excluir agendamento?');" class="text-red-400 hover:underline text-xs">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("footer.php"); ?>
