<?php if (!isset($site_content)) { http_response_code(404); exit; } ?>
<!doctype html>
<html lang="pt" class="dark">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Golden Jardim — Paisagismo Premium em Americana-SP</title>
<meta name="description" content="Paisagismo, jardinagem, poda e manutenção mensal de alto padrão em Americana-SP e região." />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;800&family=Inter:wght@300;400;500;600&display=swap" />
<link rel="stylesheet" href="assets/css/public.css" />
<link rel="stylesheet" href="assets/css/site-content.css?v=3" />
</head>
<body>

<header id="siteHeader">
  <div class="nav-inner">
    <a href="#top" class="brand">
      <span class="logo">🌿</span>
      <span class="name">Golden <span style="color:var(--moss-ink)">Jardim</span></span>
    </a>
    <nav class="links" id="site-nav" aria-label="Navegação principal">
      <a href="#services" data-i18n="nav.services">Serviços</a>
      <a href="#gallery" data-i18n="nav.projects">Projetos</a>
      <a href="#about" data-i18n="nav.about">Sobre</a>
      <a href="#contact" data-i18n="nav.contact">Contato</a>
    </nav>
    <div class="nav-actions">
      <button type="button" id="menu-toggle" aria-controls="site-nav" aria-expanded="false" aria-label="Abrir menu">☰</button>
      <!-- Language selector -->
      <div class="flex items-center gap-1 p-1 rounded-full border border-c glass" role="group" aria-label="Selecionar idioma">
        <button type="button" data-lang-btn="pt" class="lang-select-btn flex items-center gap-1.5 px-2.5 py-1.5 rounded-full text-xs font-medium border border-transparent cursor-pointer bg-transparent text-foreground" aria-pressed="true">
          <img src="https://flagcdn.com/w20/br.png" srcset="https://flagcdn.com/w40/br.png 2x" width="20" alt="Bandeira do Brasil" class="rounded-sm">
        </button>
        <button type="button" data-lang-btn="en" class="lang-select-btn flex items-center gap-1.5 px-2.5 py-1.5 rounded-full text-xs font-medium border border-transparent cursor-pointer bg-transparent text-foreground" aria-pressed="false">
          <img src="https://flagcdn.com/w20/us.png" srcset="https://flagcdn.com/w40/us.png 2x" width="20" alt="Bandeira dos Estados Unidos" class="rounded-sm">
        </button>
      </div>

      <!-- Theme toggle -->
      <button type="button" id="theme-toggle" class="theme-toggle glass" aria-label="Alternar tema claro/escuro" aria-pressed="false">
        <span class="knob">
          <svg id="theme-icon-sun" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true" style="display:none">
            <circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>
          </svg>
          <svg id="theme-icon-moon" width="13" height="13" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M21 12.6A9 9 0 1111.4 3a7 7 0 009.6 9.6z"/>
          </svg>
        </span>
      </button>

      <a href="#contact" class="btn-primary cta-btn" data-i18n="nav.cta">Orçamento</a>
    </div>
  </div>
</header>

<section id="top">
  <div class="hero-img">
    <img src="assets/hero-garden.jpg" alt="Jardim de luxo iluminado ao entardecer" />
    <div class="overlay1"></div>
    <div class="overlay2"></div>
  </div>
  <div class="hero-content">
    <div class="eyebrow"><span class="line"></span><span data-i18n="hero.eyebrow">Arquitetura • Natureza • Tecnologia</span></div>
    <h1 class="hero-title">
      <span data-i18n="hero.titleA">ENGENHARIA</span><br />
      <span class="grad" data-i18n="hero.titleB">DE ECOSSISTEMAS</span>
    </h1>
    <div class="hero-bottom">
      <p class="hero-body" data-i18n="hero.body">Paisagismo premium para residências, condomínios e empresas em Americana-SP. Unimos precisão técnica e arte biológica para criar obras vivas.</p>
      <div class="stats">
        <div class="stat glass"><div class="num">120+</div><div class="label" data-i18n="hero.stat1">Projetos</div></div>
        <div class="stat glass"><div class="num">15+</div><div class="label" data-i18n="hero.stat2">Anos de expertise</div></div>
      </div>
    </div>
  </div>
  <div class="scroll-hint"><span data-i18n="hero.scroll">Role para explorar</span><span>↓</span></div>
</section>

<section id="services">
  <div class="section-inner">
    <div class="services-head reveal">
      <span class="eyebrow-sm" data-i18n="services.eyebrow" data-site-eyebrow <?= trim($site_content['sections']['services']['pt']['eyebrow'])==='' ? 'hidden' : '' ?>><?= site_escape($site_content['sections']['services']['pt']['eyebrow']) ?></span>
      <h2 class="section-title" data-i18n="services.title"><?= site_escape($site_content['sections']['services']['pt']['title']) ?></h2>
      <p data-i18n="services.subtitle"><?= site_escape($site_content['sections']['services']['pt']['subtitle']) ?></p>
    </div>
    <div class="services-grid" id="servicesGrid"><?php site_render_services($site_content['services']); ?></div>
  </div>
