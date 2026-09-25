<?php
include("../conexao/banco.php");

$msg = "";
$msg_erro = "";

if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if (!mysqli_query($con, "DELETE FROM TB_ContasReceber WHERE ID_ContaReceber = $id")) {
        $msg_erro = "Não foi possível excluir: esta conta possui registros vinculados. Detalhe: " . mysqli_error($con);
    } else {
        $msg = "Conta excluída com sucesso!";
    }

    if (empty($msg_erro)) {
        header("Location: financeiro.php?ok=1");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_conta'])) {
    $id = intval($_POST['id_conta']);
    $cli_id = intval($_POST['TB_Clientes_ID_Cliente']);
    $os_id = !empty($_POST['TB_OrdensServico_ID_Ordem']) ? intval($_POST['TB_OrdensServico_ID_Ordem']) : "NULL";
    $valor = max(0, floatval(str_replace(',', '.', str_replace(['R$', ' '], '', $_POST['Con_Valor']))));
    $vencimento = mysqli_real_escape_string($con, $_POST['Con_DataVencimento']);
    $status = mysqli_real_escape_string($con, $_POST['Con_Status']);
    $forma = mysqli_real_escape_string($con, $_POST['Con_FormaPagamento']);
    
    if (mysqli_query($con, "UPDATE TB_ContasReceber SET TB_Clientes_ID_Cliente=$cli_id, TB_OrdensServico_ID_Ordem=$os_id, Con_Valor=$valor, Con_DataVencimento='$vencimento', Con_Status='$status', Con_FormaPagamento='$forma' WHERE ID_ContaReceber=$id")) {
        header("Location: financeiro.php?ok=update");
        exit();
    } else {
        $msg_erro = "Erro ao atualizar: " . mysqli_error($con);
    }
}

if (isset($_GET['ok'])) {
    if ($_GET['ok'] == 'update') $msg = "Conta a receber atualizada com sucesso!";
    if ($_GET['ok'] == '1')      $msg = "Conta excluída com sucesso!";
}

$edit_conta = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = mysqli_query($con, "SELECT * FROM TB_ContasReceber WHERE ID_ContaReceber = $id");
    $edit_conta = mysqli_fetch_assoc($res);
}

$clientes = mysqli_query($con, "SELECT * FROM TB_Clientes ORDER BY cli_Nome ASC");
$contas = mysqli_query($con, "SELECT cr.*, c.cli_Nome FROM TB_ContasReceber cr JOIN TB_Clientes c ON cr.TB_Clientes_ID_Cliente = c.ID_Cliente ORDER BY cr.Con_DataVencimento DESC");

include("header.php");
?>

<div class="mb-8">
    <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-1">Contas a Receber Existentes</h1>
    <p class="text-slate-400 text-sm">Visualize, edite ou gerencie os recebimentos e faturas da Golden Jardim.</p>
</div>

<?php if(!empty($msg)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if(!empty($msg_erro)): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm"><?= htmlspecialchars($msg_erro) ?></div>
<?php endif; ?>

<?php if($edit_conta): ?>
<div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 mb-8 max-w-3xl">
    <h3 class="font-display font-bold text-lg mb-4">Editar Conta a Receber (#<?= $edit_conta['ID_ContaReceber'] ?>)</h3>
    <form action="financeiro.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="hidden" name="id_conta" value="<?= $edit_conta['ID_ContaReceber'] ?>">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Cliente</label>
            <select name="TB_Clientes_ID_Cliente" required class="w-full bg-[#162418] border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
                <option value="">Selecione o cliente...</option>
                <?php 
                $cli_res = mysqli_query($con, "SELECT * FROM TB_Clientes ORDER BY cli_Nome ASC");
                while($c = mysqli_fetch_assoc($cli_res)): 
                ?>
                    <option value="<?= $c['ID_Cliente'] ?>" <?= $edit_conta['TB_Clientes_ID_Cliente'] == $c['ID_Cliente'] ? 'selected' : '' ?>><?= htmlspecialchars($c['cli_Nome']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">ID da OS (Opcional)</label>
            <input type="number" name="TB_OrdensServico_ID_Ordem" value="<?= $edit_conta['TB_OrdensServico_ID_Ordem'] ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="Ex: 1">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Valor (R$)</label>
            <input type="text" name="Con_Valor" required value="<?= $edit_conta['Con_Valor'] ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="0.00">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Data de Vencimento</label>
            <input type="date" name="Con_DataVencimento" required value="<?= $edit_conta['Con_DataVencimento'] ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Status</label>
            <select name="Con_Status" class="w-full bg-[#162418] border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
                <option value="Pendente" <?= $edit_conta['Con_Status'] == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                <option value="Pago" <?= $edit_conta['Con_Status'] == 'Pago' ? 'selected' : '' ?>>Pago</option>
                <option value="Cancelado" <?= $edit_conta['Con_Status'] == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Forma de Pagamento</label>
            <input type="text" name="Con_FormaPagamento" value="<?= htmlspecialchars($edit_conta['Con_FormaPagamento']) ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="Pix, Cartão, Boleto...">
        </div>
        <div class="md:col-span-2 flex items-center gap-3 pt-2">
            <button type="submit" class="bg-[#b7f052] text-[#0f1710] font-bold py-2.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider">Atualizar Conta</button>
            <a href="financeiro.php" class="bg-white/10 text-slate-300 font-bold py-2.5 px-6 rounded-xl hover:bg-white/25 transition-all text-xs uppercase tracking-wider">Cancelar</a>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="bg-[#121c14] border border-white/10 rounded-2xl overflow-hidden mb-8">
    <div class="p-6 border-b border-white/10 flex items-center justify-between">
        <h3 class="font-display font-bold text-lg">Contas a Receber Cadastradas</h3>
        <a href="cadastrar_conta.php" class="bg-[#b7f052] text-[#0f1710] font-bold py-2 px-4 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider">+ Nova Conta</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-white/10 bg-black/20 text-xs uppercase tracking-wider text-slate-400">
                    <th class="p-4">ID</th>
                    <th class="p-4">Cliente</th>
                    <th class="p-4">Valor</th>
                    <th class="p-4">Vencimento</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Forma</th>
                    <th class="p-4 text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-sm">
                <?php while($row = mysqli_fetch_assoc($contas)): ?>
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="p-4 font-mono text-xs text-[#b7f052]">#<?= $row['ID_ContaReceber'] ?></td>
                    <td class="p-4 font-bold text-white"><?= htmlspecialchars($row['cli_Nome']) ?></td>
                    <td class="p-4 font-mono text-emerald-400">R$ <?= number_format($row['Con_Valor'], 2, ',', '.') ?></td>
                    <td class="p-4 text-slate-300"><?= date('d/m/Y', strtotime($row['Con_DataVencimento'])) ?></td>
                    <td class="p-4"><span class="px-2.5 py-1 rounded-full text-xs bg-white/5 border border-white/10"><?= htmlspecialchars($row['Con_Status']) ?></span></td>
                    <td class="p-4 text-slate-300"><?= htmlspecialchars($row['Con_FormaPagamento']) ?></td>
                    <td class="p-4 text-center space-x-2">
                        <a href="financeiro.php?acao=editar&id=<?= $row['ID_ContaReceber'] ?>" class="text-blue-400 hover:underline text-xs">Editar</a>
                        <a href="financeiro.php?acao=excluir&id=<?= $row['ID_ContaReceber'] ?>" class="text-red-400 hover:underline text-xs" data-confirm-msg="Tem certeza que deseja excluir esta conta a receber?">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("footer.php"); ?>
