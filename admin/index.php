<?php
session_start();
include("../conexao/banco.php");

$erro = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = mysqli_real_escape_string($con, $_POST['login']);
    $senha = mysqli_real_escape_string($con, $_POST['senha']);
    $senha_md5 = md5($senha);

    $sql = "SELECT * FROM tb_login WHERE log_login = '$login' AND log_senha = '$senha_md5'";
    $resultado = mysqli_query($con, $sql);

    if (mysqli_num_rows($resultado) == 1) {
        $usuario = mysqli_fetch_assoc($resultado);
        $_SESSION['log_codigo'] = $usuario['log_codigo'];
        $_SESSION['log_nome'] = $usuario['log_nome'];
        $_SESSION['log_login'] = $usuario['log_login'];
        header("Location: dashboard.php");
        exit();
    } else {
        $erro = "Usuário ou senha incorretos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Painel Administrativo Golden Jardim</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;800&family=Inter:wght@300;400;500;600&display=swap">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            colors: {
              forest: '#0f1710',
              pine: '#162418',
              moss: '#b7f052',
              'moss-dark': '#9cd438',
              mist: '#f3f7f4'
            },
            fontFamily: {
              sans: ['Inter', 'sans-serif'],
              display: ['Sora', 'sans-serif']
            }
          }
        }
      }
    </script>
</head>
<body class="bg-[#0b120d] text-slate-100 font-sans min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
    <!-- Ambient glow -->
    <div class="absolute w-[500px] h-[500px] bg-[#b7f052]/10 rounded-full blur-[120px] -top-32 -left-32 pointer-events-none"></div>
    <div class="absolute w-[400px] h-[400px] bg-[#b7f052]/5 rounded-full blur-[100px] -bottom-32 -right-32 pointer-events-none"></div>

    <div class="w-full max-w-md bg-[#121c14]/80 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl relative z-10">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-[#b7f052] text-[#0f1710] flex items-center justify-center font-bold text-lg">🌿</span>
                <div>
                    <h1 class="font-display font-bold text-lg tracking-tight uppercase">Golden Jardim</h1>
                    <p class="text-xs text-slate-400">Painel Administrativo</p>
                </div>
            </div>
            <!-- Theme toggle button -->
            <button type="button" id="themeToggle" class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-white/10 transition-colors" aria-label="Alternar tema">
                <span id="sunIcon" class="text-sm">☀️</span>
                <span id="moonIcon" class="text-sm hidden">🌙</span>
            </button>
        </div>

        <?php if (!empty($erro)): ?>
            <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm flex items-center gap-2">
                <span>⚠️</span> <?= $erro ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-5">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Usuário / Login</label>
                <input type="text" name="login" required placeholder="Ex: admin" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-[#b7f052] transition-colors">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Senha</label>
                <input type="password" name="senha" required placeholder="••••••••" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-[#b7f052] transition-colors">
            </div>
            <button type="submit" class="w-full bg-[#b7f052] text-[#0f1710] font-bold py-3.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all transform hover:scale-[1.01] active:scale-[0.99] shadow-lg shadow-[#b7f052]/20 flex items-center justify-center gap-2 text-sm uppercase tracking-wider">
                Entrar no Painel →
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="../index.html" class="text-xs text-slate-400 hover:text-[#b7f052] transition-colors">← Voltar para o site principal</a>
        </div>
    </div>

    <script>
        const html = document.documentElement;
        const themeToggle = document.getElementById('themeToggle');
        const sunIcon = document.getElementById('sunIcon');
        const moonIcon = document.getElementById('moonIcon');

        themeToggle.addEventListener('click', () => {
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
                localStorage.setItem('admin-theme', 'light');
            } else {
                html.classList.add('dark');
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
                localStorage.setItem('admin-theme', 'dark');
            }
        });

        if (localStorage.getItem('admin-theme') === 'light') {
            html.classList.remove('dark');
            sunIcon.classList.add('hidden');
            moonIcon.classList.remove('hidden');
        }
    </script>
</body>
</html>
