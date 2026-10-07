# FBS Flooring — client preview on GitHub Pages

This package contains the animated services-only FBS website and its complete source code. It was exported from the published animation update on 7 October 2026. The original business website is unaffected.

## Publish with GitHub's website — no terminal required

1. Sign in at https://github.com. Create an account if necessary.
2. Open https://github.com/new and name the repository **fbs-flooring-preview**.
3. Choose **Public** for the free GitHub Pages route. This makes the repository's code and assets public too. Tick **Add a README file**, then create the repository. Do not select a software licence for third-party business imagery.
4. Unzip this download. Open the extracted **fbs-flooring-preview** folder. You should see **docs**, **source**, **build_preview.py** and this **README.md**.
5. In your new repository choose **Add file → Upload files**. Drag **docs**, **source**, **build_preview.py** and **README.md** into the upload area together. Upload the contents of the extracted folder, not its outer folder and not the ZIP itself. Confirm the upload keeps the folders. If GitHub asks about replacing its initial README, use this supplied version.
6. Enter the commit message **Add FBS website preview**, then choose **Commit changes** to the default **main** branch. The repository root must contain **docs/index.html**, not **fbs-flooring-preview/docs/index.html**.
7. Open the repository's **Settings → Pages**. Under **Build and deployment**, set **Source** to **Deploy from a branch**.
8. Select branch **main** and folder **/docs**, then click **Save**. Leave **Custom domain** blank for this preview.
9. Wait for the **pages build and deployment** run in **Actions** to finish successfully. In **Settings → Pages**, use **Visit site** or copy the published URL. The usual address is **https://YOUR-GITHUB-USERNAME.github.io/fbs-flooring-preview/**. This is an example, not an already published address.
10. Test the homepage, a service detail, an advice guide, gallery image, mobile navigation and room planner. The planner must open the contact page within the preview and carry its calculated area. Then share the **website URL** with the client. They do not need a GitHub account.

GitHub's browser uploader supports up to 100 files per upload and 25 MiB per file. This package's visible repository contents are below those limits. On a Mac, the hidden **docs/.nojekyll** file might not be selected by dragging folders. This plain HTML site also works with the default Pages build. To disable Jekyll explicitly, use **Add file → Create new file**, name it **docs/.nojekyll**, leave it empty, and commit it.

## What is in each folder?

- **docs/**: ready-made website for GitHub Pages, including local photos and animations. Only this folder is hosted.
- **source/**: editable content, HTML generator, CSS, JavaScript, verification scripts and WordPress theme code. It is retained in GitHub but not published as website pages.
- **build_preview.py**: rebuilds the source and exports relative navigation, image and dynamic planner links for GitHub Pages project URLs.
- **source/README.md**: full WordPress installation and content-editing notes.
- **source/CONTENT-AUDIT.md**: factual sources, imagery context and design references.

The website contains 23 content pages, three services, eight advice guides and seven local WebP images. It has no shop or product catalogue. Inspiration images are not claimed as completed FBS projects.

## Updating the preview later

The quickest method is to ask for an updated GitHub Pages package and upload its **docs** folder to the same repository, then commit. Pages republishes the changes. When a page is removed or renamed, remove its old folder from GitHub too; the web uploader does not delete obsolete files automatically.

For a developer: edit **source/content/site.json**, **source/content/guides.json**, **source/content/images.json** and the files under **source/assets/**. From this repository folder run:

```bash
python3 -m pip install -r source/requirements.txt
python3 build_preview.py
cd source
node verify_interactions.cjs
node verify_motion.cjs
cd ..
```

To set metadata to the actual preview address:

```bash
python3 build_preview.py --origin https://YOUR-GITHUB-USERNAME.github.io/fbs-flooring-preview
```

Commit both source changes and the regenerated **docs/** files. Renaming the repository does not break the generated relative navigation or image links. The site stays **noindex** for client review; this is not password protection.

## Preview limitations and permanent hosting

The enquiry builder prepares an email or WhatsApp message. The visitor reviews and sends it in that app. GitHub Pages does not run the supplied WordPress PHP theme or provide a form inbox. No booking is confirmed automatically.

For the business owner's ongoing visual editing, the recommended handover remains a managed WordPress installation using the supplied theme. GitHub Pages is suitable for this static client preview and code delivery. The theme still needs an installation test in WordPress before launch.

The exported pages use the business domain for canonical metadata by default and remain noindex. You can set the actual preview origin using the command above. No Git credentials, Sites hosting configuration, account tokens or internal source snapshots are included.

Official setup references:

- https://docs.github.com/en/pages/getting-started-with-github-pages/configuring-a-publishing-source-for-your-github-pages-site
- https://docs.github.com/en/pages/getting-started-with-github-pages/what-is-github-pages
- https://docs.github.com/en/repositories/working-with-files/managing-files/adding-a-file-to-a-repository
