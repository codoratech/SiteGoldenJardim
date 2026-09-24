<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['log_codigo'])) {
    header("Location: index.php");
    exit();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo — Golden Jardim</title>
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
<body class="bg-[#0b120d] dark:bg-[#0b120d] text-slate-100 font-sans min-h-screen flex selection:bg-[#b7f052] selection:text-[#0f1710]">

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-[#121c14] border-r border-white/10 flex flex-col transition-transform duration-300 lg:translate-x-0 -translate-x-full">
        <div class="p-6 flex items-center justify-between border-b border-white/10">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-[#b7f052] text-[#0f1710] flex items-center justify-center font-bold">🌿</span>
                <div>
                    <span class="font-display font-bold text-sm tracking-tight uppercase block">Golden Jardim</span>
                    <span class="text-[10px] text-slate-400">Admin v2.0</span>
                </div>
            </div>
            <button id="closeSidebar" class="lg:hidden text-slate-400 hover:text-white">✕</button>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors <?= $current_page == 'dashboard.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                <span>📊</span> Dashboard
            </a>
            <a href="clientes.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors <?= $current_page == 'clientes.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                <span>👥</span> Clientes
            </a>
            <a href="produtos.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors <?= $current_page == 'produtos.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                <span>📦</span> Produtos
            </a>
            <a href="servicos.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors <?= $current_page == 'servicos.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                <span>🌱</span> Serviços
            </a>
            <a href="fornecedores.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors <?= $current_page == 'fornecedores.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                <span>🏭</span> Fornecedores
            </a>
            <a href="ordens.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors <?= $current_page == 'ordens.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                <span>📋</span> Ordens de Serviço
            </a>
            <a href="agendamentos.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors <?= $current_page == 'agendamentos.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                <span>📅</span> Agendamentos
            </a>
            <a href="estoque.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors <?= $current_page == 'estoque.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                <span>📦</span> Estoque
            </a>
            <a href="financeiro.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors <?= $current_page == 'financeiro.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                <span>💰</span> Contas a Receber
            </a>
        </nav>

        <div class="p-4 border-t border-white/10">
            <a href="logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-400 hover:bg-red-500/10 transition-colors">
                <span>🚪</span> Sair do Sistema
            </a>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div id="mainWrapper" class="flex-1 flex flex-col lg:pl-64 transition-all duration-300">
        <!-- Topbar -->
        <header class="h-20 bg-[#121c14]/90 backdrop-blur-xl border-b border-white/10 sticky top-0 z-40 px-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button id="openSidebar" class="text-slate-300 hover:text-white text-xl">☰</button>
                <h2 class="font-display font-bold text-lg tracking-tight">Painel de Controle</h2>
            </div>
            <div class="flex items-center gap-4">
                <!-- Theme toggle -->
                <button type="button" id="themeToggle" class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-white/10 transition-colors" aria-label="Alternar tema">
                    <span id="sunIcon" class="text-sm">☀️</span>
                    <span id="moonIcon" class="text-sm hidden">🌙</span>
                </button>
                <div class="hidden sm:flex items-center gap-3 pl-4 border-l border-white/10">
                    <div class="w-9 h-9 rounded-full bg-[#b7f052]/20 border border-[#b7f052]/30 text-[#b7f052] flex items-center justify-center font-bold text-sm">
                        <?= substr($_SESSION['log_nome'], 0, 1) ?>
                    </div>
                    <div>
                        <span class="text-xs font-bold block text-slate-200"><?= htmlspecialchars($_SESSION['log_nome']) ?></span>
                        <span class="text-[10px] text-slate-400">Administrador</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-1 p-6 md:p-8 max-w-7xl w-full mx-auto">
