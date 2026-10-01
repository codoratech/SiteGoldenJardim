(() => {
  'use strict';
  const data = JSON.parse(document.getElementById('site-content-data').textContent);
  function node(tag, className, text) {
    const element = document.createElement(tag);
    if (className) element.className = className;
    if (text !== undefined) element.textContent = text;
    return element;
  }
  function image(path, alt) {
    const element = node('img');
    element.src = path;
    element.alt = alt;
    element.loading = 'lazy';
    return element;
  }
  function renderServices(language) {
    const fragment = document.createDocumentFragment();
    data.services.forEach((item, index) => {
      const card = node('div', 'service-card reveal');
      card.style.transitionDelay = `${index * 80}ms`;
      if (item.img) card.append(image(item.img, ''));
      card.append(node('div', 'grad'));
      const top = node('div', 'top');
      top.append(node('span', '', `${String(index + 1).padStart(2, '0')} / ${String(data.services.length).padStart(2, '0')}`));
      const icon = node('span', '', item.icon);
      icon.setAttribute('aria-hidden', 'true');
      top.append(icon);
      const bottom = node('div', 'bottom');
      bottom.append(node('h3', '', item[language][0]), node('p', '', item[language][1]), node('div', 'rule'));
      card.append(top, bottom);
      fragment.append(card);
    });
    document.getElementById('servicesGrid').replaceChildren(fragment);
  }
  function renderProjects(language) {
    const fragment = document.createDocumentFragment();
    data.projects.forEach((item, index) => {
      const text = item[language];
      const card = node('figure', `gallery-item reveal${item.img ? '' : ' gallery-item-no-image'}`);
      card.style.transitionDelay = `${index * 100}ms`;
      if (item.img) card.append(image(item.img, text.alt));
      card.append(node('div', 'grad'));
      const caption = node('figcaption', 'cap');
      const content = node('div');
      if (text.category) content.append(node('span', 'tag', text.category));
      content.append(node('h3', '', text.title));
      if (text.description) content.append(node('p', 'project-description', text.description));
      if (text.location) content.append(node('span', 'project-location', text.location));
      const arrow = node('span', 'arrow', '↗');
      arrow.setAttribute('aria-hidden', 'true');
      caption.append(content, arrow);
      card.append(caption);
      fragment.append(card);
    });
    document.getElementById('galleryGrid').replaceChildren(fragment);
  }
  window.GoldenSiteContent = {
    renderServices, renderProjects,
    mergeTranslations(dictionary) {
      for (const language of ['pt', 'en']) {
        for (const [section, fields] of Object.entries(data.sections)) {
          for (const [key, text] of Object.entries(fields[language])) {
            dictionary[language][`${section}.${key}`] = text;
          }
        }
      }
    }
  };
})();
