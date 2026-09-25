<?php
include("../conexao/banco.php");

$msg = "";
$msg_erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome     = mysqli_real_escape_string($con, $_POST['cli_Nome']);
    $tipo     = mysqli_real_escape_string($con, $_POST['cli_Tipo']);
    $telefone = mysqli_real_escape_string($con, $_POST['cli_Telefone']);
    $email    = mysqli_real_escape_string($con, $_POST['cli_Email']);
    $endereco = mysqli_real_escape_string($con, $_POST['cli_Endereco']);

    if (empty($nome)) {
        $msg_erro = "O nome do cliente é obrigatório.";
    } else {
        $sql = "INSERT INTO TB_Clientes (cli_Nome, cli_Tipo, cli_Telefone, cli_Email, cli_Endereco) 
                VALUES ('$nome', '$tipo', '$telefone', '$email', '$endereco')";

        if (mysqli_query($con, $sql)) {
            header("Location: cadastrar_cliente.php?ok=insert");
            exit();
        } else {
            $msg_erro = "Erro ao cadastrar: " . mysqli_error($con);
        }
    }
}

if (isset($_GET['ok']) && $_GET['ok'] == 'insert') {
    $msg = "Cliente cadastrado com sucesso!";
}

include("header.php");
?>

<div class="mb-8">
    <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-1">Cadastrar Novo Cliente</h1>
    <p class="text-slate-400 text-sm">Adicione um novo cliente ao sistema da Golden Jardim.</p>
</div>

<?php if(!empty($msg)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if(!empty($msg_erro)): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm"><?= htmlspecialchars($msg_erro) ?></div>
<?php endif; ?>

<div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 mb-8 max-w-3xl">
    <h3 class="font-display font-bold text-lg mb-4">Informações do Cliente</h3>
    <form action="cadastrar_cliente.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Nome Completo</label>
            <input type="text" name="cli_Nome" required class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="Ex: João da Silva">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Tipo de Cliente</label>
            <select name="cli_Tipo" class="w-full bg-[#162418] border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]">
                <option value="Residencial">Residencial</option>
                <option value="Condomínio">Condomínio</option>
                <option value="Comercial">Comercial</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Telefone / WhatsApp</label>
            <input type="text" name="cli_Telefone" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="(19) 99999-9999">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">E-mail</label>
            <input type="email" name="cli_Email" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="exemplo@email.com">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Endereço</label>
            <input type="text" name="cli_Endereco" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="Rua, Número, Bairro, Cidade">
        </div>
        <div class="md:col-span-2 flex items-center gap-3 pt-2">
            <button type="submit" class="bg-[#b7f052] text-[#0f1710] font-bold py-2.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider">Salvar Cliente</button>
            <a href="clientes.php" class="bg-white/10 text-slate-300 font-bold py-2.5 px-6 rounded-xl hover:bg-white/25 transition-all text-xs uppercase tracking-wider">Clientes Existentes</a>
        </div>
    </form>
</div>

<?php include("footer.php"); ?>
