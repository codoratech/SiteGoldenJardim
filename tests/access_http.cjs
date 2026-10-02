// Integration checks in an isolated local database, using the real PHP routes.
const fs=require('node:fs'),path=require('node:path'),assert=require('node:assert/strict'),{spawnSync}=require('node:child_process');
const project=path.resolve(__dirname,'..'),php='C:/xampp/php/php.exe',cgi='C:/xampp/php/php-cgi.exe';
function runPHP(source){const r=spawnSync(php,[],{input:'<?php '+source,cwd:project,encoding:'utf8',timeout:15000});assert.equal(r.status,0,r.stderr);assert(!r.stderr,r.stderr);return r.stdout;}
function query(sql){return JSON.parse(runPHP("$c=new mysqli('127.0.0.1','root','','gj_roles_tests',3310);$c->set_charset('utf8mb4');$r=$c->query(base64_decode('"+Buffer.from(sql).toString('base64')+"'));echo json_encode($r instanceof mysqli_result?$r->fetch_all(MYSQLI_ASSOC):[]);"));}
runPHP("$c=new mysqli('127.0.0.1','root','','',3310);$c->query('CREATE DATABASE IF NOT EXISTS gj_roles_tests CHARACTER SET utf8mb4');$c->select_db('gj_roles_tests');$c->set_charset('utf8mb4');function sql_run($c,$s){$c->multi_query($s);do{if($r=$c->store_result())$r->free();}while($c->more_results()&&$c->next_result());}if(!$c->query(\"SHOW TABLES LIKE 'tb_login'\")->num_rows){$s=file_get_contents('.deploy/infinityfree-2026-09-30/banco-escola.sql');$s=preg_replace('/^INSERT INTO .*;\\R/m','',$s);sql_run($c,$s);sql_run($c,file_get_contents('migrations/002_dashboard_estoque.sql'));sql_run($c,file_get_contents('migrations/005_perfis_acesso.sql'));sql_run($c,file_get_contents('migrations/003_site_conteudo.sql'));} sql_run($c,file_get_contents('migrations/006_permissoes_conteudo_publico.sql')); $c->query(\"DELETE FROM tb_login WHERE log_login LIKE 'roles_test_%'\");$h=password_hash('LocalRoles-2026!',PASSWORD_DEFAULT);$s=$c->prepare('INSERT INTO tb_login(log_nome,log_login,log_senha,log_perfil) VALUES (?,?,?,?)');foreach(['Administrador','Consulta','Operacional','Editor do site'] as $role){$n='Perfil teste '.$role;$l='roles_test_'.strtolower($role);$s->bind_param('ssss',$n,$l,$h,$role);$s->execute();}");
class Client {
 constructor(){this.cookie='';this.token='';}
 req(page,data){const [filename,qs='']=page.split('?'),input=data?.raw??(data?new URLSearchParams(data).toString():'');const r=spawnSync(cgi,['-d','extension=gd','-d','upload_tmp_dir='+path.join(project,'.local-tests/upload-tmp'),'-d','cgi.force_redirect=0','-d','session.save_path='+path.join(project,'.local-tests/sessions')],{cwd:project,env:{...process.env,REDIRECT_STATUS:'1',REQUEST_METHOD:data?'POST':'GET',SCRIPT_FILENAME:path.join(project,'admin',filename).replace(/\\/g,'/'),SCRIPT_NAME:'/admin/'+filename,QUERY_STRING:qs,HTTP_COOKIE:this.cookie,CONTENT_TYPE:data?.contentType||'application/x-www-form-urlencoded',CONTENT_LENGTH:String(Buffer.byteLength(input)),GJ_DB_HOST:'127.0.0.1',GJ_DB_USER:'root',GJ_DB_PASSWORD:'',GJ_DB_NAME:'gj_roles_tests',GJ_DB_PORT:'3310'},input,encoding:'utf8',timeout:10000});assert.equal(r.status,0,r.stderr);assert(!r.stderr,r.stderr);const [head,...parts]=r.stdout.split('\r\n\r\n'),body=parts.join('\r\n\r\n'),headers=Object.fromEntries(head.split('\r\n').map(l=>{const i=l.indexOf(':');return [l.slice(0,i).toLowerCase(),l.slice(i+1).trim()]}));if(headers['set-cookie'])this.cookie=headers['set-cookie'].split(';')[0];const token=body.match(/name="csrf_token" value="([^"]+)"/);if(token)this.token=token[1];assert(!/Fatal error|<b>Warning/.test(body));return {status:Number((headers.status||'200').split(' ')[0]),body,headers};}
 login(role){this.req('index.php');assert.equal(this.req('index.php',{csrf_token:this.token,login:'roles_test_'+role.toLowerCase(),senha:'LocalRoles-2026!'}).status,302);assert.equal(this.req('dashboard.php').status,200);}
 post(page,data){return this.req(page,{csrf_token:this.token,...data});}
 upload(page,data){const boundary='GoldenJardimPermissionsBoundary';const parts=[];for(const [name,value] of Object.entries({csrf_token:this.token,...data}))parts.push(Buffer.from('--'+boundary+'\r\nContent-Disposition: form-data; name="'+name+'"\r\n\r\n'+value+'\r\n'));parts.push(Buffer.from('--'+boundary+'\r\nContent-Disposition: form-data; name="imagem"; filename="permissions.png"\r\nContent-Type: image/png\r\n\r\n'),fs.readFileSync(path.join(project,'.local-tests/permissions-test.png')),Buffer.from('\r\n--'+boundary+'--\r\n'));return this.req(page,{contentType:'multipart/form-data; boundary='+boundary,raw:Buffer.concat(parts)});}
}
let checks=0;
try {
 const admin=new Client();admin.login('Administrador');
 let r=admin.req('cadastrar_login.php');assert.equal(r.status,200);assert(r.body.includes('name="log_perfil"'));for(const role of ['Consulta','Operacional','Administrador','Editor do site'])assert(r.body.includes('value="'+role+'"'));checks+=4;
 const createdLogin='roles_test_created';
 assert.equal(admin.post('cadastrar_login.php',{log_nome:'Acesso criado',log_login:createdLogin,log_senha:'LocalRoles-2026!',log_perfil:'Consulta'}).status,302);
 let created=query("SELECT * FROM tb_login WHERE log_login='"+createdLogin+"'")[0];assert.equal(created.log_perfil,'Consulta');checks++;
 assert.equal(admin.post('cadastrar_login.php',{log_nome:'Inválido',log_login:'roles_test_invalid',log_senha:'LocalRoles-2026!',log_perfil:'SuperAdmin'}).status,400);assert.equal(query("SELECT * FROM tb_login WHERE log_login='roles_test_invalid'").length,0);checks++;
 for(const role of ['Consulta','Operacional','Editor do site']) {
  const c=new Client();c.login(role);
  for(const route of ['clientes','produtos','servicos','fornecedores','ordens','agendamentos','estoque','financeiro']) {
   r=c.req(route+'.php');assert.equal(r.status,200,role+' '+route);assert.equal(r.body.includes('CONTEÚDO PÚBLICO'),role==='Editor do site');assert(!r.body.includes('Gerenciar acessos'));
   if(role!=='Operacional'){assert(!r.body.includes('data-delete-form'));assert(!r.body.includes('title="Editar registro"'));assert.equal(c.req(route+'.php?acao=editar&id=1').status,403);assert.equal(c.post(route+'.php',{acao:'excluir',id:'1'}).status,403);} checks++;
  }
  for(const route of ['logins.php','cadastrar_login.php']){assert.equal(c.req(route).status,403);assert.equal(c.post(route,{acao:'excluir',id:'1'}).status,403);checks++;} for(const route of ['site_textos.php','site_projetos.php','site_servicos.php']){const denied=c.req(route);assert.equal(denied.status,role==='Editor do site'?200:302);if(role!=='Editor do site'){assert.equal(denied.headers.location,'dashboard.php');const dashboard=c.req('dashboard.php');assert(dashboard.body.includes('Você não tem permissão para acessar esta área'));assert(!c.req('dashboard.php').body.includes('Você não tem permissão para acessar esta área'));assert.equal(c.post(route,{acao:'excluir',id:'1'}).status,403);}checks++;}
  for(const create of ['cliente','produto','servico','fornecedor','ordem','agendamento','estoque','conta']){assert.equal(c.req('cadastrar_'+create+'.php').status,role==='Operacional'?200:403);checks++;}
  r=c.req('perfil.php?id='+created.log_codigo);assert.equal(r.status,200);assert(r.body.includes('Perfil teste '+role));assert(!r.body.includes('Acesso criado'));checks++;
  assert.equal(c.req('perfil.php',{csrf_token:'invalid',senha_atual:'a',nova_senha:'b',confirmacao:'b'}).status,403);checks++;
  if(role==='Operacional'){
   const name='roles_test_client';assert.equal(c.post('cadastrar_cliente.php',{cli_Nome:name,cli_Tipo:'Residencial',cli_Telefone:'',cli_Email:'',cli_Endereco:''}).status,302);
   const client=query("SELECT ID_Cliente FROM tb_clientes WHERE cli_Nome='"+name+"'")[0];assert(client);assert.equal(c.post('clientes.php',{id_cliente:String(client.ID_Cliente),cli_Nome:name,cli_Tipo:'Comercial',cli_Telefone:'',cli_Email:'',cli_Endereco:''}).status,302);assert.equal(query('SELECT cli_Tipo FROM tb_clientes WHERE ID_Cliente='+client.ID_Cliente)[0].cli_Tipo,'Comercial');assert.equal(c.post('clientes.php',{acao:'excluir',id:String(client.ID_Cliente)}).status,302);checks+=3;
  }
 }
 const editor=new Client();editor.login('Editor do site');
 const operational=new Client();operational.login('Operacional');
 const uploadFilesBefore=fs.readdirSync(path.join(project,'uploads/site')).sort();
 assert.equal(operational.upload('site_projetos.php',{acao:'salvar',registro:'',titulo_pt:'permissions_fixture',titulo_en:'',descricao_pt:'',descricao_en:'',ordem:'1',ativo:'1'}).status,403);
 assert.deepEqual(fs.readdirSync(path.join(project,'uploads/site')).sort(),uploadFilesBefore);checks++;
 for(const endpoint of ['site_controller.php','site_uploads.php','site_helpers.php','site_config.php','site_sections_helpers.php','partials/site_page.php','partials/site_form.php','partials/site_sections_form.php','teste_upload.php']){assert.equal(operational.req(endpoint).status,403,endpoint);checks++;}
 for(const route of ['site_projetos.php','site_servicos.php']){
  const data={acao:'salvar',registro:'',titulo_pt:'permissions_fixture',titulo_en:'',descricao_pt:'Teste local',descricao_en:'',ordem:'1',ativo:'1',icone:'sprout',alt_pt:'Imagem de teste',alt_en:''};
  const table=route==='site_projetos.php'?'tb_site_projetos':'tb_site_servicos';
  assert.equal(editor.upload(route,data).status,302,route+' upload');
  const item=query("SELECT * FROM "+table+" WHERE titulo_pt='permissions_fixture'")[0];assert(item&&item.imagem_path);const imageFile=path.join(project,item.imagem_path);assert(fs.existsSync(imageFile));
  assert.equal(operational.post(route,{acao:'excluir',registro:String(item.id)}).status,403);assert.equal(query('SELECT id FROM '+table+' WHERE id='+item.id).length,1);
  assert.equal(editor.post(route,{...data,registro:String(item.id),titulo_pt:'permissions_fixture_updated'}).status,302);
  assert.equal(editor.post(route,{acao:'excluir',registro:String(item.id)}).status,302);assert.equal(query('SELECT id FROM '+table+' WHERE id='+item.id).length,0);assert(!fs.existsSync(imageFile));checks+=4;
 }
 const section=query("SELECT * FROM tb_site_secoes WHERE chave='services'")[0];
 assert.equal(editor.post('site_textos.php',{acao:'salvar',secao:'services',selo_pt:section.selo_pt,selo_en:section.selo_en,titulo_pt:section.titulo_pt,titulo_en:section.titulo_en,subtitulo_pt:section.subtitulo_pt,subtitulo_en:section.subtitulo_en}).status,302);checks++;
 assert.equal(editor.req('site_projetos.php',{csrf_token:'invalid',acao:'excluir',registro:'1'}).status,403);checks++;
 const editorId=query("SELECT log_codigo FROM tb_login WHERE log_login='roles_test_editor do site'")[0].log_codigo;
 query("UPDATE tb_login SET log_perfil='Operacional' WHERE log_codigo="+editorId);
 assert.equal(editor.req('site_projetos.php').status,302);assert.equal(editor.post('site_projetos.php',{acao:'excluir',registro:'1'}).status,403);assert(!editor.req('dashboard.php').body.includes('CONTEÚDO PÚBLICO'));checks++;
 query('UPDATE tb_login SET log_ativo=0 WHERE log_codigo='+editorId);assert.equal(editor.req('dashboard.php').status,403);checks++;
 const inactive=new Client();inactive.req('index.php');assert.equal(inactive.post('index.php',{login:'roles_test_editor do site',senha:'LocalRoles-2026!'}).status,200);assert.equal(inactive.req('dashboard.php').status,302);checks++;
 const live=new Client();live.req('index.php');assert.equal(live.req('index.php',{csrf_token:live.token,login:createdLogin,senha:'LocalRoles-2026!'}).status,302);live.req('perfil.php');
 assert.equal(live.req('cadastrar_cliente.php').status,403);
 assert.equal(admin.post('logins.php',{id_login:String(created.log_codigo),log_nome:'Acesso criado',log_login:createdLogin,log_senha:'',log_perfil:'Operacional'}).status,302);
 assert.equal(query('SELECT log_perfil FROM tb_login WHERE log_codigo='+created.log_codigo)[0].log_perfil,'Operacional');assert.equal(live.req('cadastrar_cliente.php').status,200);checks++;
 assert.equal(admin.post('logins.php',{id_login:String(created.log_codigo),log_nome:'Acesso criado',log_login:createdLogin,log_senha:'',log_perfil:'Consulta'}).status,302);assert.equal(live.req('cadastrar_cliente.php').status,403);checks++;
 const self=query("SELECT log_codigo FROM tb_login WHERE log_login='roles_test_administrador'")[0];assert.equal(admin.post('logins.php',{id_login:String(self.log_codigo),log_nome:'Teste',log_login:'roles_test_administrador',log_senha:'',log_perfil:'Consulta'}).status,400);checks++;
 // Password change updates only the session identity, even with an injected id.
 const otherHash=query('SELECT log_senha FROM tb_login WHERE log_codigo='+self.log_codigo)[0].log_senha;
 assert.equal(live.post('perfil.php',{id_login:String(self.log_codigo),senha_atual:'wrong',nova_senha:'ChangedLocal-2026!',confirmacao:'ChangedLocal-2026!'}).status,400);
 assert.equal(live.post('perfil.php',{id_login:String(self.log_codigo),senha_atual:'LocalRoles-2026!',nova_senha:'ChangedLocal-2026!',confirmacao:'ChangedLocal-2026!'}).status,302);assert.equal(query('SELECT log_senha FROM tb_login WHERE log_codigo='+self.log_codigo)[0].log_senha,otherHash);checks++;
 console.log(checks+' grupos de testes HTTP/CGI aprovados: criação/edição de perfis, bloqueios, CRUD de Operacional, sessões atualizadas e senha própria.');
} finally {
 query("DELETE FROM tb_clientes WHERE cli_Nome='roles_test_client'");query("DELETE FROM tb_login WHERE log_login LIKE 'roles_test_%'");
}
