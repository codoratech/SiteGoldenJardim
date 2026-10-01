
/* ---------- CONTEÚDO / DADOS ---------- */
const WA_NUMBER = "5519989862859";

const dict = {
  pt: {
    "nav.services":"Serviços","nav.projects":"Projetos","nav.about":"Sobre","nav.contact":"Contato","nav.cta":"Orçamento",
    "hero.eyebrow":"Arquitetura • Natureza • Tecnologia","hero.titleA":"ENGENHARIA","hero.titleB":"DE ECOSSISTEMAS",
    "hero.body":"Paisagismo premium para residências, condomínios e empresas em Americana-SP. Unimos precisão técnica e arte biológica para criar obras vivas.",
    "hero.stat1":"Projetos","hero.stat2":"Anos de expertise","hero.scroll":"Role para explorar",
    "ba.eyebrow":"Caso de estudo","ba.title":"TRANSFORMAÇÃO","ba.before":"Antes","ba.after":"Depois","ba.hint":"Arraste ou use as setas para comparar",
    "testimonials.eyebrow":"Confiança","testimonials.title":"O que dizem nossos clientes",
    "about.eyebrow":"Quem somos","about.title":"Tecnologia e jardinagem em equilíbrio.",
    "about.body":"A Golden Jardim nasceu para elevar o padrão do paisagismo na região de Americana-SP. Combinamos curadoria botânica, gestão técnica e identidade visual para entregar experiências verdes únicas.",
    "about.r.role":"Gestão de Operações","about.f.role":"Gestão Financeira",
    "contact.eyebrow":"// Vamos conversar","contact.title":"Seu próximo projeto começa com uma conversa.","contact.subtitle":"Paisagismo premium e engenharia de ecossistemas para sua residência ou empresa. Vamos entender sua necessidade e apresentar a melhor solução.","contact.waBtn":"Conversar com a Golden Jardim","contact.info1":"Atendimento via WhatsApp","contact.info2":"Segunda a Sexta • 09h às 17h","contact.info3":"Primeira resposta em poucos minutos",
    "footer.studio":"Estúdio","footer.leadership":"Liderança","footer.rights":"© 2026 Golden Jardim • Paisagismo de precisão",
    "footer.address":"Americana, São Paulo\nAtendemos Americana e região","cta.float":"Falar no WhatsApp"
  },
  en: {
    "nav.services":"Services","nav.projects":"Projects","nav.about":"About","nav.contact":"Contact","nav.cta":"Get Quote",
    "hero.eyebrow":"Architecture • Nature • Technology","hero.titleA":"ENGINEERING","hero.titleB":"ECOSYSTEMS",
    "hero.body":"Premium landscaping for residential, condominium and commercial spaces in Americana, SP. We blend technical precision with biological artistry to create living masterpieces.",
    "hero.stat1":"Projects","hero.stat2":"Years expertise","hero.scroll":"Scroll to explore",
    "ba.eyebrow":"Case study","ba.title":"TRANSFORMATION","ba.before":"Before","ba.after":"After","ba.hint":"Drag or use arrow keys to compare",
    "testimonials.eyebrow":"Trust","testimonials.title":"What our clients say",
    "about.eyebrow":"About","about.title":"Technology and gardening in balance.",
    "about.body":"Golden Jardim was born to raise the bar for landscaping in the Americana-SP region. We combine botanical curation, technical management and visual identity to deliver unique green experiences.",
    "about.r.role":"Operations Manager","about.f.role":"Financial Manager",
    "contact.eyebrow":"// Let's talk","contact.title":"Your next project starts with a conversation.","contact.subtitle":"Premium landscaping and ecosystem engineering for your residence or business. Let's understand your needs and present the best solution.","contact.waBtn":"Chat with Golden Jardim","contact.info1":"WhatsApp support","contact.info2":"Monday to Friday • 9am to 5pm","contact.info3":"First reply in minutes",
    "footer.studio":"Studio","footer.leadership":"Leadership","footer.rights":"© 2026 Golden Jardim • Precision landscape",
    "footer.address":"Americana, São Paulo\nServing Americana & nearby cities","cta.float":"Chat on WhatsApp"
  }
};

