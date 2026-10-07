// Exercise the actual enquiry and area functions without sending any messages.
const fs=require('node:fs'),vm=require('node:vm'),assert=require('node:assert/strict');
const code=fs.readFileSync('assets/site.js','utf8');
const context={document:{querySelector:()=>null,querySelectorAll:()=>[],addEventListener:()=>{}},window:{},location:{pathname:'/'},URL,URLSearchParams};
vm.runInNewContext(code,context);const logic=context.window.FBSLogic;
assert.equal(logic.area(5,4),20);assert.equal(logic.area(3.25,4.4),14.3);
const message=logic.enquiry({name:'Test & check',email:'test@example.com',location:'Dublin',service:'floor-fitting',area:'20',message:'Already have flooring.\nPlease discuss fitting.'});
const urls=logic.links(message);assert.equal(new URL(urls.whatsapp).searchParams.get('text'),message);assert.equal(new URL(urls.email).searchParams.get('body'),message);
assert.ok(message.includes('Approximate floor area: 20 m²'));assert.ok(message.includes('test@example.com'));assert.ok(!message.includes('product'));assert.ok(logic.enquiry({}).includes('Not measured yet'));
console.log('Passed: room area, email/WhatsApp encoding, enquiry fields and optional measurements. No messages sent.');
