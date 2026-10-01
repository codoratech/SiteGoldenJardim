const fs=require('node:fs'),assert=require('node:assert/strict'),{spawnSync}=require('node:child_process');
const root=process.cwd().replace(/\\/g,'/');
let cookie='',token='';
function php(code){const result=spawnSync('C:/xampp/php/php.exe',['-d','extension=gd'],{input:'<?php '+code,encoding:'utf8',timeout:10000});assert.equal(result.status,0,result.stderr);return result.stdout;}
function query(sql){return JSON.parse(php("mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT); $c=new mysqli('127.0.0.1','root','','gj_codex_test',3308);$c->set_charset('utf8mb4');$r=$c->query(base64_decode('"+Buffer.from(sql).toString('base64')+"'));echo json_encode($r instanceof mysqli_result?$r->fetch_all(MYSQLI_ASSOC):[]);"));}
function req(route,data,file){
 const [filename,qs='']=route.split('?');let contentType='application/x-www-form-urlencoded',input=Buffer.alloc(0);
 if(data && file){const boundary='----GoldenSiteLocalTest';const parts=[];for(const [key,value] of Object.entries(data))parts.push(Buffer.from(`--${boundary}\r\nContent-Disposition: form-data; name="${key}"\r\n\r\n${value}\r\n`));parts.push(Buffer.from(`--${boundary}\r\nContent-Disposition: form-data; name="imagem"; filename="${file.name}"\r\nContent-Type: ${file.mime}\r\n\r\n`),file.bytes,Buffer.from(`\r\n--${boundary}--\r\n`));input=Buffer.concat(parts);contentType='multipart/form-data; boundary='+boundary;}
 else if(data)input=Buffer.from(new URLSearchParams(data).toString());
 const env={...process.env,REDIRECT_STATUS:'1',REQUEST_METHOD:data?'POST':'GET',SCRIPT_FILENAME:root+'/'+filename,SCRIPT_NAME:'/'+filename,QUERY_STRING:qs,HTTP_COOKIE:cookie,CONTENT_TYPE:contentType,CONTENT_LENGTH:String(input.length),GJ_DB_HOST:'127.0.0.1',GJ_DB_USER:'root',GJ_DB_PASSWORD:'',GJ_DB_NAME:'gj_codex_test',GJ_DB_PORT:'3308'};
 const result=spawnSync('C:/xampp/php/php-cgi.exe',['-d','cgi.force_redirect=0','-d','extension=gd','-d','upload_max_filesize=8M','-d','post_max_size=12M','-d','upload_tmp_dir='+root+'/.local-tests/upload-tmp','-d','session.save_path='+root+'/.local-tests/sessions'],{env,input,encoding:'utf8',timeout:15000});assert.equal(result.status,0,result.stderr);assert(!/PHP (Warning|Fatal|Deprecated)/.test(result.stderr),result.stderr);assert(!/Fatal error|<b>Warning|<b>Deprecated/.test(result.stdout));
 const split=result.stdout.indexOf('\r\n\r\n'),head=result.stdout.slice(0,split),body=result.stdout.slice(split+4);const headers=new Map(head.split('\r\n').map(line=>{const i=line.indexOf(':');return [line.slice(0,i).toLowerCase(),line.slice(i+1).trim()]}));if(headers.has('set-cookie'))cookie=headers.get('set-cookie').split(';')[0];return {body,headers,status:Number((headers.get('status')||'200').split(' ')[0])};
}
function post(route,data,file){const response=req(route,{...data,csrf_token:token},file);assert.equal(response.status,302,response.body.replace(/<[^>]*>/g,'').slice(0,500));return response;}
function publicData(){const response=req('index.php');assert.equal(response.status,200);return JSON.parse(response.body.match(/id="site-content-data">(.*?)<\/script>/s)[1]);}
function imageInfo(path){return JSON.parse(php("$p='"+root+"/"+path+"';$i=getimagesize($p);$g=imagecreatefromwebp($p);echo json_encode(['width'=>$i[0],'height'=>$i[1],'mime'=>$i['mime'],'alpha'=>(imagecolorat($g,0,0)>>24)&127]);"));}
fs.mkdirSync('.local-tests/upload-tmp',{recursive:true});fs.mkdirSync('.local-tests/images',{recursive:true});
php("$c=new mysqli('127.0.0.1','root','','gj_codex_test',3308);$c->set_charset('utf8mb4');$c->multi_query(file_get_contents('migrations/003_site_conteudo.sql'));do{if($r=$c->store_result())$r->free();}while($c->more_results()&&$c->next_result());$i=imagecreatetruecolor(2400,1200);imagefill($i,0,0,imagecolorallocate($i,34,110,60));imagejpeg($i,'.local-tests/images/site-large.jpg',90);$i=imagecreatetruecolor(600,900);imagealphablending($i,false);imagesavealpha($i,true);imagefill($i,0,0,imagecolorallocatealpha($i,0,0,0,127));imagefilledrectangle($i,80,80,520,820,imagecolorallocatealpha($i,20,100,60,0));imagepng($i,'.local-tests/images/site-alpha.png');imagewebp($i,'.local-tests/images/site-alpha.webp',82);");
const jpeg={name:'site-large.jpg',mime:'image/jpeg',bytes:fs.readFileSync('.local-tests/images/site-large.jpg')};
const png={name:'site-alpha.png',mime:'image/png',bytes:fs.readFileSync('.local-tests/images/site-alpha.png')};
const webp={name:'site-alpha.webp',mime:'image/webp',bytes:fs.readFileSync('.local-tests/images/site-alpha.webp')};
php("$i=imagecreatetruecolor(1200,2400);imagefill($i,0,0,imagecolorallocate($i,34,110,60));imagejpeg($i,'.local-tests/images/site-portrait.jpg',90);");
const portrait={name:'site-portrait.jpg',mime:'image/jpeg',bytes:fs.readFileSync('.local-tests/images/site-portrait.jpg')};
assert.equal(req('admin/site_servicos.php').status,302,'Sessão não foi exigida.');
let login=req('admin/index.php');token=login.body.match(/name="csrf_token" value="([^"]+)"/)[1];post('admin/index.php',{login:'codex_teste_local',senha:'TesteLocal-2026!'});
token=req('admin/site_servicos.php').body.match(/name="csrf_token" value="([^"]+)"/)[1];
for(const [kind,table,route] of [['services','tb_site_servicos','admin/site_servicos.php'],['projects','tb_site_projetos','admin/site_projetos.php']]){
 for(const leftover of query("SELECT id FROM "+table+" WHERE titulo_pt LIKE '[TESTE LOCAL]%'") ) post(route,{acao:'excluir',registro:String(leftover.id)});
 const title='[TESTE LOCAL] '+kind+' '+Date.now();const data={acao:'salvar',registro:'',titulo_pt:title,titulo_en:'English '+title,descricao_pt:'Descrição com acentuação',descricao_en:'English description',ordem:'9999',ativo:'1',...(kind==='services'?{icone:'sprout'}:{categoria_pt:'Residencial',categoria_en:'Residential',local:'Americana',imagem_alt_pt:'Imagem PT',imagem_alt_en:'Image EN'})};
 const baseline=query('SELECT COUNT(*) AS n FROM '+table)[0].n;
 assert.equal(req(route,{...data,csrf_token:'invalid'},jpeg).status,403);assert.equal(query('SELECT COUNT(*) AS n FROM '+table)[0].n,baseline);
 let response=req(route,{...data,titulo_pt:'',csrf_token:token});assert.equal(response.status,400);assert(response.body.includes('Preencha este campo.')&&response.body.includes('Descrição com acentuação'));
 response=req(route,{...data,csrf_token:token},{name:'fake.jpg',mime:'image/jpeg',bytes:Buffer.from('<?php echo "unsafe"; ?>')});assert.equal(response.status,400);assert(response.body.includes('JPG, PNG ou WebP válida'));
 response=req(route,{...data,csrf_token:token},{name:'large.jpg',mime:'image/jpeg',bytes:Buffer.alloc(5*1024*1024+1)});assert.equal(response.status,400);assert(response.body.includes('no máximo 5 MB'));
 post(route,data,jpeg);let row=query('SELECT * FROM '+table+' ORDER BY id DESC LIMIT 1')[0];data.registro=String(row.id);assert.match(row.imagem_path,/^uploads\/site\/[a-f0-9]{32}\.webp$/);assert.equal(imageInfo(row.imagem_path).width,1600);assert.equal(imageInfo(row.imagem_path).height,800);assert.equal(imageInfo(row.imagem_path).mime,'image/webp');
 let pub=publicData();let item=pub[kind==='services'?'services':'projects'].find(item=>(kind==='services'?item.pt[0]:item.pt.title)===title);assert(item);assert.equal(kind==='services'?item.en[0]:item.en.title,data.titulo_en);
 const oldPath=row.imagem_path;data.titulo_pt=title+' <b>editado</b>';data.titulo_en='';post(route,data,png);row=query('SELECT * FROM '+table+' WHERE id='+row.id)[0];assert(!fs.existsSync(oldPath),'Imagem anterior permaneceu no disco.');assert.equal(imageInfo(row.imagem_path).alpha,127,'Transparência foi perdida.');assert(req(route).body.includes('&lt;b&gt;editado&lt;/b&gt;'));
 const pngPath=row.imagem_path;post(route,data,webp);row=query('SELECT * FROM '+table+' WHERE id='+row.id)[0];assert(!fs.existsSync(pngPath));assert.equal(imageInfo(row.imagem_path).alpha,127);
 const webpPath=row.imagem_path;post(route,data,portrait);row=query('SELECT * FROM '+table+' WHERE id='+row.id)[0];assert(!fs.existsSync(webpPath));assert.equal(imageInfo(row.imagem_path).width,800);assert.equal(imageInfo(row.imagem_path).height,1600,'Foto vertical ultrapassou 1600 px.');
 const retained=row.imagem_path;post(route,data);row=query('SELECT * FROM '+table+' WHERE id='+row.id)[0];assert.equal(row.imagem_path,retained,'Salvar sem upload apagou a imagem.');
 pub=publicData();item=pub[kind==='services'?'services':'projects'].find(item=>(kind==='services'?item.pt[0]:item.pt.title)===data.titulo_pt);assert.equal(kind==='services'?item.en[0]:item.en.title,data.titulo_pt);
 const before=query('SELECT id FROM '+table+' ORDER BY ordem,id').map(r=>r.id);post(route,{acao:'subir',registro:data.registro});const after=query('SELECT id FROM '+table+' ORDER BY ordem,id').map(r=>r.id);assert.equal(after.indexOf(row.id),before.indexOf(row.id)-1);post(route,{acao:'descer',registro:data.registro});assert.deepEqual(query('SELECT id FROM '+table+' ORDER BY ordem,id').map(r=>r.id),before);
 post(route,{acao:'alternar',registro:data.registro});pub=publicData();assert(!pub[kind==='services'?'services':'projects'].some(item=>(kind==='services'?item.pt[0]:item.pt.title)===data.titulo_pt));assert.equal(Number(query('SELECT ativo FROM '+table+' WHERE id='+row.id)[0].ativo),0);post(route,{acao:'alternar',registro:data.registro});
 req(route+'?acao=excluir&id='+row.id);assert.equal(Number(query('SELECT COUNT(*) AS n FROM '+table+' WHERE id='+row.id)[0].n),1,'GET excluiu registro.');
 post(route,{acao:'excluir',registro:data.registro});assert(!fs.existsSync(retained),'Imagem do registro excluído permaneceu.');assert.equal(query('SELECT COUNT(*) AS n FROM '+table)[0].n,baseline);assert(req(route).body.includes('excluído com sucesso.'));
 console.log(kind+': criar, editar, JPG/PNG/WebP, 1600px, transparência, substituir/remover arquivo, ordem, visibilidade, exclusão, PT/EN, validação e CSRF aprovados.');
}
assert(req('admin/dashboard.php').body.includes('chart-orders'));assert(req('admin/clientes.php').body.includes('manage-table'));
assert.equal(req('admin/site_textos.php',{acao:'salvar',secao:'services',csrf_token:'invalid'}).status,403);
const sectionFields=['selo_pt','selo_en','titulo_pt','titulo_en','subtitulo_pt','subtitulo_en'];
const originalSections=query('SELECT * FROM tb_site_secoes');
try {
 for(const key of ['services','gallery']) {
  const data={acao:'salvar',secao:key,selo_pt:'Selo <b>PT</b>',selo_en:'EN label',titulo_pt:'Título de teste com acentuação',titulo_en:'Test heading',subtitulo_pt:'Descrição da seção',subtitulo_en:'Section description'};
  const invalid=req('admin/site_textos.php',{...data,titulo_pt:'',csrf_token:token});assert.equal(invalid.status,400);assert(invalid.body.includes('Preencha o título em português.')&&invalid.body.includes('Descrição da seção'));
  assert.equal(req('admin/site_textos.php',{...data,secao:'services OR 1=1',csrf_token:token}).status,400);
  assert.equal(req('admin/site_textos.php',{...data,selo_pt:'x'.repeat(81),csrf_token:token}).status,400);
  post('admin/site_textos.php',data);let publicSection=publicData().sections[key];assert.equal(publicSection.pt.title,data.titulo_pt);assert.equal(publicSection.en.title,data.titulo_en);assert.equal(publicSection.en.eyebrow,data.selo_en);
  const adminText=req('admin/site_textos.php').body;assert(adminText.includes('Selo &lt;b&gt;PT&lt;/b&gt;'));assert.equal((adminText.match(/aria-label="Caminho da página"/g)||[]).length,1);assert(!adminText.includes('class="topbar-breadcrumb"'));assert.equal((adminText.match(/data-site-form/g)||[]).length,2);
  post('admin/site_textos.php',{...data,selo_en:'',titulo_en:'',subtitulo_en:''});publicSection=publicData().sections[key];assert.deepEqual(publicSection.en,publicSection.pt);
  req('admin/site_textos.php?acao=salvar&secao='+key+'&titulo_pt=GET');assert.equal(publicData().sections[key].pt.title,data.titulo_pt);
 }
} finally {for(const original of originalSections)post('admin/site_textos.php',{acao:'salvar',secao:original.chave,...Object.fromEntries(sectionFields.map(field=>[field,original[field]]))});}
console.log('Textos das duas seções: edição PT/EN, fallback EN, validação, CSRF, escape, dois formulários e breadcrumb único aprovados; textos originais restaurados.');
console.log('Dashboard e Clientes continuam funcionando; sem registros de teste remanescentes.');