const testimonials = [
  {pt:["A Golden Jardim transformou nossa entrada em uma assinatura visual. Profissionalismo absoluto.","Marina Rocha","Condomínio Alphaville Americana"], en:["Golden Jardim turned our entrance into a true visual signature. Absolute professionalism.","Marina Rocha","Alphaville Americana Condominium"]},
  {pt:["Atendimento técnico impecável. Cada planta foi escolhida com critério, cada detalhe pensado.","Eduardo Lima","Residencial — Nova Odessa"], en:["Flawless technical service. Every plant was chosen with care, every detail considered.","Eduardo Lima","Private residence — Nova Odessa"]},
  {pt:["Parceiros recorrentes em nossos projetos. Execução cirúrgica e estética sempre alinhada.","Studio Arq.5","Escritório de Arquitetura"], en:["Recurring partners on our projects. Surgical execution and consistent aesthetics.","Studio Arq.5","Architecture studio"]}
];

GoldenSiteContent.mergeTranslations(dict);

let lang = "pt";

function waUrl(text){
  return `https://wa.me/${WA_NUMBER}?text=${encodeURIComponent(text)}`;
}

function renderServices(){ GoldenSiteContent.renderServices(lang); }
function renderGallery(){ GoldenSiteContent.renderProjects(lang); }

function renderTestimonials(){
  const grid = document.getElementById("testGrid");
  grid.innerHTML = testimonials.map((t,i)=>`
    <figure class="test-card glass reveal" style="transition-delay:${i*120}ms">
      <span class="quote-mark" aria-hidden="true">❝</span>
      <blockquote>"${t[lang][0]}"</blockquote>
      <footer><div class="name">${t[lang][1]}</div><div class="role">${t[lang][2]}</div></footer>
    </figure>`).join("");
}

function applyI18n(){
  document.documentElement.lang = lang;
  document.querySelectorAll("[data-i18n]").forEach(el=>{
    const key = el.getAttribute("data-i18n");
    if(dict[lang][key] !== undefined) el.textContent = dict[lang][key];
    if(el.hasAttribute('data-site-eyebrow')) el.hidden = !el.textContent.trim();
  });
  renderServices();
  renderGallery();
  renderTestimonials();
  document.querySelectorAll("[data-lang-btn]").forEach(b=>{
    b.setAttribute("aria-pressed", b.getAttribute("data-lang-btn") === lang);
  });
  observeReveals();
}

/* ---------- TEMA ---------- */
function applyTheme(t){
  const isLight = t === "light";
  document.documentElement.classList.toggle("light", isLight);
  document.documentElement.classList.toggle("dark", !isLight);
  const sunIcon = document.getElementById("theme-icon-sun");
  const moonIcon = document.getElementById("theme-icon-moon");
  if(sunIcon) sunIcon.style.display = isLight ? "block" : "none";
  if(moonIcon) moonIcon.style.display = isLight ? "none" : "block";
  const themeToggle = document.getElementById("theme-toggle");
  if(themeToggle) themeToggle.setAttribute("aria-pressed", isLight);
  localStorage.setItem("gj-theme", t);
}
(function initTheme(){
  const saved = localStorage.getItem("gj-theme");
  const theme = saved || (window.matchMedia("(prefers-color-scheme: light)").matches ? "light" : "dark");
  applyTheme(theme);
})();
document.getElementById("theme-toggle").addEventListener("click", ()=>{
  const isLight = document.documentElement.classList.contains("light");
  applyTheme(isLight ? "dark" : "light");
});

