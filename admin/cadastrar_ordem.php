<?php
include("../conexao/banco.php");

$msg = "";
$msg_erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cli_id = intval($_POST['TB_Clientes_ID_Cliente']);
    $status = mysqli_real_escape_string($con, $_POST['Or_Status']);
    $valor = max(0, floatval(str_replace(',', '.', str_replace(['R$', ' '], '', $_POST['Ord_ValorTotal']))));
    $obs = mysqli_real_escape_string($con, $_POST['Ord_Observacoes']);
    
    if (empty($cli_id)) {
        $msg_erro = "O cliente é obrigatório.";
    } else {
        if (mysqli_query($con, "INSERT INTO TB_OrdensServico (TB_Clientes_ID_Cliente, Or_Status, Ord_ValorTotal, Ord_Observacoes) VALUES ($cli_id, '$status', $valor, '$obs')")) {
            header("Location: cadastrar_ordem.php?ok=insert");
            exit();
        } else {
            $msg_erro = "Erro ao cadastrar: " . mysqli_error($con);
        }
    }
}

if (isset($_GET['ok']) && $_GET['ok'] == 'insert') {
    $msg = "Ordem de Serviço criada com sucesso!";
}

$clientes = mysqli_query($con, "SELECT * FROM TB_Clientes ORDER BY cli_Nome ASC");

include("header.php");
?>

<div class="mb-8">
    <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-1">Cadastrar Nova Ordem de Serviço</h1>
    <p class="text-slate-400 text-sm">Registre um novo projeto ou serviço de paisagismo e manutenção.</p>
</div>

<?php if(!empty($msg)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if(!empty($msg_erro)): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm"><?= htmlspecialchars($msg_erro) ?></div>
<?php endif; ?>

<div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 mb-8 max-w-3xl">
    <h3 class="font-display font-bold text-lg mb-4">Informações da Ordem de Serviço</h3>
    <form action="cadastrar_ordem.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Status</label>
            <select name="Or_Status" class="w-full bg-[#162418] border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
                <option value="Em Andamento">Em Andamento</option>
                <option value="Concluído">Concluído</option>
                <option value="Cancelado">Cancelado</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Valor Total (R$)</label>
            <input type="text" name="Ord_ValorTotal" required class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="0.00">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Observações</label>
            <input type="text" name="Ord_Observacoes" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="Detalhes da OS">
        </div>
        <div class="md:col-span-2 flex items-center gap-3 pt-2">
            <button type="submit" class="bg-[#b7f052] text-[#0f1710] font-bold py-2.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider">Salvar Ordem de Serviço</button>
            <a href="ordens.php" class="bg-white/10 text-slate-300 font-bold py-2.5 px-6 rounded-xl hover:bg-white/25 transition-all text-xs uppercase tracking-wider">Ordens Existentes</a>
        </div>
    </form>
</div>

<?php include("footer.php"); ?>