</section>

<section id="gallery">
  <div class="section-inner">
    <div class="gallery-head reveal">
      <div>
        <span class="eyebrow-sm" data-i18n="gallery.eyebrow" data-site-eyebrow <?= trim($site_content['sections']['gallery']['pt']['eyebrow'])==='' ? 'hidden' : '' ?>><?= site_escape($site_content['sections']['gallery']['pt']['eyebrow']) ?></span>
        <h2 class="section-title" data-i18n="gallery.title"><?= site_escape($site_content['sections']['gallery']['pt']['title']) ?></h2>
      </div>
      <p data-i18n="gallery.subtitle"><?= site_escape($site_content['sections']['gallery']['pt']['subtitle']) ?></p>
    </div>
    <div class="gallery-grid" id="galleryGrid"><?php site_render_projects($site_content['projects']); ?></div>
  </div>
</section>

<section id="beforeafter">
  <div class="section-inner reveal">
    <span class="eyebrow-sm" data-i18n="ba.eyebrow">Caso de estudo</span>
    <h2 class="section-title" data-i18n="ba.title">TRANSFORMAÇÃO</h2>
    <div class="ba-frame" id="baFrame">
      <img src="assets/after.jpg" alt="Jardim após o projeto de paisagismo" />
      <div class="ba-before-wrap" id="baBeforeWrap"><img src="assets/before.jpg" alt="Área externa antes do projeto de paisagismo" /></div>
      <div class="ba-tag before" data-i18n="ba.before">Antes</div>
      <div class="ba-tag after" data-i18n="ba.after">Depois</div>
      <div class="ba-handle" id="baHandle" role="slider" tabindex="0" aria-label="Comparação antes e depois" aria-valuemin="0" aria-valuemax="100" aria-valuenow="50"><div class="dot">↔</div></div>
      <div class="ba-hint" data-i18n="ba.hint">Arraste para comparar</div>
    </div>
  </div>
</section>

<section id="testimonials">
  <div class="section-inner">
    <div class="reveal" style="margin-bottom:64px; max-width:680px;">
      <span class="eyebrow-sm" data-i18n="testimonials.eyebrow">Confiança</span>
      <h2 class="section-title" data-i18n="testimonials.title">O que dizem nossos clientes</h2>
    </div>
    <div class="test-grid" id="testGrid"></div>
  </div>
</section>

<section id="about">
  <div class="section-inner about-grid">
    <div class="about-img-wrap reveal">
      <div class="frame"><img src="assets/gallery-3.jpg" alt="Equipe Golden Jardim" /></div>
      <div class="about-badge"><p>Rodrigo &amp;<br />Fernanda Câmara</p><p>Founders</p></div>
    </div>
    <div class="about-body reveal">
      <span class="eyebrow-sm" data-i18n="about.eyebrow">Quem somos</span>
      <h2 class="section-title" style="margin-bottom:32px" data-i18n="about.title">Tecnologia e jardinagem em equilíbrio.</h2>
      <p data-i18n="about.body">A Golden Jardim nasceu para elevar o padrão do paisagismo na região de Americana-SP. Combinamos curadoria botânica, gestão técnica e identidade visual para entregar experiências verdes únicas.</p>
      <div class="about-founders">
        <div><div class="fname">Rodrigo Câmara</div><div class="frole" data-i18n="about.r.role">Gestão de Operações</div></div>
        <div><div class="fname">Fernanda Câmara</div><div class="frole" data-i18n="about.f.role">Gestão Financeira</div></div>
      </div>
    </div>
  </div>
</section>

