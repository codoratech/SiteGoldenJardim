<?php
include("../conexao/banco.php");

$msg = "";
$msg_erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cli_id = intval($_POST['TB_Clientes_ID_Cliente']);
    $os_id = !empty($_POST['TB_OrdensServico_ID_Ordem']) ? intval($_POST['TB_OrdensServico_ID_Ordem']) : "NULL";
    $data = mysqli_real_escape_string($con, $_POST['Age_DataAgendada']);
    $status = mysqli_real_escape_string($con, $_POST['Age_Status']);
    $obs = mysqli_real_escape_string($con, $_POST['Age_Observacoes']);
    
    if (empty($cli_id) || empty($data)) {
        $msg_erro = "Cliente e Data Agendada são obrigatórios.";
    } else {
        if (mysqli_query($con, "INSERT INTO TB_Agendamentos (TB_Clientes_ID_Cliente, TB_OrdensServico_ID_Ordem, Age_DataAgendada, Age_Status, Age_Observacoes) VALUES ($cli_id, $os_id, '$data', '$status', '$obs')")) {
            header("Location: cadastrar_agendamento.php?ok=insert");
            exit();
        } else {
            $msg_erro = "Erro ao cadastrar: " . mysqli_error($con);
        }
    }
}

if (isset($_GET['ok']) && $_GET['ok'] == 'insert') {
    $msg = "Agendamento criado com sucesso!";
}

$clientes = mysqli_query($con, "SELECT * FROM TB_Clientes ORDER BY cli_Nome ASC");

include("header.php");
?>

<div class="mb-8">
    <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-1">Cadastrar Novo Agendamento</h1>
    <p class="text-slate-400 text-sm">Agende uma nova visita, poda ou instalação.</p>
</div>

<?php if(!empty($msg)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if(!empty($msg_erro)): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm"><?= htmlspecialchars($msg_erro) ?></div>
<?php endif; ?>

<div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 mb-8 max-w-3xl">
    <h3 class="font-display font-bold text-lg mb-4">Informações do Agendamento</h3>
    <form action="cadastrar_agendamento.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Cliente</label>
            <select name="TB_Clientes_ID_Cliente" required class="w-full bg-[#162418] border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
                <option value="">Selecione o cliente...</option>
                <?php while($c = mysqli_fetch_assoc($clientes)): ?>
                    <option value="<?= $c['ID_Cliente'] ?>"><?= htmlspecialchars($c['cli_Nome']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">ID da OS (Opcional)</label>
            <input type="number" name="TB_OrdensServico_ID_Ordem" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="Ex: 1">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Data e Hora Agendada</label>
            <input type="datetime-local" name="Age_DataAgendada" required class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Status</label>
            <select name="Age_Status" class="w-full bg-[#162418] border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
                <option value="Agendado">Agendado</option>
                <option value="Realizado">Realizado</option>
                <option value="Cancelado">Cancelado</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Observações</label>
            <input type="text" name="Age_Observacoes" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="Observações do agendamento">
        </div>
        <div class="md:col-span-2 flex items-center gap-3 pt-2">
            <button type="submit" class="bg-[#b7f052] text-[#0f1710] font-bold py-2.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider">Salvar Agendamento</button>
            <a href="agendamentos.php" class="bg-white/10 text-slate-300 font-bold py-2.5 px-6 rounded-xl hover:bg-white/25 transition-all text-xs uppercase tracking-wider">Agendamentos Existentes</a>
        </div>
    </form>
</div>

<?php include("footer.php"); ?>
