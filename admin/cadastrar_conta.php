<?php
include("../conexao/banco.php");

$msg = "";
$msg_erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cli_id = intval($_POST['TB_Clientes_ID_Cliente']);
    $os_id = !empty($_POST['TB_OrdensServico_ID_Ordem']) ? intval($_POST['TB_OrdensServico_ID_Ordem']) : "NULL";
    $valor = max(0, floatval(str_replace(',', '.', str_replace(['R$', ' '], '', $_POST['Con_Valor']))));
    $vencimento = mysqli_real_escape_string($con, $_POST['Con_DataVencimento']);
    $status = mysqli_real_escape_string($con, $_POST['Con_Status']);
    $forma = mysqli_real_escape_string($con, $_POST['Con_FormaPagamento']);
    
    if (empty($cli_id) || empty($vencimento)) {
        $msg_erro = "Cliente e Data de Vencimento são obrigatórios.";
    } else {
        if (mysqli_query($con, "INSERT INTO TB_ContasReceber (TB_Clientes_ID_Cliente, TB_OrdensServico_ID_Ordem, Con_Valor, Con_DataVencimento, Con_Status, Con_FormaPagamento) VALUES ($cli_id, $os_id, $valor, '$vencimento', '$status', '$forma')")) {
            header("Location: cadastrar_conta.php?ok=insert");
            exit();
        } else {
            $msg_erro = "Erro ao cadastrar: " . mysqli_error($con);
        }
    }
}

if (isset($_GET['ok']) && $_GET['ok'] == 'insert') {
    $msg = "Conta a receber cadastrada com sucesso!";
}

$clientes = mysqli_query($con, "SELECT * FROM TB_Clientes ORDER BY cli_Nome ASC");

include("header.php");
?>

<div class="mb-8">
    <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-1">Cadastrar Conta a Receber</h1>
    <p class="text-slate-400 text-sm">Adicione um novo registro financeiro de recebimento.</p>
</div>

<?php if(!empty($msg)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if(!empty($msg_erro)): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm"><?= htmlspecialchars($msg_erro) ?></div>
<?php endif; ?>

<div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 mb-8 max-w-3xl">
    <h3 class="font-display font-bold text-lg mb-4">Informações da Conta</h3>
    <form action="cadastrar_conta.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Valor (R$)</label>
            <input type="text" name="Con_Valor" required class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="0.00">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Data de Vencimento</label>
            <input type="date" name="Con_DataVencimento" required class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Status</label>
            <select name="Con_Status" class="w-full bg-[#162418] border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
                <option value="Pendente">Pendente</option>
                <option value="Pago">Pago</option>
                <option value="Cancelado">Cancelado</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Forma de Pagamento</label>
            <input type="text" name="Con_FormaPagamento" value="Pix" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="Pix, Cartão, Boleto...">
        </div>
        <div class="md:col-span-2 flex items-center gap-3 pt-2">
            <button type="submit" class="bg-[#b7f052] text-[#0f1710] font-bold py-2.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider">Salvar Conta</button>
            <a href="financeiro.php" class="bg-white/10 text-slate-300 font-bold py-2.5 px-6 rounded-xl hover:bg-white/25 transition-all text-xs uppercase tracking-wider">Contas Existentes</a>
        </div>
    </form>
</div>

<?php include("footer.php"); ?>
