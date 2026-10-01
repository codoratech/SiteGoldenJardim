<?php if (!isset($entity,$key)) { http_response_code(404); exit; } ?>
<section class="manage-card form-card">
 <div class="card-heading"><div><h2><?= $editing ? 'Editar '.$entity['singular'] : 'Novo cadastro' ?></h2><p>Os campos com <span class="required-mark">*</span> são obrigatórios.</p></div><?= admin_icon($entity['icon']) ?></div>
 <form method="POST" action="<?= ui_escape($isCreate ? $entity['create'] : $key.'.php') ?>" class="manage-form" data-managed-form novalidate>
 <?= csrf_field() ?><?php if ($editing): ?><input type="hidden" name="<?= ui_escape($entity['edit']) ?>" value="<?= ui_escape($record[$entity['pk']]) ?>"><?php endif; ?>
 <?php $sections = []; foreach ($entity['fields'] as $field) $sections[$field[4]][] = $field; foreach ($sections as $section=>$fields): ?>
 <fieldset><legend><?= ui_escape($section) ?></legend><div class="form-grid">
 <?php foreach ($fields as $field): [$name,$label,$type,$required] = $field; $value=ui_value($name,$record); $id='field-'.$name; if ($name==='log_senha' && $editing) $required=false; $readonly=$key==='logins' && $name==='log_login' && strcasecmp(trim($record['log_login'] ?? ''),'Adm')===0; ?>
 <div class="form-field <?= $type==='textarea' || strpos($name,'Endereco')!==false ? 'field-wide' : '' ?>"><label for="<?= ui_escape($id) ?>"><?= ui_escape($label) ?><?php if ($required): ?> <span class="required-mark" aria-hidden="true">*</span><?php endif; ?></label>
 <?php $fieldError=ui_field_error($field,$value,$required,$error); $attributes=' id="'.ui_escape($id).'" name="'.ui_escape($name).'" aria-describedby="'.ui_escape($id).'-error'.(!empty($field[6]) ? ' '.ui_escape($id).'-help' : '').'" aria-invalid="'.($fieldError ? 'true' : 'false').'"'.($required ? ' required' : '').($readonly ? ' readonly' : ''); ?>
 <?php if ($type==='select'): $options=$field[5]; ?>
 <select<?= $attributes ?>><?php if (is_string($options)): ?><option value=""><?= $required ? 'Selecione um cliente...' : 'Sem vínculo' ?></option><?php $choices=$options==='clients' ? ui_rows($con,'SELECT ID_Cliente AS id, cli_Nome AS label FROM tb_clientes ORDER BY cli_Nome') : ui_rows($con,'SELECT ID_Produto AS id, Pro_Nome AS label FROM tb_produtos ORDER BY Pro_Nome'); foreach ($choices as $choice): ?><option value="<?= ui_escape($choice['id']) ?>" <?= (string)$value===(string)$choice['id'] ? 'selected' : '' ?>><?= ui_escape($choice['label']) ?></option><?php endforeach; ?>
 <?php else: if ($key==='clientes') $value=ui_client_type($value); if ($value!=='' && !in_array($value,$options,true)) array_unshift($options,$value); foreach ($options as $option): ?><option value="<?= ui_escape($option) ?>" <?= (string)$value===(string)$option ? 'selected' : '' ?>><?= ui_escape($option==='Saida' ? 'Saída' : $option) ?></option><?php endforeach; endif; ?></select>
 <?php elseif ($type==='textarea'): ?><textarea<?= $attributes ?> rows="3" placeholder="<?= ui_escape($field[5] ?? 'Informações complementares') ?>"><?= ui_escape($value) ?></textarea>
 <?php else: if ($type==='datetime-local' && $value && strtotime($value)) $value=date('Y-m-d\TH:i',strtotime($value)); ?>
 <input<?= $attributes ?> type="<?= ui_escape($type==='money' ? 'text' : $type) ?>" value="<?= ui_escape($value) ?>" placeholder="<?= ui_escape($field[5] ?? '') ?>" <?= $type==='money' ? 'inputmode="decimal" data-money' : '' ?> <?= $type==='number' ? 'min="'.(strpos($name,'_ID_')!==false ? '1' : '0').'" max="2147483647" step="1" inputmode="numeric"' : '' ?> <?= $type==='password' ? 'minlength="8" autocomplete="new-password"' : '' ?> <?= $name==='log_nome' ? 'maxlength="120"' : ($name==='log_login' ? 'maxlength="50"' : '') ?>>
 <?php endif; ?>
 <?php if (!empty($field[6])): ?><p class="field-help" id="<?= ui_escape($id) ?>-help"><?= ui_escape($field[6]) ?></p><?php endif; ?><p class="field-error" id="<?= ui_escape($id) ?>-error" aria-live="polite"><?= ui_escape($fieldError) ?></p>
 </div><?php endforeach; ?></div></fieldset><?php endforeach; ?>
 <div class="form-actions"><button class="primary-action" type="submit"><?= admin_icon('check') ?><?= $editing ? 'Salvar alterações' : 'Salvar cadastro' ?></button><?php if ($isCreate): ?><a class="secondary-action" href="<?= ui_escape($key.'.php') ?>">Existentes</a><?php endif; ?><a class="text-action" href="<?= ui_escape($key.'.php') ?>">Cancelar</a></div>
 </form>
</section>
