"""Regenerate GitHub Pages output, including project-subfolder-safe links."""
from pathlib import Path
import argparse
import os
import re
import shutil
import subprocess
import sys

ROOT = Path(__file__).resolve().parent

def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--origin', default='https://fbsflooring.ie',
                        help='Optional full preview URL for canonical metadata')
    args = parser.parse_args()
    env = dict(os.environ, FBS_STAGING='1', FBS_SITE_ORIGIN=args.origin.rstrip('/'))
    subprocess.run([sys.executable, 'build.py'], cwd=ROOT / 'source', env=env, check=True)
    subprocess.run([sys.executable, 'validate.py'], cwd=ROOT / 'source', check=True)
    output = ROOT / 'docs'
    if output.exists():
        shutil.rmtree(output)
    shutil.copytree(ROOT / 'source' / 'dist', output)
    for page in output.rglob('*.html'):
        depth = len(page.parent.relative_to(output).parts)
        prefix = '../' * depth or './'
        text = page.read_text()
        # Convert URL-bearing HTML attributes, including lightbox image paths.
        text = re.sub(r'((?:href|src|data-gallery)=[\"\'])/(?!/)',
                      lambda match: match[1] + prefix, text)
        text = re.sub(r'(content=[\"\']0;url=)/(?!/)',
                      lambda match: match[1] + prefix, text)
        # The room planner creates its contact URL at runtime.
        text = text.replace('`/contact-us/?area=', '`' + prefix + 'contact-us/?area=')
        page.write_text(text)
    (output / '.nojekyll').touch()
    print('GitHub Pages preview ready in docs/; links work under any repository name.')

if __name__ == '__main__':
    main()
