// Behaviour checks for reduced-motion, keyboard focus and unsupported browser fallback.
const fs=require('node:fs'),vm=require('node:vm'),assert=require('node:assert/strict');
const code=fs.readFileSync('assets/motion.js','utf8');
function env({reduced=false,observerSupported=true,observerThrows=false}={}){
 const animations=[],events={},frames=new Map(),media=[];let raf=0,io;
 function element(name){
  const classes=new Set(),styles=new Map();return {name,dataset:{},innerHTML:'Expert fitting.<br>A home that feels<br><em>finished.</em>',style:{setProperty:(k,v)=>styles.set(k,v),removeProperty:k=>styles.delete(k)},classes,styles,
   classList:{contains:k=>classes.has(k),add:k=>classes.add(k),remove:k=>classes.delete(k),toggle:(k,on)=>{if(on)classes.add(k);else classes.delete(k);}},
   animate:(keyframes,options)=>{const a={element:name,keyframes,options,cancelled:false,cancel(){this.cancelled=true;}};animations.push(a);return a;},contains:t=>t.parent===name,
   matches:s=>s.split(',').some(k=>k.trim()==='.'+name),closest:()=>null,getBoundingClientRect:()=>({top:100,bottom:650,height:550}),addEventListener:()=>{}};
 }
 const root=element('root');root.scrollHeight=3000;const header=element('site-header'),hero=element('hero-visual'),heading=element('heading'),card=element('service-card');card.classList.add('service-card');card.parentElement={children:[card]};
 const lines=[element('line1'),element('line2'),element('line3')];heading.querySelectorAll=()=>lines;
 function matches(query){return query==='.site-header'?header:query==='.hero h1,.page-heading h1,.article h1'?heading:query==='.hero-visual'?hero:null;}
 const document={documentElement:root,querySelector:matches,querySelectorAll:q=>q.startsWith('.section-head')?[card]:q==='.hero-visual,.split-photo,.inspiration-grid figure'?[hero]:[],addEventListener:(type,fn)=>events[type]=fn};
 const win={innerHeight:900,scrollY:400,matchMedia:query=>{const m={matches:query.includes('reduce')?reduced:true,change:null,addEventListener(type,fn){this.change=fn;}};media.push(m);return m;},requestAnimationFrame:fn=>{frames.set(++raf,fn);return raf;},cancelAnimationFrame:id=>frames.delete(id),addEventListener:(type,fn)=>events[type]=fn,removeEventListener:type=>delete events[type]};
 if(observerSupported)win.IntersectionObserver=class{constructor(cb,opts){if(observerThrows)throw Error('Unavailable observer');this.cb=cb;this.options=opts;this.targets=[];this.disconnected=false;io=this;}observe(el){this.targets.push(el);}unobserve(el){this.targets=this.targets.filter(x=>x!==el);}disconnect(){this.disconnected=true;}};
 vm.runInNewContext(code,{window:win,document,Map,WeakSet});
 return {win,root,header,hero,heading,card,animations,events,frames,media,get io(){return io;}};
}
const reduced=env({reduced:true});assert.equal(reduced.animations.length,0);assert.equal(reduced.root.classes.has('motion-enabled'),false);assert.equal(reduced.heading.dataset.motionHeading,undefined);assert.equal(reduced.hero.classes.has('motion-parallax'),false);
const unavailable=env({observerSupported:false});assert.equal(unavailable.animations.length,0);assert.equal(unavailable.heading.dataset.motionHeading,undefined);
const broken=env({observerThrows:true});assert.equal(broken.animations.length,0);assert.equal(broken.root.classes.has('motion-enabled'),false);
const enabled=env();assert.ok(enabled.animations.length>=4);enabled.io.cb([{target:enabled.card,isIntersecting:true}]);const cardAnimation=enabled.animations.find(x=>x.element==='service-card');assert.ok(cardAnimation);enabled.events.focusin({target:{parent:'service-card'}});assert.equal(cardAnimation.cancelled,true);
// Switching the OS preference cancels in-flight effects and restores the static presentation.
enabled.media[0].matches=true;enabled.media[0].change();assert.ok(enabled.animations.every(x=>x.cancelled));assert.equal(enabled.root.classes.has('motion-enabled'),false);assert.equal(enabled.hero.classes.has('motion-parallax'),false);assert.equal(enabled.frames.size,0);assert.equal(enabled.io.disconnected,true);
console.log('Passed: reduced-motion initial load/live change, keyboard focus, unsupported/failed observer, and frame cleanup.');
