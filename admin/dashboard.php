<?php
include("header.php");
include("../conexao/banco.php");

// Contagens para os cards
$q_clientes = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM TB_Clientes"))['total'];
$q_produtos = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM TB_Produtos"))['total'];
$q_servicos = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM TB_Servicos"))['total'];
$q_os = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM TB_OrdensServico"))['total'];
$q_agendamentos = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total FROM TB_Agendamentos"))['total'];
$total_receber_row = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(Con_Valor) as total FROM TB_ContasReceber WHERE Con_Status = 'Pendente'"));
$total_receber = isset($total_receber_row['total']) ? $total_receber_row['total'] : 0;
?>

<div class="mb-8">
    <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-2">Visão Geral</h1>
    <p class="text-slate-400 text-sm">Bem-vindo ao centro de controle e gestão do Golden Jardim.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
    <div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 relative overflow-hidden group hover:border-[#b7f052]/50 transition-colors">
        <div class="flex items-center justify-between mb-4">
            <span class="text-2xl">👥</span>
            <span class="text-xs font-mono text-[#b7f052] bg-[#b7f052]/10 px-2.5 py-1 rounded-full">Ativos</span>
        </div>
        <h3 class="font-display font-bold text-3xl mb-1"><?= $q_clientes ?></h3>
        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Total de Clientes</p>
    </div>

    <div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 relative overflow-hidden group hover:border-[#b7f052]/50 transition-colors">
        <div class="flex items-center justify-between mb-4">
            <span class="text-2xl">📋</span>
            <span class="text-xs font-mono text-amber-400 bg-amber-400/10 px-2.5 py-1 rounded-full">Ordens</span>
        </div>
        <h3 class="font-display font-bold text-3xl mb-1"><?= $q_os ?></h3>
        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Ordens de Serviço</p>
    </div>

    <div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 relative overflow-hidden group hover:border-[#b7f052]/50 transition-colors">
        <div class="flex items-center justify-between mb-4">
            <span class="text-2xl">💰</span>
            <span class="text-xs font-mono text-emerald-400 bg-emerald-400/10 px-2.5 py-1 rounded-full">A Receber</span>
        </div>
        <h3 class="font-display font-bold text-3xl mb-1">R$ <?= number_format($total_receber, 2, ',', '.') ?></h3>
        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Contas Pendentes</p>
    </div>

    <div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 relative overflow-hidden group hover:border-[#b7f052]/50 transition-colors">
        <div class="flex items-center justify-between mb-4">
            <span class="text-2xl">🌱</span>
            <span class="text-xs font-mono text-blue-400 bg-blue-400/10 px-2.5 py-1 rounded-full">Catálogo</span>
        </div>
        <h3 class="font-display font-bold text-3xl mb-1"><?= $q_servicos ?></h3>
        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Serviços Cadastrados</p>
    </div>

    <div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 relative overflow-hidden group hover:border-[#b7f052]/50 transition-colors">
        <div class="flex items-center justify-between mb-4">
            <span class="text-2xl">📦</span>
            <span class="text-xs font-mono text-purple-400 bg-purple-400/10 px-2.5 py-1 rounded-full">Produtos</span>
        </div>
        <h3 class="font-display font-bold text-3xl mb-1"><?= $q_produtos ?></h3>
        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Produtos no Sistema</p>
    </div>

    <div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 relative overflow-hidden group hover:border-[#b7f052]/50 transition-colors">
        <div class="flex items-center justify-between mb-4">
            <span class="text-2xl">📅</span>
            <span class="text-xs font-mono text-teal-400 bg-teal-400/10 px-2.5 py-1 rounded-full">Agenda</span>
        </div>
        <h3 class="font-display font-bold text-3xl mb-1"><?= $q_agendamentos ?></h3>
        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Agendamentos</p>
    </div>
</div>

<!-- Quick Links / Recent -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-[#121c14] border border-white/10 rounded-2xl p-6">
        <h3 class="font-display font-bold text-lg mb-4">Ações Rápidas</h3>
        <div class="grid grid-cols-2 gap-4">
            <a href="clientes.php" class="p-4 rounded-xl bg-white/5 hover:bg-[#b7f052]/10 border border-white/10 hover:border-[#b7f052]/30 transition-all text-center block">
                <span class="text-2xl block mb-2">👤</span>
                <span class="text-xs font-bold uppercase tracking-wider">Novo Cliente</span>
            </a>
            <a href="ordens.php" class="p-4 rounded-xl bg-white/5 hover:bg-[#b7f052]/10 border border-white/10 hover:border-[#b7f052]/30 transition-all text-center block">
                <span class="text-2xl block mb-2">📋</span>
                <span class="text-xs font-bold uppercase tracking-wider">Nova Ordem OS</span>
            </a>
            <a href="agendamentos.php" class="p-4 rounded-xl bg-white/5 hover:bg-[#b7f052]/10 border border-white/10 hover:border-[#b7f052]/30 transition-all text-center block">
                <span class="text-2xl block mb-2">📅</span>
                <span class="text-xs font-bold uppercase tracking-wider">Agendar Serviço</span>
            </a>
            <a href="financeiro.php" class="p-4 rounded-xl bg-white/5 hover:bg-[#b7f052]/10 border border-white/10 hover:border-[#b7f052]/30 transition-all text-center block">
                <span class="text-2xl block mb-2">💰</span>
                <span class="text-xs font-bold uppercase tracking-wider">Contas a Receber</span>
            </a>
        </div>
    </div>

    <div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 flex flex-col justify-between">
        <div>
            <h3 class="font-display font-bold text-lg mb-2">Golden Jardim • Excelência Verde</h3>
            <p class="text-sm text-slate-400 leading-relaxed mb-4">Sistema integrado para gerenciamento completo de paisagismo, horticultura, ordens de serviço, estoque e controle financeiro.</p>
        </div>
        <div class="p-4 rounded-xl bg-[#b7f052]/10 border border-[#b7f052]/20 text-[#b7f052] text-xs font-mono">
            Status do Sistema: Operacional 🟢 v2.0
        </div>
    </div>
</div>

<?php include("footer.php"); ?>
