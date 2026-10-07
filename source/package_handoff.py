"""Package the corrected service website, full source and native WordPress theme."""
from pathlib import Path
import json,re,shutil,zipfile,os,html
from html.parser import HTMLParser
class Plain(HTMLParser):
 def __init__(self):super().__init__();self.parts=[]
 def handle_data(self,value):self.parts.append(value)
def clean(value):
 p=Plain();p.feed(value);return ''.join(p.parts).strip()
def blocks(sections):
 return ''.join('<!-- wp:heading --><h2 class="wp-block-heading">'+html.escape(title)+'</h2><!-- /wp:heading --><!-- wp:paragraph --><p>'+html.escape(text)+'</p><!-- /wp:paragraph -->' for title,text in sections)
ROOT=Path(__file__).parent;DIST=ROOT/'dist';THEME=ROOT/'wordpress/fbs-flooring-theme';SEED=THEME/'seed'
SEED.mkdir(exist_ok=True)
home=(DIST/'index.html').read_text();header=re.search(r'<body>(.*?)<main id="main">',home,re.S)[1];footer=re.search(r'</main>(.*?)<script>',home,re.S)[1]
(SEED/'header.html').write_text(header);(SEED/'footer.html').write_text(footer);(SEED/'home.html').write_text(re.search(r'<main id="main">(.*?)</main>',home,re.S)[1])
for name in ['site','guides','images']:shutil.copy2(ROOT/'content'/f'{name}.json',SEED/f'{name}.json')
guides=json.loads((ROOT/'content/guides.json').read_text());excluded={'/'+g['slug']+'/' for g in guides}|{'/services/'+s['slug']+'/' for s in json.loads((ROOT/'content/site.json').read_text())['services']}
pages=[]
for entry in json.loads((ROOT/'content/built-pages.json').read_text()):
 if entry['path'] in excluded:continue
 file=DIST/('index.html' if entry['path']=='/' else entry['path'].strip('/')+'/index.html')
 body=re.search(r'<main id="main">(.*?)</main>',file.read_text(),re.S)[1]
 visual=False;sections=[]
 if entry['path']=='/faqs/':sections=[(clean(a),clean(b)) for a,b in re.findall(r'<summary>(.*?)</summary><p>(.*?)</p>',body,re.S)]
 elif entry['path']=='/how-it-works/':sections=[(clean(a),clean(b)) for a,b in re.findall(r'<strong>(.*?)</strong><span>(.*?)</span>',body,re.S)]
 elif entry['path']=='/about-us/':
  content=re.search(r'<div class="split-copy">(.*?)</div>',body,re.S)[1]
  sections=[('About FBS',clean(v)) for v in re.findall(r'<p>(.*?)</p>',content,re.S)]
 elif entry['path'] in ['/privacy-policy/','/cookie-policy/']:sections=[(clean(a),clean(b)) for a,b in re.findall(r'<h2>(.*?)</h2><p>(.*?)</p>',body,re.S)]
 if sections:visual=True
 pages.append({**entry,'slug':'home' if entry['path']=='/' else entry['path'].strip('/'),'visual':visual,'content':blocks(sections) if visual else '<!-- wp:html -->'+body+'<!-- /wp:html -->'})
(SEED/'pages.json').write_text(json.dumps(pages,ensure_ascii=False))
# Bundle only the pictures in the services site, replacing obsolete catalogue assets.
for p in (THEME/'assets').iterdir():
 if p.suffix=='.webp' or p.name in ['data.json','site.css','site.js']:p.unlink()
for p in (DIST/'assets').iterdir():
 if p.is_file():shutil.copy2(p,THEME/'assets'/p.name)
release=ROOT/'release';release.mkdir(exist_ok=True);theme_zip=release/'FBS-WordPress-Theme.zip'
def zip_tree(base,dest,prefix):
 with zipfile.ZipFile(dest,'w',zipfile.ZIP_DEFLATED,compresslevel=6) as z:
  for p in sorted(base.rglob('*')):
   if p.is_file():z.write(p,Path(prefix)/p.relative_to(base))
zip_tree(THEME,theme_zip,'fbs-flooring-theme')
destination=Path(os.environ.get('FBS_HANDOFF_DIRECTORY',str(release/'handoff')));export=destination/'fbs-handoff'
if export.exists():shutil.rmtree(export)
export.mkdir(parents=True)
shutil.copytree(DIST,export/'website');source=export/'source';source.mkdir()
for name in ['build.py','requirements.txt','validate.py','verify_interactions.cjs','verify_motion.cjs','package_handoff.py','README.md','serve.py','CONTENT-AUDIT.md']:
 if (ROOT/name).exists():shutil.copy2(ROOT/name,source/name)
shutil.copytree(ROOT/'assets',source/'assets');shutil.copytree(ROOT/'content',source/'content');shutil.copytree(ROOT/'wordpress',source/'wordpress')
shutil.copy2(theme_zip,export/theme_zip.name);shutil.copy2(ROOT/'README.md',export/'READ-ME-FIRST.md');shutil.copy2(ROOT/'CONTENT-AUDIT.md',export/'CONTENT-AUDIT.md')
bundle=destination/'FBS-Complete-Website.zip';zip_tree(export,bundle,'FBS-Flooring')
shutil.copy2(theme_zip,destination/theme_zip.name);shutil.copy2(ROOT/'README.md',destination/'FBS-Setup-Guide.md')
print(json.dumps({'complete':str(bundle),'theme':str(theme_zip),'bytes':bundle.stat().st_size,'pages':len(pages)}))