<section id="contact" style="position:relative; padding:128px 24px; background:var(--background); overflow:hidden;">
  <div style="position:absolute; inset:0; background:radial-gradient(circle at 50% 50%, color-mix(in oklab, var(--moss) 8%, transparent) 0%, transparent 70%); pointer-events:none;" aria-hidden="true"></div>
  <div class="section-inner" style="max-width:800px; margin:0 auto; text-align:center; position:relative; z-index:10;">
    <span class="eyebrow-sm reveal" data-i18n="contact.eyebrow" style="display:block; margin-bottom:16px; font-family:monospace; text-transform:uppercase; letter-spacing:.3em; font-size:10px; color:var(--moss-ink);">// Vamos conversar</span>
    <h2 class="section-title reveal" style="font-family:'Sora'; font-weight:800; font-size:36px; letter-spacing:-.03em; line-height:1.1; margin-bottom:20px; transition-delay:.05s;" data-i18n="contact.title">Seu próximo projeto começa com uma conversa.</h2>
    <p class="reveal" style="font-size:15px; line-height:1.7; color:color-mix(in oklab, var(--foreground) 70%, transparent); max-width:600px; margin:0 auto 40px; transition-delay:.1s;" data-i18n="contact.subtitle">Paisagismo premium e engenharia de ecossistemas para sua residência ou empresa. Vamos entender sua necessidade e apresentar a melhor solução.</p>

    <a href="#" id="waContactBtn" target="_blank" rel="noopener noreferrer" class="reveal btn-primary" style="display:inline-flex; align-items:center; gap:12px; background:var(--moss); color:var(--forest); padding:16px 32px; border-radius:999px; font-weight:800; font-size:12px; text-transform:uppercase; letter-spacing:.12em; transition:transform .3s var(--ease), box-shadow .3s var(--ease); transition-delay:.15s;" aria-label="Conversar com a Golden Jardim pelo WhatsApp">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="#25D366" aria-hidden="true"><path d="M12.02 2C6.5 2 2 6.48 2 12c0 1.85.5 3.58 1.36 5.07L2 22l5.08-1.33A9.96 9.96 0 0012.02 22C17.54 22 22 17.52 22 12S17.54 2 12.02 2zm5.9 14.3c-.25.7-1.45 1.35-2 1.44-.5.08-1.15.11-1.86-.12-.43-.14-.98-.32-1.7-.63-2.97-1.28-4.9-4.27-5.05-4.47-.15-.2-1.2-1.6-1.2-3.05 0-1.46.76-2.17 1.03-2.47.27-.3.6-.37.8-.37.2 0 .4 0 .58.01.19.01.44-.07.68.53.25.6.85 2.08.92 2.23.07.15.12.33.02.53-.1.2-.15.32-.3.5-.15.17-.31.39-.44.52-.15.15-.3.31-.13.6.17.3.76 1.28 1.65 2.08 1.13 1.02 2.08 1.34 2.38 1.49.3.15.47.13.65-.08.18-.2.75-.87.95-1.17.2-.3.4-.25.68-.15.27.1 1.75.83 2.05 1 .3.15.5.23.57.35.07.13.07.75-.18 1.45z"/></svg>
      <span data-i18n="contact.waBtn">Conversar com a Golden Jardim</span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </a>

    <div class="reveal" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:32px; margin-top:40px; font-size:13px; color:color-mix(in oklab, var(--foreground) 70%, transparent); transition-delay:.2s;">
      <span style="display:inline-flex; align-items:center; gap:8px;"><span style="width:8px; height:8px; border-radius:50%; background:#25D366; display:inline-block;"></span><span data-i18n="contact.info1">Atendimento via WhatsApp</span></span>
      <span style="display:inline-flex; align-items:center; gap:8px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--moss-ink)" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 9h18M8 3v4M16 3v4"/></svg>
        <span data-i18n="contact.info2">Segunda a Sexta • 09h às 17h</span>
      </span>
      <span style="display:inline-flex; align-items:center; gap:8px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--moss-ink)" stroke-width="1.8"><path d="M13 2L4 14h6l-1 8 9-12h-6l1-8z"/></svg>
        <span data-i18n="contact.info3">Primeira resposta em poucos minutos</span>
      </span>
    </div>
  </div>
</section>

<footer class="site-footer">
  <div class="section-inner">
    <div class="footer-top">
      <div><div class="footer-wordmark">GOLDEN<br />JARDIM</div></div>
      <div class="footer-col">
        <h4 data-i18n="footer.studio">Estúdio</h4>
        <p data-i18n="footer.address">Americana, São Paulo
Atendemos Americana e região</p>
      </div>
      <div class="footer-col">
        <h4 data-i18n="footer.leadership">Liderança</h4>
        <p>Rodrigo Câmara<br /><span class="role" data-i18n="about.r.role">Gestão de Operações</span></p>
        <p>Fernanda Câmara<br /><span class="role" data-i18n="about.f.role">Gestão Financeira</span></p>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="rights" data-i18n="footer.rights">© 2026 Golden Jardim • Paisagismo de precisão</div>
      <div class="footer-links">
        <a href="https://instagram.com/goldenjardim" target="_blank" rel="noopener noreferrer">Instagram</a>
        <a id="waFooterLink" href="#" target="_blank" rel="noopener noreferrer">WhatsApp</a>
      </div>
    </div>
  </div>
</footer>

<a class="wa-float" id="waFloat" href="#" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
  <span class="bubble">💬</span>
  <span class="label" data-i18n="cta.float">Falar no WhatsApp</span>
</a>

<script type="application/json" id="site-content-data"><?= json_encode($site_content, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) ?></script>
<script src="assets/js/site-content.js?v=3"></script>
<script src="assets/js/public.js?v=3"></script>
</body>
</html>
