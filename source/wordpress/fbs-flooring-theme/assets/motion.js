/* FBS motion: progressive enhancement; readable content is the default. */
(()=>{'use strict';
if(typeof window.matchMedia!=='function'||typeof document.querySelectorAll!=='function')return;
const reduce=window.matchMedia('(prefers-reduced-motion: reduce)');
const desktop=window.matchMedia('(min-width: 921px) and (pointer: fine)');
const root=document.documentElement,header=document.querySelector('.site-header');
const active=new Map(),seen=new WeakSet();let observer=null,frame=0,started=false,photos=[];
const all=(selector)=>[...document.querySelectorAll(selector)];
const ease='cubic-bezier(.22,1,.36,1)';
function cancel(element){const animation=active.get(element);if(animation){animation.cancel();active.delete(element);}}
function play(element,frames,delay=0,duration=760){
 if(!element||reduce.matches||typeof element.animate!=='function')return;
 cancel(element);
 // No permanent hidden class or fill remains if JavaScript/animation fails.
 try{const animation=element.animate(frames,{duration,delay,easing:ease,fill:'backwards'});active.set(element,animation);
 animation.onfinish=()=>{if(active.get(element)===animation){active.delete(element);animation.cancel();}};
 }catch{cancel(element);}
}
function headline(heading){
 if(!heading||heading.dataset.motionHeading)return;
 const lines=heading.innerHTML.split(/<br\s*\/?\s*>/i);
 // Preserve words, emphasis and natural wrapping; never split accessible text into letters.
 heading.innerHTML=lines.map(line=>'<span class="motion-line"><span class="motion-line-inner">'+line+'</span></span>').join('');
 heading.dataset.motionHeading='true';
 [...heading.querySelectorAll('.motion-line-inner')].forEach((line,i)=>play(line,[{transform:'translate3d(0,110%,0)'},{transform:'translate3d(0,0,0)'}],Math.min(i*95,285),900));
}
function reveal(element){
 if(seen.has(element))return;seen.add(element);
 let delay=0;
 if(element.classList.contains('service-card')||element.classList.contains('article-card')||element.classList.contains('gallery-item')){
  const siblings=[...element.parentElement.children];delay=Math.min(siblings.indexOf(element)%3*85,170);
 }
 if(element.matches('.split-photo,.inspiration-grid figure,.article-photo,.hero-visual')){
  play(element,[{clipPath:'inset(0 0 100% 0)'},{clipPath:'inset(0 0 0% 0)'}],delay,1050);
 }else{
  play(element,[{opacity:0,transform:'translate3d(0,24px,0)'},{opacity:1,transform:'translate3d(0,0,0)'}],delay,720);
 }
}
function update(){
 frame=0;if(!started||reduce.matches)return;
 const y=Math.max(0,window.scrollY||0),height=Math.max(1,root.scrollHeight-window.innerHeight);
 root.style.setProperty('--reading-progress',Math.min(1,y/height).toFixed(4));
 header?.classList.toggle('is-scrolled',y>24);
 if(desktop.matches){for(const photo of photos){const box=photo.getBoundingClientRect();
  if(box.bottom<0||box.top>window.innerHeight)continue;
  const ratio=(window.innerHeight/2-(box.top+box.height/2))/Math.max(window.innerHeight,1);
  photo.style.setProperty('--image-shift',Math.max(-16,Math.min(16,ratio*24)).toFixed(2)+'px');
 }}
}
function requestUpdate(){if(!frame&&started)frame=window.requestAnimationFrame(update);}
function resetPhotos(){for(const photo of photos){photo.classList.toggle('motion-parallax',started&&!reduce.matches&&desktop.matches);photo.style.removeProperty('--image-shift');}requestUpdate();}
function stop(){
 started=false;observer?.disconnect();observer=null;
 if(frame){window.cancelAnimationFrame(frame);frame=0;}
 for(const animation of active.values())animation.cancel();active.clear();
 window.removeEventListener('scroll',requestUpdate);window.removeEventListener('resize',requestUpdate);
 root.classList.remove('motion-enabled');root.style.removeProperty('--reading-progress');header?.classList.remove('is-scrolled');
 for(const photo of photos){photo.classList.remove('motion-parallax');photo.style.removeProperty('--image-shift');}
}
function start(){
 if(started||reduce.matches||typeof window.IntersectionObserver!=='function')return;
 // Register the observer before changing presentation, so unsupported browsers keep static content.
 try{observer=new window.IntersectionObserver(entries=>{for(const entry of entries){if(!entry.isIntersecting)continue;reveal(entry.target);observer.unobserve(entry.target);}},{threshold:0.08,rootMargin:'0px 0px -24px 0px'});
 started=true;root.classList.add('motion-enabled');
 headline(document.querySelector('.hero h1,.page-heading h1,.article h1'));
 const hero=document.querySelector('.hero-visual');if(hero)reveal(hero);
 for(const element of all('.section-head,.statement,.service-card,.article-card,.gallery-item,.split-copy,.split-photo,.inspiration-grid figure,.article-photo,.cta-inner,.faq details,.content-grid aside')){
  if(element.closest('.article-card,.service-card,.gallery-item')!==element&&element.closest('.article-card,.service-card,.gallery-item'))continue;
  observer.observe(element);
 }
 photos=all('.hero-visual,.split-photo,.inspiration-grid figure');resetPhotos();
 window.addEventListener('scroll',requestUpdate,{passive:true});window.addEventListener('resize',requestUpdate,{passive:true});requestUpdate();
 }catch{stop();}
}
// Keyboard users should never wait for a focused control to finish appearing.
document.addEventListener('focusin',event=>{for(const element of [...active.keys()]){if(element===event.target||element.contains(event.target)){cancel(element);seen.add(element);}}});
// Re-filtered guides and mobile navigation stay immediately visible.
const menu=document.querySelector('#mobile-menu');document.querySelector('#mobile-trigger')?.addEventListener('click',()=>{if(menu&&!menu.hidden)play(menu,[{opacity:0,transform:'translateY(-8px)'},{opacity:1,transform:'translateY(0)'}],0,260);});
const dialog=document.querySelector('#gallery-dialog');dialog?.addEventListener('toggle',()=>{if(dialog.open)play(dialog,[{opacity:0,transform:'translateY(12px) scale(.985)'},{opacity:1,transform:'translateY(0) scale(1)'}],0,260);});
reduce.addEventListener?.('change',()=>{if(reduce.matches)stop();else start();});desktop.addEventListener?.('change',resetPhotos);
window.addEventListener('pagehide',stop);window.addEventListener('pageshow',event=>{if(event.persisted)start();});
window.FBSMotion={start,stop};start();
})();
