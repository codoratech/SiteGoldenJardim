<?php
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) { require_once __DIR__.'/bootstrap.php'; require_login(); exigirPermissao('conteudo_publico'); http_response_code(404); exit; }
class SiteImageValidation extends RuntimeException {}

function site_upload_inspect($file) {
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (!is_int($file['error'] ?? null) || $file['error'] !== UPLOAD_ERR_OK) {
        throw new SiteImageValidation(in_array($file['error'] ?? null, [UPLOAD_ERR_INI_SIZE,UPLOAD_ERR_FORM_SIZE], true) ? 'A imagem deve ter no máximo 5 MB.' : 'Não foi possível receber a imagem. Selecione o arquivo novamente.');
    }
    $path = $file['tmp_name'] ?? null;
    if (!is_string($path) || !is_uploaded_file($path)) throw new SiteImageValidation('Upload inválido. Selecione a imagem novamente.');
    $size = filesize($path);
    if (!$size || $size > 5 * 1024 * 1024) throw new SiteImageValidation('A imagem deve ter no máximo 5 MB.');
    $allowed = ['image/jpeg'=>IMAGETYPE_JPEG,'image/png'=>IMAGETYPE_PNG,'image/webp'=>IMAGETYPE_WEBP];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($path);
    $info = @getimagesize($path);
    if (!isset($allowed[$mime]) || !$info || $info[2] !== $allowed[$mime]) throw new SiteImageValidation('Envie uma imagem JPG, PNG ou WebP válida.');
    [$width,$height] = $info;
    $limit = ini_get('memory_limit');
    $bytes = (int) $limit;
    if (preg_match('/([KMG])$/i', $limit, $match)) $bytes *= 1024 ** (strpos('KMG',strtoupper($match[1])) + 1);
    $scale = min(1,1600/max(1,$width,$height));
    $estimated = $width * $height * 8 + max(1,(int)round($width*$scale)) * max(1,(int)round($height*$scale)) * 8 + 24 * 1024 * 1024;
    if ($width < 1 || $height < 1 || $width * $height > 24000000 || ($bytes > 0 && $estimated > $bytes - memory_get_usage(true))) {
        throw new SiteImageValidation('A resolução dessa imagem é muito alta. Exporte uma versão menor, de preferência com até 1600 px no maior lado.');
    }
    return compact('path','mime','width','height');
}

function site_upload_store($image) {
    exigirPermissao('conteudo_publico');
    if (!$image) return null;
    $directory = dirname(__DIR__).'/uploads/site';
    if (!is_dir($directory) && !mkdir($directory,0755,true)) throw new RuntimeException('Não foi possível preparar a pasta de imagens.');
    if (!is_file($directory.'/.htaccess')) throw new RuntimeException('A proteção da pasta de imagens precisa estar instalada.');
    $source = $target = null;
    $path = null;
    try {
        $source = match ($image['mime']) {
            'image/jpeg'=>@imagecreatefromjpeg($image['path']),
            'image/png'=>@imagecreatefrompng($image['path']),
            'image/webp'=>@imagecreatefromwebp($image['path']),
        };
        if (!$source) throw new SiteImageValidation('Não foi possível decodificar a imagem. Envie outro arquivo JPG, PNG ou WebP.');
        $scale = min(1,1600/max($image['width'],$image['height']));
        $width = max(1,(int)round($image['width']*$scale));
        $height = max(1,(int)round($image['height']*$scale));
        $target = imagecreatetruecolor($width,$height);
        imagealphablending($target,false);
        imagesavealpha($target,true);
        imagefill($target,0,0,imagecolorallocatealpha($target,0,0,0,127));
        if (!imagecopyresampled($target,$source,0,0,0,0,$width,$height,$image['width'],$image['height'])) throw new RuntimeException('Falha ao redimensionar a imagem.');
        $extension = function_exists('imagewebp') ? 'webp' : ($image['mime']==='image/png' ? 'png' : 'jpg');
        $name = bin2hex(random_bytes(16)).'.'.$extension;
        $path = $directory.'/'.$name;
        $saved = match ($extension) { 'webp'=>imagewebp($target,$path,82), 'png'=>imagepng($target,$path,6), 'jpg'=>imagejpeg($target,$path,82) };
        if (!$saved || !is_file($path) || !filesize($path) || filesize($path)>5*1024*1024) throw new RuntimeException('Falha ao salvar a imagem otimizada.');
        return 'uploads/site/'.$name;
    } catch (Throwable $error) {
        if ($path && is_file($path)) @unlink($path);
        throw $error;
    } finally {
        // PHP 8.4 releases GD objects when references are dropped.
        unset($target,$source);
    }
}

function site_upload_delete($path) {
    exigirPermissao('conteudo_publico');
    if (!is_string($path) || !preg_match('~\Auploads/site/[a-f0-9]{32}\.(?:jpg|png|webp)\z~D',$path)) return true;
    $directory = realpath(dirname(__DIR__).'/uploads/site');
    $absolute = dirname(__DIR__).'/'.$path;
    if (!file_exists($absolute)) return true;
    if (!$directory || is_link($absolute) || dirname(realpath($absolute) ?: '') !== $directory) return false;
    return unlink($absolute);
}
