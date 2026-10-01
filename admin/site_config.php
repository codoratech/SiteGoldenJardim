<?php
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) { http_response_code(404); exit; }
function site_admin_config($kind) {
    $common = ['titulo_pt'=>['Título em português','text',true,120], 'titulo_en'=>['Título em inglês','text',false,120], 'descricao_pt'=>['Descrição em português','textarea',false,600], 'descricao_en'=>['Descrição em inglês','textarea',false,600]];
    $configs = [
        'services'=>['table'=>'tb_site_servicos','route'=>'site_servicos.php','label'=>'Serviços do site','singular'=>'serviço','section'=>'services','icon'=>'leaf','fields'=>$common + ['icone'=>['Ícone','select',true,24]],'icons'=>['sprout'=>'🌱 Broto','droplet'=>'💧 Gota','pruning'=>'✂️ Poda','tree'=>'🌳 Árvore','building'=>'🏢 Comercial','compass'=>'🧭 Consultoria']],
        'projects'=>['table'=>'tb_site_projetos','route'=>'site_projetos.php','label'=>'Projetos','singular'=>'projeto','section'=>'gallery','icon'=>'image','fields'=>$common + ['categoria_pt'=>['Categoria em português','text',false,80], 'categoria_en'=>['Categoria em inglês','text',false,80], 'local'=>['Local','text',false,180], 'imagem_alt_pt'=>['Texto alternativo em português','text',false,180], 'imagem_alt_en'=>['Texto alternativo em inglês','text',false,180]]],
    ];
    $config = $configs[$kind] ?? null;
    if (!$config) throw new InvalidArgumentException('Seção inválida.');
    if ($kind === 'projects') { $config['fields']['titulo_pt'][3]=160; $config['fields']['titulo_en'][3]=160; }
    return $config;
}
