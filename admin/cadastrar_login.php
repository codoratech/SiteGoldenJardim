<?php
include("../conexao/banco.php");

$msg = "";
$msg_erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = mysqli_real_escape_string($con, $_POST['log_nome']);
    $login = mysqli_real_escape_string($con, $_POST['log_login']);
    $senha = $_POST['log_senha'];
    
    if (empty($nome) || empty($login) || empty($senha)) {
        $msg_erro = "Todos os campos são obrigatórios.";
    } else {
        $senha_md5 = md5($senha);
        if (mysqli_query($con, "INSERT INTO tb_login (log_nome, log_login, log_senha) VALUES ('$nome', '$login', '$senha_md5')")) {
            header("Location: cadastrar_login.php?ok=insert");
            exit();
        } else {
            $msg_erro = "Erro ao cadastrar login: " . mysqli_error($con);
        }
    }
}

if (isset($_GET['ok']) && $_GET['ok'] == 'insert') {
    $msg = "Login cadastrado com sucesso!";
}

include("header.php");
?>

<div class="mb-8">
    <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-1">Cadastrar Novo Login</h1>
    <p class="text-slate-400 text-sm">Adicione um novo usuário com acesso ao painel administrativo.</p>
</div>

<?php if(!empty($msg)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if(!empty($msg_erro)): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm"><?= htmlspecialchars($msg_erro) ?></div>
<?php endif; ?>

<div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 mb-8 max-w-2xl">
    <h3 class="font-display font-bold text-lg mb-4">Informações do Credenciamento</h3>
    <form action="cadastrar_login.php" method="POST" class="space-y-4">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Nome do Usuário</label>
            <input type="text" name="log_nome" required class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="Ex: Administrador">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Usuário / Login</label>
            <input type="text" name="log_login" required class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="Ex: adm">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Senha</label>
            <input type="password" name="log_senha" required class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="••••••••">
        </div>
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-[#b7f052] text-[#0f1710] font-bold py-2.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider">Salvar Cadastro</button>
            <a href="logins.php" class="bg-white/10 text-slate-300 font-bold py-2.5 px-6 rounded-xl hover:bg-white/25 transition-all text-xs uppercase tracking-wider">Ver Logins Existentes</a>
        </div>
    </form>
</div>

<?php include("footer.php"); ?>
