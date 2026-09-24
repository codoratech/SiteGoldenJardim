<?php
// ==========================================================
// 1) CONEXÃO COM O BANCO (antes de qualquer HTML)
// ==========================================================
include("../conexao/banco.php");

$msg = "";
$msg_erro = "";

// ==========================================================
// 2) EXCLUIR CLIENTE
// ==========================================================
if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if (!mysqli_query($con, "DELETE FROM TB_Clientes WHERE ID_Cliente = $id")) {
        // Provavelmente erro de FK (1217): cliente tem vendas/OS vinculadas
        $msg_erro = "Não foi possível excluir: este cliente possui registros vinculados. Detalhe: " . mysqli_error($con);
    } else {
        $msg = "Cliente excluído com sucesso!";
    }

    // Redireciona para evitar reenvio do GET ao recarregar
    if (empty($msg_erro)) {
        header("Location: clientes.php?ok=1");
        exit();
    }
}

// ==========================================================
// 3) CADASTRAR / ATUALIZAR CLIENTE (POST)
// ==========================================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome     = mysqli_real_escape_string($con, $_POST['cli_Nome']);
    $tipo     = mysqli_real_escape_string($con, $_POST['cli_Tipo']);
    $telefone = mysqli_real_escape_string($con, $_POST['cli_Telefone']);
    $email    = mysqli_real_escape_string($con, $_POST['cli_Email']);
    $endereco = mysqli_real_escape_string($con, $_POST['cli_Endereco']);

    if (isset($_POST['id_cliente']) && !empty($_POST['id_cliente'])) {
        $id = intval($_POST['id_cliente']);
        $sql = "UPDATE TB_Clientes 
                SET cli_Nome='$nome', cli_Tipo='$tipo', cli_Telefone='$telefone', 
                    cli_Email='$email', cli_Endereco='$endereco' 
                WHERE ID_Cliente=$id";

        if (mysqli_query($con, $sql)) {
            header("Location: clientes.php?ok=update");
            exit();
        } else {
            $msg_erro = "Erro ao atualizar: " . mysqli_error($con);
        }
    } else {
        $sql = "INSERT INTO TB_Clientes (cli_Nome, cli_Tipo, cli_Telefone, cli_Email, cli_Endereco) 
                VALUES ('$nome', '$tipo', '$telefone', '$email', '$endereco')";

        if (mysqli_query($con, $sql)) {
            header("Location: clientes.php?ok=insert");
            exit();
        } else {
            $msg_erro = "Erro ao cadastrar: " . mysqli_error($con);
        }
    }
}

// ==========================================================
// 4) MENSAGENS VIA QUERYSTRING (após redirect)
// ==========================================================
if (isset($_GET['ok'])) {
    if ($_GET['ok'] == 'insert') $msg = "Cliente cadastrado com sucesso!";
    if ($_GET['ok'] == 'update') $msg = "Cliente atualizado com sucesso!";
    if ($_GET['ok'] == '1')      $msg = "Cliente excluído com sucesso!";
}

// ==========================================================
// 5) BUSCAR CLIENTE PARA EDIÇÃO
// ==========================================================
$edit_cliente = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = mysqli_query($con, "SELECT * FROM TB_Clientes WHERE ID_Cliente = $id");
    $edit_cliente = mysqli_fetch_assoc($res);
}

// ==========================================================
// 6) LISTAR TODOS OS CLIENTES
// ==========================================================
$clientes = mysqli_query($con, "SELECT * FROM TB_Clientes ORDER BY ID_Cliente DESC");

// ==========================================================
// 7) AGORA SIM: HTML (header)
// ==========================================================
include("header.php");
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-1">Gerenciamento de Clientes</h1>
        <p class="text-slate-400 text-sm">Cadastre, edite e acompanhe os clientes da Golden Jardim.</p>
    </div>
</div>

