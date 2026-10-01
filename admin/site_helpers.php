<?php
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) { http_response_code(404); exit; }
require_once __DIR__.'/site_config.php';
require_once __DIR__.'/site_uploads.php';
require_once __DIR__.'/../includes/site_content.php';

function site_admin_write($con,$sql,$params=[]) {
    if (!ui_query($con,$sql,$params)) throw new RuntimeException('Falha ao gravar conteúdo público: '.mysqli_error($con));
}
function site_admin_id($value, $allowNew=false) {
    if ($allowNew && $value==='') return 0;
    if (!is_string($value) || !ctype_digit($value) || (int)$value<1 || (int)$value>4294967295) throw new AdminFormValidation('Registro inválido. Atualize a página e tente novamente.');
    return (int)$value;
}
function site_admin_validate($config,$input) {
    $data=[]; $errors=[];
    foreach ($config['fields'] as $name=>$field) {
        $value=trim($input[$name] ?? '');
        if (!mb_check_encoding($value,'UTF-8') || str_contains($value,"\0")) $errors[$name]='Informe um texto válido.';
        elseif ($field[2] && $value==='') $errors[$name]='Preencha este campo.';
        elseif (mb_strlen($value,'UTF-8')>$field[3]) $errors[$name]='Use no máximo '.$field[3].' caracteres.';
        elseif ($name==='icone' && !isset($config['icons'][$value])) $errors[$name]='Selecione um ícone válido.';
        $data[$name]=$value;
    }
    $order=$input['ordem'] ?? '';
    if (!ctype_digit($order) || (int)$order>9999) $errors['ordem']='Informe uma ordem de 0 a 9999.';
    $data['ordem']=(int)$order;
    $active=$input['ativo'] ?? '';
    if (!in_array($active,['0','1'],true)) $errors['ativo']='Escolha ativo ou inativo.';
    $data['ativo']=(int)$active;
    return [$data,$errors];
}
function site_admin_remove_unused($con,$path) {
    if (!$path) return true;
    $total=0;
    foreach (['tb_site_servicos','tb_site_projetos'] as $table) $total+=(int)ui_rows($con,'SELECT COUNT(*) AS total FROM '.$table.' WHERE imagem_path=?',[$path])[0]['total'];
    return $total ? true : site_upload_delete($path);
}
function site_admin_mutate($con,$config,$action,$id,$data=[],$image=null) {
    $newPath=null; $oldPath=null; $cleanup=false;
    mysqli_begin_transaction($con);
    try {
        // Serialize ordering and image replacement, including simultaneous saves.
        $rows=ui_rows($con,'SELECT * FROM '.$config['table'].' ORDER BY ordem,id FOR UPDATE');
        $index=null;
        foreach ($rows as $position=>$row) if ((int)$row['id']===$id) { $index=$position; break; }
        if ($id && $index===null) throw new AdminFormValidation('Registro não encontrado. Ele pode ter sido excluído em outra aba.');
        $record=$index===null ? null : $rows[$index];
        if ($action==='salvar') {
            $newPath=site_upload_store($image);
            $data['imagem_path']=$newPath ?? ($record['imagem_path'] ?? null);
            if ($newPath && $record) { $oldPath=$record['imagem_path']; $cleanup=true; }
            $columns=array_keys($data);
            if ($id) site_admin_write($con,'UPDATE '.$config['table'].' SET '.implode(',',array_map(fn($column)=>$column.'=?',$columns)).' WHERE id=?',[...array_values($data),$id]);
            else site_admin_write($con,'INSERT INTO '.$config['table'].' ('.implode(',',$columns).') VALUES ('.implode(',',array_fill(0,count($columns),'?')).')',array_values($data));
        } elseif ($action==='excluir') {
            $oldPath=$record['imagem_path']; $cleanup=true;
            site_admin_write($con,'DELETE FROM '.$config['table'].' WHERE id=?',[$id]);
        } elseif ($action==='alternar') {
            site_admin_write($con,'UPDATE '.$config['table'].' SET ativo=? WHERE id=?',[(int)!$record['ativo'],$id]);
        } else {
            $neighbor=$index+($action==='subir' ? -1 : 1);
            if (isset($rows[$neighbor])) [$rows[$index],$rows[$neighbor]]=[$rows[$neighbor],$rows[$index]];
            foreach ($rows as $position=>$row) site_admin_write($con,'UPDATE '.$config['table'].' SET ordem=? WHERE id=?',[$position+1,(int)$row['id']]);
        }
        if (!mysqli_commit($con)) throw new RuntimeException('Falha ao confirmar a gravação.');
    } catch (Throwable $error) {
        mysqli_rollback($con);
        if ($newPath) site_upload_delete($newPath);
        throw $error;
    }
    if ($cleanup) {
        try {
            if (!site_admin_remove_unused($con,$oldPath)) throw new RuntimeException('Arquivo antigo não pôde ser removido.');
        } catch (Throwable $error) {
            error_log('Golden Jardim: limpeza de imagem: '.$error->getMessage());
            return 'A alteração foi salva, mas a imagem antiga não pôde ser removida. Avise o administrador.';
        }
    }
    return '';
}
