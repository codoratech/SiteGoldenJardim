<?php
include("../conexao/banco.php");

$msg = "";
$msg_erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo = mysqli_real_escape_string($con, $_POST['Est_Tipo']);
    $qtd = intval($_POST['Est_Quantidade']);
    
    if (empty($tipo)) {
        $msg_erro = "O tipo / item de estoque é obrigatório.";
    } else {
        if (mysqli_query($con, "INSERT INTO TB_Estoque (Est_Tipo, Est_Quantidade) VALUES ('$tipo', $qtd)")) {
            header("Location: cadastrar_estoque.php?ok=insert");
            exit();
        } else {
            $msg_erro = "Erro ao registrar: " . mysqli_error($con);
        }
    }
}

if (isset($_GET['ok']) && $_GET['ok'] == 'insert') {
    $msg = "Movimentação de estoque registrada com sucesso!";
}

include("header.php");
?>

<div class="mb-8">
    <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-1">Cadastrar Movimentação de Estoque</h1>
    <p class="text-slate-400 text-sm">Registre entradas ou saídas de itens no estoque da Golden Jardim.</p>
</div>

<?php if(!empty($msg)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if(!empty($msg_erro)): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm"><?= htmlspecialchars($msg_erro) ?></div>
<?php endif; ?>

<div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 mb-8 max-w-3xl">
    <h3 class="font-display font-bold text-lg mb-4">Informações da Movimentação</h3>
    <form action="cadastrar_estoque.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Tipo de Movimentação / Item</label>
            <input type="text" name="Est_Tipo" required class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="Ex: Entrada de Mudas">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Quantidade</label>
            <input type="number" name="Est_Quantidade" required class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="0">
        </div>
        <div class="md:col-span-2 flex items-center gap-3 pt-2">
            <button type="submit" class="bg-[#b7f052] text-[#0f1710] font-bold py-2.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider">Salvar Registro</button>
            <a href="estoque.php" class="bg-white/10 text-slate-300 font-bold py-2.5 px-6 rounded-xl hover:bg-white/25 transition-all text-xs uppercase tracking-wider">Estoque Existente</a>
        </div>
    </form>
</div>

<?php include("footer.php"); ?>
