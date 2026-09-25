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
    <style>
        #sidebar {
            transition: width 0.3s ease, transform 0.3s ease;
        }
        #sidebar.collapsed {
            width: 5rem !important;
        }
        #sidebar.collapsed .sidebar-text,
        #sidebar.collapsed .dropdown-arrow,
        #sidebar.collapsed .sidebar-subtitle,
        #sidebar.collapsed [id$="Submenu"] {
            display: none !important;
        }
        #sidebar.collapsed nav a,
        #sidebar.collapsed nav button,
        #sidebar.collapsed .border-t a {
            justify-content: center !important;
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }
    </style>
</head>
<body class="bg-[#0b120d] dark:bg-[#0b120d] text-slate-100 font-sans min-h-screen flex selection:bg-[#b7f052] selection:text-[#0f1710]">

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-[#121c14] border-r border-white/10 flex flex-col lg:translate-x-0 -translate-x-full">
        <div class="p-6 flex items-center justify-between border-b border-white/10">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-[#b7f052] text-[#0f1710] flex items-center justify-center font-bold shrink-0">🌿</span>
                <div class="sidebar-text">
                    <span class="font-display font-bold text-sm tracking-tight uppercase block">Golden Jardim</span>
                    <span class="text-[10px] text-slate-400 sidebar-subtitle">Admin v2.0</span>
                </div>
            </div>
            <button id="closeSidebar" class="lg:hidden text-slate-400 hover:text-white">✕</button>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors <?= $current_page == 'dashboard.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                <span>📊</span> <span class="sidebar-text">Dashboard</span>
            </a>

            <!-- Clientes -->
            <?php $is_clientes_page = ($current_page == 'cadastrar_cliente.php' || $current_page == 'clientes.php'); ?>
            <div class="space-y-1">
                <button type="button" id="clientesDropdownBtn" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-colors <?= $is_clientes_page ? 'bg-white/10 text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                    <span class="flex items-center gap-3"><span>👥</span> <span class="sidebar-text">Clientes</span></span>
                    <span id="clientesArrow" class="transform transition-transform text-xs dropdown-arrow <?= $is_clientes_page ? 'rotate-180' : '' ?>">▼</span>
                </button>
                <div id="clientesSubmenu" class="pl-4 space-y-1 pt-1 <?= $is_clientes_page ? '' : 'hidden' ?>">
                    <a href="cadastrar_cliente.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition-colors <?= $current_page == 'cadastrar_cliente.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                        <span>➕</span> <span class="sidebar-text">Cadastrar Cliente</span>
                    </a>
                    <a href="clientes.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition-colors <?= $current_page == 'clientes.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                        <span>📋</span> <span class="sidebar-text">Clientes Existentes</span>
                    </a>
                </div>
            </div>

            <!-- Produtos -->
            <?php $is_produtos_page = ($current_page == 'cadastrar_produto.php' || $current_page == 'produtos.php'); ?>
            <div class="space-y-1">
                <button type="button" id="produtosDropdownBtn" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-colors <?= $is_produtos_page ? 'bg-white/10 text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                    <span class="flex items-center gap-3"><span>📦</span> <span class="sidebar-text">Produtos</span></span>
                    <span id="produtosArrow" class="transform transition-transform text-xs dropdown-arrow <?= $is_produtos_page ? 'rotate-180' : '' ?>">▼</span>
                </button>
                <div id="produtosSubmenu" class="pl-4 space-y-1 pt-1 <?= $is_produtos_page ? '' : 'hidden' ?>">
                    <a href="cadastrar_produto.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition-colors <?= $current_page == 'cadastrar_produto.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                        <span>➕</span> <span class="sidebar-text">Cadastrar Produto</span>
                    </a>
                    <a href="produtos.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition-colors <?= $current_page == 'produtos.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                        <span>📋</span> <span class="sidebar-text">Produtos Existentes</span>
                    </a>
                </div>
            </div>

            <!-- Serviços -->
            <?php $is_servicos_page = ($current_page == 'cadastrar_servico.php' || $current_page == 'servicos.php'); ?>
            <div class="space-y-1">
                <button type="button" id="servicosDropdownBtn" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-colors <?= $is_servicos_page ? 'bg-white/10 text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                    <span class="flex items-center gap-3"><span>🌱</span> <span class="sidebar-text">Serviços</span></span>
                    <span id="servicosArrow" class="transform transition-transform text-xs dropdown-arrow <?= $is_servicos_page ? 'rotate-180' : '' ?>">▼</span>
                </button>
                <div id="servicosSubmenu" class="pl-4 space-y-1 pt-1 <?= $is_servicos_page ? '' : 'hidden' ?>">
                    <a href="cadastrar_servico.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition-colors <?= $current_page == 'cadastrar_servico.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                        <span>➕</span> <span class="sidebar-text">Cadastrar Serviço</span>
                    </a>
                    <a href="servicos.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition-colors <?= $current_page == 'servicos.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                        <span>📋</span> <span class="sidebar-text">Serviços Existentes</span>
                    </a>
                </div>
            </div>

            <!-- Fornecedores -->
            <?php $is_fornecedores_page = ($current_page == 'cadastrar_fornecedor.php' || $current_page == 'fornecedores.php'); ?>
            <div class="space-y-1">
                <button type="button" id="fornecedoresDropdownBtn" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-colors <?= $is_fornecedores_page ? 'bg-white/10 text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                    <span class="flex items-center gap-3"><span>🏭</span> <span class="sidebar-text">Fornecedores</span></span>
                    <span id="fornecedoresArrow" class="transform transition-transform text-xs dropdown-arrow <?= $is_fornecedores_page ? 'rotate-180' : '' ?>">▼</span>
                </button>
                <div id="fornecedoresSubmenu" class="pl-4 space-y-1 pt-1 <?= $is_fornecedores_page ? '' : 'hidden' ?>">
                    <a href="cadastrar_fornecedor.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition-colors <?= $current_page == 'cadastrar_fornecedor.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                        <span>➕</span> <span class="sidebar-text">Cadastrar Fornecedor</span>
                    </a>
                    <a href="fornecedores.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition-colors <?= $current_page == 'fornecedores.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                        <span>📋</span> <span class="sidebar-text">Fornecedores Existentes</span>
                    </a>
                </div>
            </div>

            <!-- Ordens de Serviço -->
            <?php $is_ordens_page = ($current_page == 'cadastrar_ordem.php' || $current_page == 'ordens.php'); ?>
            <div class="space-y-1">
                <button type="button" id="ordensDropdownBtn" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-colors <?= $is_ordens_page ? 'bg-white/10 text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                    <span class="flex items-center gap-3"><span>📋</span> <span class="sidebar-text">Ordens de Serviço</span></span>
                    <span id="ordensArrow" class="transform transition-transform text-xs dropdown-arrow <?= $is_ordens_page ? 'rotate-180' : '' ?>">▼</span>
                </button>
                <div id="ordensSubmenu" class="pl-4 space-y-1 pt-1 <?= $is_ordens_page ? '' : 'hidden' ?>">
                    <a href="cadastrar_ordem.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition-colors <?= $current_page == 'cadastrar_ordem.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                        <span>➕</span> <span class="sidebar-text">Cadastrar Ordem</span>
                    </a>
                    <a href="ordens.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition-colors <?= $current_page == 'ordens.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                        <span>📋</span> <span class="sidebar-text">Ordens Existentes</span>
                    </a>
                </div>
            </div>

            <!-- Agendamentos -->
            <?php $is_agendamentos_page = ($current_page == 'cadastrar_agendamento.php' || $current_page == 'agendamentos.php'); ?>
            <div class="space-y-1">
                <button type="button" id="agendamentosDropdownBtn" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-colors <?= $is_agendamentos_page ? 'bg-white/10 text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                    <span class="flex items-center gap-3"><span>📅</span> <span class="sidebar-text">Agendamentos</span></span>
                    <span id="agendamentosArrow" class="transform transition-transform text-xs dropdown-arrow <?= $is_agendamentos_page ? 'rotate-180' : '' ?>">▼</span>
                </button>
                <div id="agendamentosSubmenu" class="pl-4 space-y-1 pt-1 <?= $is_agendamentos_page ? '' : 'hidden' ?>">
                    <a href="cadastrar_agendamento.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition-colors <?= $current_page == 'cadastrar_agendamento.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                        <span>➕</span> <span class="sidebar-text">Cadastrar Agendamento</span>
                    </a>
                    <a href="agendamentos.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition-colors <?= $current_page == 'agendamentos.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                        <span>📋</span> <span class="sidebar-text">Agendamentos Existentes</span>
                    </a>
                </div>
            </div>

            <!-- Estoque -->
            <?php $is_estoque_page = ($current_page == 'cadastrar_estoque.php' || $current_page == 'estoque.php'); ?>
            <div class="space-y-1">
                <button type="button" id="estoqueDropdownBtn" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-colors <?= $is_estoque_page ? 'bg-white/10 text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                    <span class="flex items-center gap-3"><span>📦</span> <span class="sidebar-text">Estoque</span></span>
                    <span id="estoqueArrow" class="transform transition-transform text-xs dropdown-arrow <?= $is_estoque_page ? 'rotate-180' : '' ?>">▼</span>
                </button>
                <div id="estoqueSubmenu" class="pl-4 space-y-1 pt-1 <?= $is_estoque_page ? '' : 'hidden' ?>">
                    <a href="cadastrar_estoque.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition-colors <?= $current_page == 'cadastrar_estoque.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                        <span>➕</span> <span class="sidebar-text">Cadastrar Movimentação</span>
                    </a>
                    <a href="estoque.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition-colors <?= $current_page == 'estoque.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                        <span>📋</span> <span class="sidebar-text">Estoque Existente</span>
                    </a>
                </div>
            </div>

            <!-- Contas a Receber / Financeiro -->
            <?php $is_financeiro_page = ($current_page == 'cadastrar_conta.php' || $current_page == 'financeiro.php'); ?>
            <div class="space-y-1">
                <button type="button" id="financeiroDropdownBtn" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-colors <?= $is_financeiro_page ? 'bg-white/10 text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                    <span class="flex items-center gap-3"><span>💰</span> <span class="sidebar-text">Contas a Receber</span></span>
                    <span id="financeiroArrow" class="transform transition-transform text-xs dropdown-arrow <?= $is_financeiro_page ? 'rotate-180' : '' ?>">▼</span>
                </button>
                <div id="financeiroSubmenu" class="pl-4 space-y-1 pt-1 <?= $is_financeiro_page ? '' : 'hidden' ?>">
                    <a href="cadastrar_conta.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition-colors <?= $current_page == 'cadastrar_conta.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                        <span>➕</span> <span class="sidebar-text">Cadastrar Conta</span>
                    </a>
                    <a href="financeiro.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition-colors <?= $current_page == 'financeiro.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                        <span>📋</span> <span class="sidebar-text">Contas Existentes</span>
                    </a>
                </div>
            </div>

            <!-- Logins -->
            <?php $is_logins_page = ($current_page == 'cadastrar_login.php' || $current_page == 'logins.php'); ?>
            <div class="space-y-1">
                <button type="button" id="loginsDropdownBtn" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-colors <?= $is_logins_page ? 'bg-white/10 text-white font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                    <span class="flex items-center gap-3"><span>🔑</span> <span class="sidebar-text">Logins</span></span>
                    <span id="loginsArrow" class="transform transition-transform text-xs dropdown-arrow <?= $is_logins_page ? 'rotate-180' : '' ?>">▼</span>
                </button>
                <div id="loginsSubmenu" class="pl-4 space-y-1 pt-1 <?= $is_logins_page ? '' : 'hidden' ?>">
                    <a href="cadastrar_login.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition-colors <?= $current_page == 'cadastrar_login.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                        <span>➕</span> <span class="sidebar-text">Cadastrar Login</span>
                    </a>
                    <a href="logins.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-medium transition-colors <?= $current_page == 'logins.php' ? 'bg-[#b7f052] text-[#0f1710] font-bold shadow-lg shadow-[#b7f052]/20' : 'text-slate-300 hover:bg-white/5 hover:text-white' ?>">
                        <span>📋</span> <span class="sidebar-text">Logins Existentes</span>
                    </a>
                </div>
            </div>
        </nav>

        <div class="p-4 border-t border-white/10">
            <a href="logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-400 hover:bg-red-500/10 transition-colors">
                <span>🚪</span> <span class="sidebar-text">Sair do Sistema</span>
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
