<?php
// Original public content, also used as the migration seed and outage fallback.
function site_defaults() {
    return [
      'sections'=>[
        'services'=>['pt'=>['title'=>"NOSSOS\nSERVIÇOS",'subtitle'=>'Especialidades conduzidas por Rodrigo & Fernanda Câmara','eyebrow'=>''],'en'=>['title'=>"OUR\nSERVICES",'subtitle'=>'Specialty services led by Rodrigo & Fernanda Câmara','eyebrow'=>'']],
        'gallery'=>['pt'=>['title'=>'Projetos assinados','subtitle'=>'Uma seleção de transformações entregues em Americana e região.','eyebrow'=>'Portfólio'],'en'=>['title'=>'Signature projects','subtitle'=>'Selected transformations delivered across Americana region.','eyebrow'=>'Portfolio']],
      ],
      'services'=>[
        ['icon'=>'sprout','img'=>'assets/service-landscaping.jpg','pt'=>['Paisagismo','Projetos autorais de paisagismo, da concepção 3D à execução final.'],'en'=>['Landscaping','Signature landscape design — from 3D vision to flawless delivery.']],
        ['icon'=>'droplet','img'=>null,'pt'=>['Manutenção','Contratos mensais para condomínios e residências de alto padrão.'],'en'=>['Maintenance','Monthly contracts for premium condominiums and residences.']],
        ['icon'=>'pruning','img'=>'assets/service-pruning.jpg','pt'=>['Poda Técnica','Poda de árvores e jardins com técnicas avançadas de condução.'],'en'=>['Expert Pruning','Tree and garden pruning with advanced shaping techniques.']],
        ['icon'=>'tree','img'=>null,'pt'=>['Jardinagem','Cuidado contínuo, adubação inteligente e saúde vegetal completa.'],'en'=>['Gardening','Ongoing care, smart fertilization and full plant health.']],
        ['icon'=>'building','img'=>'assets/service-commercial.jpg','pt'=>['Comercial','Soluções escaláveis para empresas, lobbies e áreas corporativas.'],'en'=>['Commercial','Scalable solutions for offices, lobbies and corporate sites.']],
        ['icon'=>'compass','img'=>null,'pt'=>['Consultoria','Diagnóstico, plano de manejo e curadoria botânica especializada.'],'en'=>['Consulting','Diagnostics, management plans and curated botanical advisory.']],
      ],
      'projects'=>[
        ['img'=>'assets/gallery-1.jpg','pt'=>['title'=>'Residência Vista Mar','description'=>'','category'=>'Residencial','location'=>'','alt'=>'Residência Vista Mar'],'en'=>[]],
        ['img'=>'assets/gallery-2.jpg','pt'=>['title'=>'Sede Corporativa Atrium','description'=>'','category'=>'Comercial','location'=>'','alt'=>'Sede Corporativa Atrium'],'en'=>[]],
        ['img'=>'assets/gallery-3.jpg','pt'=>['title'=>'Caminho das Palmeiras','description'=>'','category'=>'Paisagismo','location'=>'','alt'=>'Caminho das Palmeiras'],'en'=>[]],
        ['img'=>'assets/gallery-4.jpg','pt'=>['title'=>'Condomínio Belvedere','description'=>'','category'=>'Condomínio','location'=>'','alt'=>'Condomínio Belvedere'],'en'=>[]],
      ],
    ];
}
