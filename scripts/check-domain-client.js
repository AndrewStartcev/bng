const fs = require('node:fs');
const vm = require('node:vm');
const assert = require('node:assert/strict');
const html = fs.readFileSync('assets/original/page.html', 'utf8');
const script = [...html.matchAll(/<script\b[^>]*>([\s\S]*?)<\/script>/g)].map(m=>m[1]).find(s=>s.includes('class Component extends DCLogic'));
function component(hash, urls) {
  const sandbox = {DCLogic: class { props = {}; setState(s) {Object.assign(this.state,s);} }, window:{BNG_DOMAIN_URLS:urls, BNG_INITIAL_LANG:'ru', location:{hash,assign(value){this.assigned=value;}}}};
  vm.createContext(sandbox);
  vm.runInContext(script+';this.component = new Component();', sandbox);
  return sandbox;
}
for (const page of ['home','rock','evidence','flowsheet','products','milestones','news','company']) {
  const s=component('#'+page,{ru:'https://bng-elements.ru/',en:'https://bng-elements.com/'});
  assert.equal(s.component.state.page,page);
  s.component.switchLanguage('en');
  assert.equal(s.window.location.assigned,'https://bng-elements.com/'+(page==='home'?'':'#'+page));
}
const dev=component('#invalid',{});
assert.equal(dev.component.state.page,'home');
dev.component.switchLanguage('en');
assert.equal(dev.component.state.lang,'en');
assert.equal(dev.window.location.assigned,undefined);
dev.component.switchLanguage('invalid');
assert.equal(dev.component.state.lang,'en');
console.log('Eight section links, domain switching, unknown hash and dev language switching OK');
