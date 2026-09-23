<?php
include("../conexao/banco.php");

$msg = "";
$msg_erro = "";

if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if (!mysqli_query($con, "DELETE FROM TB_Estoque WHERE ID_Estoque = $id")) {
        $msg_erro = "Não foi possível excluir: este registro possui dependências. Detalhe: " . mysqli_error($con);
    } else {
        $msg = "Registro excluído com sucesso!";
    }

    if (empty($msg_erro)) {
        header("Location: estoque.php?ok=1");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo = mysqli_real_escape_string($con, $_POST['Est_Tipo']);
    $qtd = intval($_POST['Est_Quantidade']);
    
    if (isset($_POST['id_estoque']) && !empty($_POST['id_estoque'])) {
        $id = intval($_POST['id_estoque']);
        if (mysqli_query($con, "UPDATE TB_Estoque SET Est_Tipo='$tipo', Est_Quantidade=$qtd WHERE ID_Estoque=$id")) {
            header("Location: estoque.php?ok=update");
            exit();
        } else {
            $msg_erro = "Erro ao atualizar: " . mysqli_error($con);
        }
    } else {
        if (mysqli_query($con, "INSERT INTO TB_Estoque (Est_Tipo, Est_Quantidade) VALUES ('$tipo', $qtd)")) {
            header("Location: estoque.php?ok=insert");
            exit();
        } else {
            $msg_erro = "Erro ao registrar: " . mysqli_error($con);
        }
    }
}

if (isset($_GET['ok'])) {
    if ($_GET['ok'] == 'insert') $msg = "Movimentação de estoque registrada!";
    if ($_GET['ok'] == 'update') $msg = "Estoque atualizado com sucesso!";
    if ($_GET['ok'] == '1')      $msg = "Registro excluído com sucesso!";
}

$edit_estoque = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = mysqli_query($con, "SELECT * FROM TB_Estoque WHERE ID_Estoque = $id");
    $edit_estoque = mysqli_fetch_assoc($res);
}

$estoque = mysqli_query($con, "SELECT * FROM TB_Estoque ORDER BY ID_Estoque DESC");

include("header.php");
?>

<div class="mb-8">
    <h1 class="font-display font-bold text-2xl md:text-3xl tracking-tight mb-1">Controle de Estoque</h1>
    <p class="text-slate-400 text-sm">Gerenciamento de estoque de plantas, adubos, ferramentas e insumos.</p>
</div>

<?php if(!empty($msg)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if(!empty($msg_erro)): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm"><?= htmlspecialchars($msg_erro) ?></div>
<?php endif; ?>

<div class="bg-[#121c14] border border-white/10 rounded-2xl p-6 mb-8">
    <h3 class="font-display font-bold text-lg mb-4"><?= $edit_estoque ? 'Editar Movimentação' : 'Nova Movimentação de Estoque' ?></h3>
    <form action="estoque.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="hidden" name="id_estoque" value="<?= (isset($edit_estoque['ID_Estoque']) ? $edit_estoque['ID_Estoque'] : '') ?>">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Tipo de Movimentação / Item</label>
            <input type="text" name="Est_Tipo" required value="<?= (isset($edit_estoque['Est_Tipo']) ? htmlspecialchars($edit_estoque['Est_Tipo']) : '') ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="Ex: Entrada de Mudas">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Quantidade</label>
            <input type="number" name="Est_Quantidade" required value="<?= (isset($edit_estoque['Est_Quantidade']) ? $edit_estoque['Est_Quantidade'] : '') ?>" class="w-full bg-black/30 border border-white/10 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#b7f052]" placeholder="0">
        </div>
        <div class="md:col-span-2 flex items-center gap-3 mt-2">
            <button type="submit" class="bg-[#b7f052] text-[#0f1710] font-bold py-2.5 px-6 rounded-xl hover:bg-[#9cd438] transition-all text-xs uppercase tracking-wider"><?= $edit_estoque ? 'Atualizar' : 'Salvar Registro' ?></button>
            <?php if($edit_estoque): ?><a href="estoque.php" class="bg-white/10 text-slate-300 font-bold py-2.5 px-6 rounded-xl hover:bg-white/25 transition-all text-xs uppercase tracking-wider">Cancelar</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-[#121c14] border border-white/10 rounded-2xl overflow-hidden">
    <div class="p-6 border-b border-white/10"><h3 class="font-display font-bold text-lg">Registros de Estoque</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-white/10 bg-black/20 text-xs uppercase tracking-wider text-slate-400">
                    <th class="p-4">ID</th>
                    <th class="p-4">Tipo / Item</th>
                    <th class="p-4">Quantidade</th>
                    <th class="p-4">Data da Movimentação</th>
                    <th class="p-4 text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5 text-sm">
                <?php while($row = mysqli_fetch_assoc($estoque)): ?>
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="p-4 font-mono text-xs text-[#b7f052]">#<?= $row['ID_Estoque'] ?></td>
                    <td class="p-4 font-bold text-white"><?= htmlspecialchars($row['Est_Tipo']) ?></td>
                    <td class="p-4 font-mono <?= $row['Est_Quantidade'] >= 0 ? 'text-emerald-400' : 'text-red-400' ?>"><?= $row['Est_Quantidade'] ?></td>
                    <td class="p-4 text-slate-300"><?= date('d/m/Y H:i', strtotime($row['Est_DataMovimentacao'])) ?></td>
                    <td class="p-4 text-center space-x-2">
                        <a href="estoque.php?acao=editar&id=<?= $row['ID_Estoque'] ?>" class="text-blue-400 hover:underline text-xs">Editar</a>
                        <a href="estoque.php?acao=excluir&id=<?= $row['ID_Estoque'] ?>" onclick="return confirm('Excluir registro?');" class="text-red-400 hover:underline text-xs">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("footer.php"); ?>
