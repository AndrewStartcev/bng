const fs = require('node:fs');
const vm = require('node:vm');
const assert = require('node:assert/strict');
const html = fs.readFileSync('assets/original/page.html', 'utf8');
const script = [...html.matchAll(/<script\b[^>]*>([\s\S]*?)<\/script>/g)].map(m=>m[1]).find(s=>s.includes('class Component extends DCLogic'));
function component(hash, urls, lang = 'ru') {
  const sandbox = {DCLogic: class { props = {}; setState(s) {Object.assign(this.state,s);} }, window:{BNG_DOMAIN_URLS:urls, BNG_INITIAL_LANG:lang, location:{hash,assign(value){this.assigned=value;}}}};
  vm.createContext(sandbox);
  vm.runInContext(script+';this.component = new Component();', sandbox);
  return sandbox;
}
for (const lang of ['ru', 'en']) {
  for (const page of ['home','rock','evidence','flowsheet','products','milestones','news','company']) {
    const s=component('#'+page,{[lang]:lang==='ru'?'https://bng-elements.ru/':'https://bng-elements.com/'},lang);
    assert.equal(s.component.state.page,page);
    assert.equal(s.component.state.lang,lang);
    assert.equal(s.component.switchLanguage,undefined);
    assert.equal(s.window.location.assigned,undefined);
  }
}
assert.equal(component('#invalid',{}).component.state.page,'home');
assert.ok(!html.includes('class="bng-client-language"'));
assert.ok(!script.includes('hreflang'));
assert.ok(script.includes('name: t.element[k]'));
console.log('Eight sections in both domain languages, no language controls or alternate links, localized footer OK');