/* ---------- IDIOMA ---------- */
document.querySelectorAll("[data-lang-btn]").forEach(btn=>{
  btn.addEventListener("click", ()=>{
    lang = btn.getAttribute("data-lang-btn");
    localStorage.setItem("gj-lang", lang);
    applyI18n();
  });
});

/* ---------- NAV SCROLL ---------- */
window.addEventListener("scroll", ()=>{
  document.getElementById("siteHeader").classList.toggle("scrolled", window.scrollY > 40);
}, {passive:true});

/* ---------- WHATSAPP LINKS ---------- */
function setupWhatsApp(){
  const greet = lang === "pt" ? "Olá Golden Jardim! Vim pelo site e gostaria de conversar sobre um projeto." : "Hello Golden Jardim! I visited the site and'd like to talk about a project.";
  const url = waUrl(greet);
  document.getElementById("waFloat").href = url;
  document.getElementById("waFooterLink").href = url;
  const contactBtn = document.getElementById("waContactBtn");
  if(contactBtn) contactBtn.href = url;
}

/* ---------- BEFORE / AFTER SLIDER ---------- */
(function(){
  const frame = document.getElementById("baFrame");
  const beforeWrap = document.getElementById("baBeforeWrap");
  const handle = document.getElementById("baHandle");
  let dragging = false;
  let position = 50;
  function setPosition(pos) {
    position = Math.max(0, Math.min(100, pos));
    beforeWrap.style.width = position + '%';
    handle.style.left = position + '%';
    handle.setAttribute('aria-valuenow', Math.round(position));
  }
  handle.addEventListener('keydown', e => {
    const changes = {ArrowLeft:-5, ArrowDown:-5, ArrowRight:5, ArrowUp:5};
    if (e.key in changes || e.key === 'Home' || e.key === 'End') {
      e.preventDefault();
      setPosition(e.key === 'Home' ? 0 : e.key === 'End' ? 100 : position + changes[e.key]);
    }
  });
  function update(clientX){
    const rect = frame.getBoundingClientRect();
    let pos = ((clientX - rect.left) / rect.width) * 100;
    pos = Math.max(0, Math.min(100, pos));
    setPosition(pos);
  }
  frame.addEventListener("mousedown", (e)=>{ dragging = true; update(e.clientX); });
  frame.addEventListener("touchstart", (e)=>{ dragging = true; update(e.touches[0].clientX); });
  window.addEventListener("mousemove", (e)=>{ if(dragging) update(e.clientX); });
  window.addEventListener("touchmove", (e)=>{ if(dragging) update(e.touches[0].clientX); });
  window.addEventListener("mouseup", ()=> dragging = false);
  window.addEventListener("touchend", ()=> dragging = false);
})();

/* ---------- REVEAL ON SCROLL ---------- */
let io;
function observeReveals(){
  if(io) io.disconnect();
  io = new IntersectionObserver((entries)=>{
    entries.forEach(entry=>{
      if(entry.isIntersecting){ entry.target.classList.add("in"); io.unobserve(entry.target); }
    });
  }, {threshold:.15});
  document.querySelectorAll(".reveal").forEach(el=> io.observe(el));
}

/* ---------- INIT ---------- */
const savedLang = localStorage.getItem("gj-lang");
if(savedLang === "pt" || savedLang === "en") lang = savedLang;
applyI18n();
setupWhatsApp();
const menuToggle = document.getElementById('menu-toggle');
const siteNav = document.getElementById('site-nav');
function closeMenu(){ siteNav.classList.remove('is-open'); menuToggle.setAttribute('aria-expanded','false'); }
menuToggle.addEventListener('click', () => {
  const open = siteNav.classList.toggle('is-open');
  menuToggle.setAttribute('aria-expanded', String(open));
});
siteNav.querySelectorAll('a').forEach(a => a.addEventListener('click', closeMenu));
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeMenu(); });
const origApplyI18n = applyI18n;
applyI18n = function(){ origApplyI18n(); setupWhatsApp(); };
applyI18n();
