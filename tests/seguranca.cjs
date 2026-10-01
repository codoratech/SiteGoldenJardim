const { spawnSync } = require('node:child_process');
const path = require('node:path');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const sessionPath = path.join(require('node:os').tmpdir(), 'goldenjardim-security-tests');
fs.mkdirSync(sessionPath, {recursive: true});
const php = process.env.PHP_BINARY || 'C:/xampp/php/php.exe';
const bootstrap = path.resolve(__dirname, '../admin/bootstrap.php').replace(/\\/g, '/');
const cases = [
  ['token ausente', {}, false],
  ['token incorreto', {csrf_token: 'invalid'}, false],
  ['token como array', {csrf_token: ['test-token']}, false],
  ['requisição válida', {csrf_token: 'test-token'}, true],
  ['valor negativo', {csrf_token: 'test-token', Con_Valor: '-1'}, false],
  ['valor inválido', {csrf_token: 'test-token', Con_Valor: 'abc'}, false],
  ['valor zero', {csrf_token: 'test-token', Con_Valor: '0'}, true],
  ['quantidade fracionária', {csrf_token: 'test-token', Est_Quantidade: '1.5'}, false],
  ['identificador inválido', {csrf_token: 'test-token', id_cliente: '1abc'}, false],
  ['e-mail inválido', {csrf_token: 'test-token', cli_Email: 'abc'}, false],
  ['data inexistente', {csrf_token: 'test-token', Con_DataVencimento: '2026-02-30'}, false],
  ['agendamento inválido', {csrf_token: 'test-token', Age_DataAgendada: '2026-02-30T10:00'}, false],
  ['agendamento válido', {csrf_token: 'test-token', Age_DataAgendada: '2026-09-30T10:00'}, true],
  ['status desconhecido', {csrf_token: 'test-token', Con_Status: 'Outro'}, false],
];
for (const [name, post, accepted] of cases) {
  const encoded = Buffer.from(JSON.stringify(post)).toString('base64');
  const code = `<?php
    session_start();
    $_SESSION['csrf_token'] = 'test-token';
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = json_decode(base64_decode('${encoded}'), true);
    require '${bootstrap}';
    echo 'REQUEST_ACCEPTED';
  `;
  const result = spawnSync(php, ['-d', 'session.save_path="' + sessionPath.replace(/\\/g, '/') + '"'], {input: code, encoding: 'utf8'});
  if (result.error) throw result.error;
  assert.equal(result.status, 0, result.stderr);
  assert.equal(result.stderr, '', 'Avisos inesperados: ' + name);
  assert.equal(result.stdout.includes('REQUEST_ACCEPTED'), accepted, name);
}
console.log(`${cases.length} casos de segurança e validação aprovados.`);
