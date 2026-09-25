<?php
include("../conexao/banco.php");

$msg = "";
$msg_erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = mysqli_real_escape_string($con, $_POST['For_Nome']);
    $telefone = mysqli_real_escape_string($con, $_POST['For_Telefone']);
    $endereco = mysqli_real_escape_string($con, $_POST['For_Endereco']);
    
    if (empty($nome)) {
        $msg_erro = "O nome do fornecedor é obrigatório.";
    } else {
        if (mysqli_query($con, "INSERT INTO TB_Fornecedores (For_Nome, For_Telefone, For_Endereco) VALUES ('$nome', '$telefone', '$endereco')")) {
            header("Location: cadastrar_fornecedor.php?ok=insert");
            exit();
        } else {
            $msg_erro = "Erro ao cadastrar: " . mysqli_error($con);
        }
    }
}

if (isset($_GET['ok']) && $_GET['ok'] == 'insert') {
    $msg = "Fornecedor cadastrado com sucesso!";
}

include("header.php");
?>

<div class="mb-8">
    <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-1">Cadastrar Novo Fornecedor</h1>
    <p class="text-slate-400 text-sm">Adicione um novo fornecedor de mudas, insumos ou equipamentos.</p>
</div>

<?php if(!empty($msg)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if(!empty($msg_erro)): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm"><?= htmlspecialchars($msg_erro) ?></div>
<?php endif; ?>

<div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 mb-8 max-w-3xl">
    <h3 class="font-display font-bold text-lg mb-4">Informações do Fornecedor</h3>
    <form action="cadastrar_fornecedor.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Nome do Fornecedor</label>
            <input type="text" name="For_Nome" required class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="Ex: Viveiro Verde Vida">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Telefone</label>
            <input type="text" name="For_Telefone" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="(19) 99999-9999">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Endereço</label>
            <input type="text" name="For_Endereco" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="Endereço do fornecedor">
        </div>
        <div class="md:col-span-2 flex items-center gap-3 pt-2">
            <button type="submit" class="bg-[#b7f052] text-[#0f1710] font-bold py-2.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider">Salvar Fornecedor</button>
            <a href="fornecedores.php" class="bg-white/10 text-slate-300 font-bold py-2.5 px-6 rounded-xl hover:bg-white/25 transition-all text-xs uppercase tracking-wider">Fornecedores Existentes</a>
        </div>
    </form>
</div>

<?php include("footer.php"); ?>