<?php if (!empty($msg)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm">
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<?php if (!empty($msg_erro)): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm">
        <?= htmlspecialchars($msg_erro) ?>
    </div>
<?php endif; ?>

<!-- Form Card -->
<div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 mb-8">
    <h3 class="font-display font-bold text-lg mb-4"><?= $edit_cliente ? 'Editar Cliente' : 'Novo Cliente' ?></h3>
    <form action="clientes.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <input type="hidden" name="id_cliente" value="<?= (isset($edit_cliente['ID_Cliente']) ? $edit_cliente['ID_Cliente'] : '') ?>">

        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Nome Completo</label>
            <input type="text" name="cli_Nome" required value="<?= (isset($edit_cliente['cli_Nome']) ? htmlspecialchars($edit_cliente['cli_Nome']) : '') ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Tipo de Cliente</label>
            <select name="cli_Tipo" class="w-full bg-[#162418] border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
                <option value="Residencial" <?= ((isset($edit_cliente['cli_Tipo']) ? $edit_cliente['cli_Tipo'] : '')) == 'Residencial' ? 'selected' : '' ?>>Residencial</option>
                <option value="Condomínio" <?= ((isset($edit_cliente['cli_Tipo']) ? $edit_cliente['cli_Tipo'] : '')) == 'Condomínio' ? 'selected' : '' ?>>Condomínio</option>
                <option value="Comercial" <?= ((isset($edit_cliente['cli_Tipo']) ? $edit_cliente['cli_Tipo'] : '')) == 'Comercial' ? 'selected' : '' ?>>Comercial</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Telefone / WhatsApp</label>
            <input type="text" name="cli_Telefone" value="<?= (isset($edit_cliente['cli_Telefone']) ? htmlspecialchars($edit_cliente['cli_Telefone']) : '') ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="(19) 99999-9999">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">E-mail</label>
            <input type="email" name="cli_Email" value="<?= (isset($edit_cliente['cli_Email']) ? htmlspecialchars($edit_cliente['cli_Email']) : '') ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Endereço</label>
            <input type="text" name="cli_Endereco" value="<?= (isset($edit_cliente['cli_Endereco']) ? htmlspecialchars($edit_cliente['cli_Endereco']) : '') ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
        </div>

        <div class="md:col-span-3 flex items-center gap-3 mt-2">
            <button type="submit" class="bg-[#b7f052] text-[#0f1710] font-bold py-2.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider">
                <?= $edit_cliente ? 'Atualizar Cliente' : 'Salvar Cliente' ?>
            </button>
            <?php if ($edit_cliente): ?>
                <a href="clientes.php" class="bg-white/10 text-slate-300 font-bold py-2.5 px-6 rounded-xl hover:bg-white/25 transition-all text-xs uppercase tracking-wider">Cancelar</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="bg-[#121c14] border border-white/10 rounded-2xl overflow-hidden">
    <div class="p-6 border-b border-white/10">
        <h3 class="font-display font-bold text-lg">Lista de Clientes Cadastrados</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-white/10 bg-black/20 text-xs uppercase tracking-wider text-slate-400">
                    <th class="p-4">ID</th>
                    <th class="p-4">Nome</th>
                    <th class="p-4">Tipo</th>
                    <th class="p-4">Telefone</th>
                    <th class="p-4">E-mail</th>
                    <th class="p-4">Endereço</th>
                    <th class="p-4 text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-sm">
                <?php while ($row = mysqli_fetch_assoc($clientes)): ?>
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="p-4 font-mono text-xs text-[#b7f052]">#<?= $row['ID_Cliente'] ?></td>
                    <td class="p-4 font-bold text-white"><?= htmlspecialchars($row['cli_Nome']) ?></td>
                    <td class="p-4"><span class="px-2.5 py-1 rounded-full text-xs bg-white/5 border border-white/10"><?= htmlspecialchars($row['cli_Tipo']) ?></span></td>
                    <td class="p-4 text-slate-300"><?= htmlspecialchars($row['cli_Telefone']) ?></td>
                    <td class="p-4 text-slate-300"><?= htmlspecialchars($row['cli_Email']) ?></td>
                    <td class="p-4 text-slate-300"><?= htmlspecialchars($row['cli_Endereco']) ?></td>
                    <td class="p-4 text-center space-x-2">
                        <a href="clientes.php?acao=editar&id=<?= $row['ID_Cliente'] ?>" class="text-blue-400 hover:underline text-xs">Editar</a>
                        <a href="clientes.php?acao=excluir&id=<?= $row['ID_Cliente'] ?>" class="text-red-400 hover:underline text-xs">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("footer.php"); ?>