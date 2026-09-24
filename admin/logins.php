<?php
include("../conexao/banco.php");

$msg = "";
$msg_erro = "";

// Ensure default 'adm' login exists with password '123' if not present
$check_adm = mysqli_query($con, "SELECT * FROM tb_login WHERE log_login = 'adm'");
if (mysqli_num_rows($check_adm) == 0) {
    mysqli_query($con, "INSERT INTO tb_login (log_nome, log_login, log_senha) VALUES ('Administrador', 'adm', MD5('123'))");
}

if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $count_res = mysqli_query($con, "SELECT COUNT(*) as total FROM tb_login");
    $count_row = mysqli_fetch_assoc($count_res);
    if ($count_row['total'] <= 1) {
        $msg_erro = "Não é possível excluir o único usuário do sistema.";
    } else {
        if (!mysqli_query($con, "DELETE FROM tb_login WHERE log_codigo = $id")) {
            $msg_erro = "Erro ao excluir login: " . mysqli_error($con);
        } else {
            $msg = "Login excluído com sucesso!";
        }
    }

    if (empty($msg_erro)) {
        header("Location: logins.php?ok=1");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = mysqli_real_escape_string($con, $_POST['log_nome']);
    $login = mysqli_real_escape_string($con, $_POST['log_login']);
    $senha = $_POST['log_senha'];
    
    if (isset($_POST['id_login']) && !empty($_POST['id_login'])) {
        $id = intval($_POST['id_login']);
        if (!empty($senha)) {
            $senha_md5 = md5($senha);
            $sql = "UPDATE tb_login SET log_nome='$nome', log_login='$login', log_senha='$senha_md5' WHERE log_codigo=$id";
        } else {
            $sql = "UPDATE tb_login SET log_nome='$nome', log_login='$login' WHERE log_codigo=$id";
        }
        if (mysqli_query($con, $sql)) {
            header("Location: logins.php?ok=update");
            exit();
        } else {
            $msg_erro = "Erro ao atualizar login: " . mysqli_error($con);
        }
    } else {
        if (empty($senha)) {
            $msg_erro = "A senha é obrigatória para novos logins.";
        } else {
            $senha_md5 = md5($senha);
            if (mysqli_query($con, "INSERT INTO tb_login (log_nome, log_login, log_senha) VALUES ('$nome', '$login', '$senha_md5')")) {
                header("Location: logins.php?ok=insert");
                exit();
            } else {
                $msg_erro = "Erro ao cadastrar login: " . mysqli_error($con);
            }
        }
    }
}

if (isset($_GET['ok'])) {
    if ($_GET['ok'] == 'insert') $msg = "Login cadastrado com sucesso!";
    if ($_GET['ok'] == 'update') $msg = "Login atualizado com sucesso!";
    if ($_GET['ok'] == '1')      $msg = "Login excluído com sucesso!";
}

$edit_login = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = mysqli_query($con, "SELECT * FROM tb_login WHERE log_codigo = $id");
    $edit_login = mysqli_fetch_assoc($res);
}

$logins = mysqli_query($con, "SELECT * FROM tb_login ORDER BY log_codigo DESC");

include("header.php");
?>

<div class="mb-8">
    <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-1">Gerenciamento de Logins</h1>
    <p class="text-slate-400 text-sm">Controle de usuários e credenciais com acesso ao painel administrativo.</p>
</div>

<?php if(!empty($msg)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if(!empty($msg_erro)): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm"><?= htmlspecialchars($msg_erro) ?></div>
<?php endif; ?>

<div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 mb-8">
    <h3 class="font-display font-bold text-lg mb-4"><?= $edit_login ? 'Editar Login' : 'Novo Login de Acesso' ?></h3>
    <form action="logins.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input type="hidden" name="id_login" value="<?= (isset($edit_login['log_codigo']) ? $edit_login['log_codigo'] : '') ?>">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Nome do Usuário</label>
            <input type="text" name="log_nome" required value="<?= (isset($edit_login['log_nome']) ? htmlspecialchars($edit_login['log_nome']) : '') ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="Ex: Administrador">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Usuário / Login</label>
            <input type="text" name="log_login" required value="<?= (isset($edit_login['log_login']) ? htmlspecialchars($edit_login['log_login']) : '') ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="Ex: adm">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Senha <?= $edit_login ? '(Deixe em branco para manter)' : '' ?></label>
            <input type="password" name="log_senha" <?= $edit_login ? '' : 'required' ?> class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="••••••••">
        </div>
        <div class="md:col-span-3 flex items-center gap-3 mt-2">
            <button type="submit" class="bg-[#b7f052] text-[#0f1710] font-bold py-2.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider"><?= $edit_login ? 'Atualizar Login' : 'Criar Login' ?></button>
            <?php if($edit_login): ?><a href="logins.php" class="bg-white/10 text-slate-300 font-bold py-2.5 px-6 rounded-xl hover:bg-white/25 transition-all text-xs uppercase tracking-wider">Cancelar</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-[#121c14] border border-white/10 rounded-2xl overflow-hidden">
    <div class="p-6 border-b border-white/10"><h3 class="font-display font-bold text-lg">Logins Registrados</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-white/10 bg-black/20 text-xs uppercase tracking-wider text-slate-400">
                    <th class="p-4">ID</th>
                    <th class="p-4">Nome</th>
                    <th class="p-4">Usuário / Login</th>
                    <th class="p-4">Data de Cadastro</th>
                    <th class="p-4 text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-sm">
                <?php while($row = mysqli_fetch_assoc($logins)): ?>
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="p-4 font-mono text-xs text-[#b7f052]">#<?= $row['log_codigo'] ?></td>
                    <td class="p-4 font-bold text-white"><?= htmlspecialchars($row['log_nome']) ?></td>
                    <td class="p-4 font-mono text-emerald-400"><?= htmlspecialchars($row['log_login']) ?></td>
                    <td class="p-4 text-slate-300"><?= date('d/m/Y H:i', strtotime($row['log_data_cadastro'])) ?></td>
                    <td class="p-4 text-center space-x-2">
                        <a href="logins.php?acao=editar&id=<?= $row['log_codigo'] ?>" class="text-blue-400 hover:underline text-xs">Editar</a>
                        <a href="logins.php?acao=excluir&id=<?= $row['log_codigo'] ?>" class="text-red-400 hover:underline text-xs" data-confirm-msg="Tem certeza que deseja excluir este login?">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("footer.php"); ?>
