from pathlib import Path
from html.parser import HTMLParser
from urllib.parse import urlparse,unquote
import json,collections
ROOT=Path(__file__).parent/'dist'
class Inspect(HTMLParser):
 def __init__(self):super().__init__();self.refs=[];self.ids=[];self.h1=0;self.title=False;self.description=False
 def handle_starttag(self,tag,attrs):
  d=dict(attrs)
  if tag=='h1':self.h1+=1
  if tag=='title':self.title=True
  if tag=='meta' and d.get('name')=='description':self.description=True
  if d.get('id'):self.ids.append(d['id'])
  if tag=='a' and d.get('href'):self.refs.append(('link',d['href']))
  if tag in ['img','script'] and d.get('src'):self.refs.append(('asset',d['src']))
  if tag=='link' and d.get('rel')=='stylesheet':self.refs.append(('asset',d.get('href','')))
errors=[];files=list(ROOT.rglob('*.html'));redirects=0;links=assets=0
for file in files:
 text=file.read_text();p=Inspect();p.feed(text)
 if 'http-equiv="refresh"' in text:redirects+=1;continue
 if p.h1!=1:errors.append((str(file.relative_to(ROOT)),'Expected one H1',p.h1))
 if not p.title or not p.description:errors.append((str(file.relative_to(ROOT)),'Missing metadata'))
 duplicates=[v for v,n in collections.Counter(p.ids).items() if n>1]
 if duplicates:errors.append((str(file),'Duplicate IDs',duplicates))
 for kind,ref in p.refs:
  parsed=urlparse(ref)
  if parsed.scheme or ref.startswith('//'):continue
  path=unquote(parsed.path)
  if path.startswith('/'):
   dest=ROOT/path.lstrip('/')
  elif path:dest=file.parent/path
  else:dest=file
  if dest.is_dir():dest=dest/'index.html'
  if not dest.exists():errors.append((str(file.relative_to(ROOT)),'Missing '+kind,ref))
  if kind=='link':links+=1
  else:assets+=1
  if parsed.fragment and dest.exists() and dest.suffix=='.html':
   q=Inspect();q.feed(dest.read_text())
   if parsed.fragment not in q.ids:errors.append((str(file.relative_to(ROOT)),'Missing anchor',ref))
for file in (ROOT/'assets').glob('*.webp'):
 from PIL import Image
 try:
  with Image.open(file) as im:im.verify()
 except Exception as ex:errors.append((str(file),'Invalid image',str(ex)))
# Business-model check: no retail routes or product data in published output.
for path in ['product','product-category','floors']:
 if (ROOT/path).exists():errors.append(('dist/'+path,'Retail content must not be published'))
for file in files:
 if 'http-equiv="refresh"' in file.read_text():continue
 text=file.read_text()
 for phrase in ['FBSCatalog','Supply only','Visit the showroom','Explore our floors','Room & pack calculator','Request price','data-save=']:
  if phrase in text:errors.append((str(file), 'Obsolete retail content',phrase))
 if file.name!='404.html' and 'noindex,nofollow' not in text:errors.append((str(file),'Review preview must remain noindex'))
if errors:
 print(json.dumps(errors[:30],indent=2));raise SystemExit(1)
print(json.dumps({'html_pages':len(files),'redirects':redirects,'internal_links_checked':links,'asset_references_checked':assets,'retail_products':0,'articles':len(json.loads((ROOT.parent/'content/guides.json').read_text())),'local_images':len(list((ROOT/'assets').glob('*.webp'))),'result':'passed'}))
