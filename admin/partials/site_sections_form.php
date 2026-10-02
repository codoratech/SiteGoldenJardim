<?php require_once dirname(__DIR__).'/bootstrap.php'; require_login(); exigirPermissao('conteudo_publico'); if (!isset($section_values,$section_labels)) { http_response_code(404); exit; } ?>
<div class="manage-page site-manage-page">
 <div class="page-heading"><div><p class="eyebrow">GOLDEN JARDIM · SITE</p><h1>Textos das seções<span>.</span></h1><p>Edite as apresentações de Serviços e Projetos em português e inglês.</p></div><a class="secondary-action" href="../index.php#services" target="_blank" rel="noopener">Ver no site <?= admin_icon('arrow') ?></a></div>
 <nav class="page-breadcrumb" aria-label="Caminho da página"><a href="dashboard.php">Painel</a><span>/</span><span>Site</span><span>/</span><span>Textos das seções</span><span>/</span><span aria-current="page">Editar</span></nav>
 <?php if ($site_flash): ?><div class="feedback feedback-success site-toast" role="status"><?= admin_icon('check') ?><div><strong><?= ui_escape($site_flash['text']) ?></strong><a href="<?= ui_escape('../index.php#'.($site_flash['section'] ?? 'services')) ?>" target="_blank" rel="noopener">Ver no site <?= admin_icon('arrow') ?></a></div><button class="icon-button" type="button" data-close-toast aria-label="Fechar aviso"><?= admin_icon('close') ?></button></div><?php endif; ?>
 <?php if ($section_error): ?><div class="feedback feedback-error" role="alert"><?= admin_icon('alert') ?><div><strong>Confira a alteração.</strong><p><?= ui_escape($section_error) ?></p></div></div><?php endif; ?>
 <p class="field-help site-language-help">Campos em inglês vazios usam o texto correspondente em português. Se houver falha no banco, o site exibe o conteúdo padrão.</p>
 <?php if ($section_ready): foreach ($section_labels as $key=>$label): ?>
 <section class="manage-card site-sections-card" id="<?= ui_escape($key) ?>" aria-labelledby="<?= ui_escape($key) ?>-heading">
  <div class="card-heading"><div><h2 id="<?= ui_escape($key) ?>-heading"><?= ui_escape($label) ?></h2><p><?= $key==='services' ? 'Apresentação acima dos cards de serviços.' : 'Apresentação acima da galeria de projetos.' ?></p></div><a class="secondary-action" href="<?= ui_escape('../index.php#'.$key) ?>" target="_blank" rel="noopener">Ver no site <?= admin_icon('arrow') ?></a></div>
  <form class="manage-form" method="POST" action="site_textos.php" data-site-form novalidate aria-label="<?= ui_escape('Textos de '.$label) ?>">
   <?= csrf_field() ?><input type="hidden" name="acao" value="salvar"><input type="hidden" name="secao" value="<?= ui_escape($key) ?>">
   <fieldset><legend>Apresentação da seção</legend><div class="form-grid">
   <?php foreach (site_section_fields() as $field=>$definition): foreach (['pt'=>'português','en'=>'inglês'] as $language=>$language_label): $name=$field.'_'.$language; $id=$key.'-'.$name; $error=$section_errors[$key][$name] ?? ''; ?>
    <div class="form-field"><div class="site-section-label"><label for="<?= ui_escape($id) ?>"><?= ui_escape($definition[0].' em '.$language_label) ?><?= $name==='titulo_pt' ? ' <span class="required-mark">*</span>' : '' ?></label><span class="site-field-placement"><?= $field==='selo' ? 'Acima do título' : ($field==='titulo' ? 'Destaque da seção' : 'Ao lado ou abaixo do título') ?></span></div>
     <textarea id="<?= ui_escape($id) ?>" name="<?= ui_escape($name) ?>" rows="<?= $field==='subtitulo' ? '3' : '2' ?>" maxlength="<?= (int)$definition[1] ?>" <?= $name==='titulo_pt' ? 'required' : '' ?> data-char-count aria-invalid="<?= $error ? 'true' : 'false' ?>" aria-describedby="<?= ui_escape($id) ?>-help <?= ui_escape($id) ?>-error"><?= ui_escape($section_values[$key][$name] ?? '') ?></textarea>
     <div class="site-field-meta"><small id="<?= ui_escape($id) ?>-count" class="field-help"></small></div><p class="field-help" id="<?= ui_escape($id) ?>-help"><?= ui_escape($definition[2]) ?><?= $language==='en' ? ' Vazio: usa português.' : '' ?></p><p class="field-error" id="<?= ui_escape($id) ?>-error" aria-live="polite"><?= ui_escape($error) ?></p>
    </div>
   <?php endforeach; endforeach; ?>
   </div></fieldset>
   <div class="form-actions"><button class="primary-action" type="submit"><?= admin_icon('check') ?>Salvar textos de <?= ui_escape($label) ?></button><a class="text-action" href="site_textos.php">Descartar alterações</a></div>
  </form>
 </section>
 <?php endforeach; endif; ?>
</div>
