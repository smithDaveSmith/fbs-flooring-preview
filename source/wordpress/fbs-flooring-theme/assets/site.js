(()=>{'use strict';
const q=(s)=>document.querySelector(s), all=(s)=>[...document.querySelectorAll(s)];
const nav=q('#mobile-trigger'),menu=q('#mobile-menu');
function closeNav(){if(!menu)return;menu.hidden=true;nav.setAttribute('aria-expanded','false');nav.setAttribute('aria-label','Open navigation');}
nav?.addEventListener('click',()=>{menu.hidden=!menu.hidden;nav.setAttribute('aria-expanded',String(!menu.hidden));nav.setAttribute('aria-label',menu.hidden?'Open navigation':'Close navigation');});
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeNav();});
all('#mobile-menu a').forEach(a=>a.addEventListener('click',closeNav));
const path=location.pathname;all('.nav a').forEach(a=>{if(path===new URL(a.href).pathname)a.setAttribute('aria-current','page');});
const info=window.FBSContact||{email:'info@fbsflooring.ie',phone:'+353 83 044 1400'};
function enquiry(v){return ['Hello FBS,','I would like to discuss a flooring service.','',`Name: ${v.name||''}`,`Email: ${v.email||''}`,`Phone: ${v.phone||'Not provided'}`,`Area: ${v.location||'To discuss'}`,`Service: ${v.service||'Free home consultation'}`,`Project: ${v.project||'To discuss'}`,`Approximate floor area: ${v.area? v.area+' m²':'Not measured yet'}`,`Timing: ${v.timing||'To discuss'}`,'',v.message||''].join('\n');}
function links(message){return{email:`mailto:${info.email}?subject=${encodeURIComponent('FBS flooring service enquiry')}&body=${encodeURIComponent(message)}`,whatsapp:`https://wa.me/${info.phone.replace(/\D/g,'')}?text=${encodeURIComponent(message)}`};}
const form=q('#quote-form');let prepared='';
if(form){const params=new URLSearchParams(location.search);for(const key of ['service','area'])if(params.has(key)){const input=form.elements.namedItem(key);if(input)input.value=params.get(key);}
form.addEventListener('submit',e=>{e.preventDefault();if(!form.reportValidity())return;prepared=enquiry(Object.fromEntries(new FormData(form)));q('#quote-preview').textContent=prepared;const url=links(prepared);q('#email-enquiry').href=url.email;q('#whatsapp-enquiry').href=url.whatsapp;q('#quote-summary').hidden=false;q('#quote-summary').scrollIntoView({block:'nearest',behavior:window.matchMedia?.('(prefers-reduced-motion: reduce)').matches?'auto':'smooth'});});
form.addEventListener('input',()=>{q('#quote-summary').hidden=true;});
q('#copy-enquiry')?.addEventListener('click',async()=>{const state=q('#copy-status');try{await navigator.clipboard.writeText(prepared);state.textContent='Enquiry copied. Paste it into your message.';}catch{state.textContent='Select and copy the enquiry text above.';}});}
function area(length,width){return Number(length)*Number(width);}
const planner=q('#room-planner');planner?.addEventListener('submit',e=>{e.preventDefault();if(!planner.reportValidity())return;const v=Object.fromEntries(new FormData(planner)),total=area(v.length,v.width),result=q('#planner-result');q('#area-total').textContent=`${Number(total.toFixed(2))} m²`;q('#area-enquiry').href=`/contact-us/?area=${encodeURIComponent(total.toFixed(2))}#quote`;result.hidden=false;});
const search=q('#blog-search'),topic=q('#blog-category');function filter(){let count=0;all('[data-article]').forEach(a=>{a.hidden=!(a.dataset.title.includes((search?.value||'').toLowerCase())&&(!topic?.value||a.dataset.category===topic.value));if(!a.hidden)count++;});if(q('#blog-count'))q('#blog-count').textContent=`${count} ${count===1?'guide':'guides'}`;if(q('#blog-empty'))q('#blog-empty').hidden=count>0;}
search?.addEventListener('input',filter);topic?.addEventListener('change',filter);q('#blog-reset')?.addEventListener('click',()=>{search.value='';topic.value='';filter();search.focus();});
const dialog=q('#gallery-dialog');all('[data-gallery]').forEach(b=>b.addEventListener('click',()=>{const im=q('#gallery-image');im.src=b.dataset.gallery;im.alt=b.dataset.caption;q('#gallery-caption').textContent=b.dataset.caption;dialog.showModal();}));q('#gallery-close')?.addEventListener('click',()=>dialog.close());dialog?.addEventListener('click',e=>{if(e.target===dialog)dialog.close();});
window.FBSLogic={enquiry,links,area};
})();
