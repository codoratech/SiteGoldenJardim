<?php
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) { require_once __DIR__.'/bootstrap.php'; require_login(); exigirPermissao('conteudo_publico'); http_response_code(404); exit; }
require_once __DIR__.'/ui_helpers.php';
require_once __DIR__.'/../includes/site_defaults.php';

function site_section_fields() {
    return ['selo'=>['Selo',80,'Texto pequeno acima do título. Deixe vazio para ocultá-lo.'], 'titulo'=>['Título',160,'Título principal da seção, acima dos cards.'], 'subtitulo'=>['Subtítulo',500,'Frase de apresentação junto ao título da seção.']];
}

function site_section_defaults($key) {
    $defaults=site_defaults()['sections'][$key]; $values=[];
    foreach (['pt','en'] as $language) {
        foreach (['selo'=>'eyebrow','titulo'=>'title','subtitulo'=>'subtitle'] as $field=>$property) $values[$field.'_'.$language]=$defaults[$language][$property];
    }
    return $values;
}

function site_section_validate($input) {
    $values=[]; $errors=[];
    foreach (site_section_fields() as $field=>$definition) {
        foreach (['pt','en'] as $language) {
            $name=$field.'_'.$language; $value=trim($input[$name] ?? '');
            if (!mb_check_encoding($value,'UTF-8') || str_contains($value,"\0")) $errors[$name]='Informe um texto válido.';
            elseif ($name==='titulo_pt' && $value==='') $errors[$name]='Preencha o título em português.';
            elseif (mb_strlen($value,'UTF-8')>$definition[1]) $errors[$name]='Use no máximo '.$definition[1].' caracteres.';
            $values[$name]=$value;
        }
    }
    return [$values,$errors];
}

function site_section_save($con,$key,$values) {
    exigirPermissao('conteudo_publico');
    $columns=array_keys($values);
    $sql='INSERT INTO tb_site_secoes (chave,'.implode(',',$columns).') VALUES ('.implode(',',array_fill(0,count($columns)+1,'?')).') ON DUPLICATE KEY UPDATE '.implode(',',array_map(fn($column)=>$column.'=VALUES('.$column.')',$columns));
    if (!ui_query($con,$sql,[$key,...array_values($values)])) throw new RuntimeException('Falha ao salvar os textos da seção.');
}
