<?php
require_once __DIR__ . '/site_defaults.php';
require_once __DIR__ . '/../conexao/connection.php';

function site_escape($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function site_image_path($value) {
    if (!is_string($value)) return null;
    // Only local images from the original assets or this module's upload directory.
    return preg_match('~\A(?:assets/[a-zA-Z0-9_-]+|uploads/site/[a-f0-9]{32})\.(?:jpg|jpeg|png|webp)\z~D', $value) ? $value : null;
}

function site_icon($key) {
    $icons = ['sprout'=>'🌱','droplet'=>'💧','pruning'=>'✂️','tree'=>'🌳','building'=>'🏢','compass'=>'🧭'];
    return $icons[$key] ?? $icons['sprout'];
}

function site_normalize($data) {
    foreach ($data['sections'] as &$section) {
        foreach ($section['pt'] as $key=>$value) {
            if (trim((string) ($section['en'][$key] ?? '')) === '') $section['en'][$key] = $value;
        }
    }
    unset($section);
    foreach (['services','projects'] as $group) {
        foreach ($data[$group] as &$item) {
            $item['img'] = site_image_path($item['img']);
            foreach ($item['pt'] as $key=>$value) {
                if (trim((string) ($item['en'][$key] ?? '')) === '') $item['en'][$key] = $value;
            }
            if ($group === 'services') $item['icon'] = site_icon($item['icon']);
            else {
                foreach (['pt','en'] as $language) {
                    if (trim($item[$language]['alt']) === '') $item[$language]['alt'] = $item[$language]['title'];
                }
            }
        }
        unset($item);
    }
    return $data;
}

function site_query_rows($connection, $sql) {
    $statement = mysqli_prepare($connection, $sql);
    if (!$statement) throw new RuntimeException('Falha ao preparar conteúdo público.');
    try {
        if (!mysqli_stmt_execute($statement)) throw new RuntimeException('Falha ao consultar conteúdo público.');
        $result = mysqli_stmt_get_result($statement);
        if (!$result) throw new RuntimeException('Falha ao ler conteúdo público.');
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_free_result($result);
        return $rows;
    } finally { mysqli_stmt_close($statement); }
}

function site_content_load($connection = null) {
    $defaults = site_defaults();
    $owned = $connection === null;
    if ($owned) $connection = gj_open_connection();
    if (!$connection) return site_normalize($defaults);
    try {
        $data = $defaults;
        $sections = site_query_rows($connection, 'SELECT chave,titulo_pt,titulo_en,subtitulo_pt,subtitulo_en,selo_pt,selo_en FROM tb_site_secoes');
        foreach ($sections as $row) {
            if (!isset($data['sections'][$row['chave']])) continue;
            foreach (['pt','en'] as $language) {
                $data['sections'][$row['chave']][$language] = ['title'=>$row['titulo_'.$language], 'subtitle'=>$row['subtitulo_'.$language], 'eyebrow'=>$row['selo_'.$language]];
            }
        }
        $rows = site_query_rows($connection, 'SELECT titulo_pt,titulo_en,descricao_pt,descricao_en,imagem_path,icone,ativo FROM tb_site_servicos ORDER BY ordem,id');
        // An empty table uses defaults; deliberately inactive cards stay hidden.
        if ($rows) {
            $data['services'] = [];
            foreach ($rows as $row) {
                if ((int) $row['ativo'] !== 1) continue;
                $data['services'][] = ['icon'=>$row['icone'],'img'=>$row['imagem_path'], 'pt'=>[$row['titulo_pt'],$row['descricao_pt']], 'en'=>[$row['titulo_en'],$row['descricao_en']]];
            }
        }
        $rows = site_query_rows($connection, 'SELECT titulo_pt,titulo_en,descricao_pt,descricao_en,categoria_pt,categoria_en,local,imagem_path,imagem_alt_pt,imagem_alt_en,ativo FROM tb_site_projetos ORDER BY ordem,id');
        if ($rows) {
            $data['projects'] = [];
            foreach ($rows as $row) {
                if ((int) $row['ativo'] !== 1) continue;
                $item = ['img'=>$row['imagem_path']];
                foreach (['pt','en'] as $language) {
                    $item[$language] = ['title'=>$row['titulo_'.$language], 'description'=>$row['descricao_'.$language], 'category'=>$row['categoria_'.$language], 'location'=>$row['local'], 'alt'=>$row['imagem_alt_'.$language]];
                }
                $data['projects'][] = $item;
            }
        }
        return site_normalize($data);
    } catch (Throwable $error) {
        error_log('Golden Jardim: conteúdo público indisponível. '.$error->getMessage());
        return site_normalize($defaults);
    } finally { if ($owned) mysqli_close($connection); }
}
