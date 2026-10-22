(() => {
  const screens=[...document.querySelectorAll('[data-bng-screen][data-bng-lang]')];
  const nav=document.querySelector('.site-header__nav'),toggle=document.querySelector('.site-header__toggle');
  let section='home',lang='ru';
  const closeMenu=()=>{nav?.classList.remove('is-open');toggle?.setAttribute('aria-expanded','false');};
  toggle?.addEventListener('click',()=>{const on=toggle.getAttribute('aria-expanded')!=='true';toggle.setAttribute('aria-expanded',String(on));nav?.classList.toggle('is-open',on);});
  document.addEventListener('keydown',e=>{if(e.key==='Escape')closeMenu();});
  document.addEventListener('click',e=>{if(nav&&toggle&&!nav.contains(e.target)&&!toggle.contains(e.target))closeMenu();});
  function show(target,locale){
    const active=screens.find(node=>node.dataset.bngScreen===target&&node.dataset.bngLang===locale);
    if(!active)return;
    section=target;lang=locale;screens.forEach(node=>{node.hidden=node!==active;});
    document.documentElement.lang=lang;
    document.querySelectorAll('[data-bng-i18n-ru][data-bng-i18n-en]').forEach(el=>{el.textContent=el.getAttribute('data-bng-i18n-'+lang);});
    nav?.querySelectorAll('[data-bng-target]').forEach(link=>{
      const current=link.dataset.bngTarget===section;
      link.classList.toggle('is-active',current);
      if(current)link.setAttribute('aria-current','page');else link.removeAttribute('aria-current');
    });
    document.querySelectorAll('[data-bng-lang-button]').forEach(btn=>btn.setAttribute('aria-pressed',String(btn.dataset.bngLangButton===lang)));
    document.title=active.querySelector('.running-head span')?.textContent||active.querySelector('h1')?.textContent||'BNG Elements';
    closeMenu();window.scrollTo(0,0);
    const heading=active.querySelector('h1');if(heading){heading.setAttribute('tabindex','-1');heading.focus({preventScroll:true});}
  }
  document.addEventListener('click',e=>{
    const link=e.target.closest('a[data-bng-target]');
    if(link){e.preventDefault();show(link.dataset.bngTarget,lang);return;}
    const button=e.target.closest('[data-bng-lang-button]');
    if(button)show(section,button.dataset.bngLangButton);
  });
  document.querySelectorAll('[data-bng-screen="products"]').forEach(screen=>{
    const filters=[...screen.querySelectorAll('[data-bng-filter]')],rows=[...screen.querySelectorAll('[data-bng-segment]')],count=screen.querySelector('[data-bng-count]');
    filters.forEach(button=>button.addEventListener('click',()=>{
      const segment=button.dataset.bngFilter;let visible=0;
      filters.forEach(f=>{const active=f===button;f.classList.toggle('is-active',active);f.setAttribute('aria-pressed',String(active));});
      rows.forEach(row=>{row.hidden=segment!=='all'&&row.dataset.bngSegment!==segment;if(!row.hidden)visible++;});
      if(count)count.textContent=visible+' / '+rows.length;
    }));
  });
})();
