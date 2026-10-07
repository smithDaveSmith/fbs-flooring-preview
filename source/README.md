# FBS Flooring — services website

This replaces the earlier retail design. FBS currently offers services, as confirmed by the project brief: consultation, measuring and advice, professional fitting, new homes and renovations. There is no product catalogue, pricing, shopping basket or ordering.

## Motion update

The website includes masked headline entrances, image reveals, staggered service/advice cards, gentle desktop image parallax, hover movement and a reading-progress line. Motion stops when the visitor enables reduced motion; mobile skips parallax. Scrolling and navigation remain native. There is no loading gate or continuous animation loop. All motion is bundled in the supplied code and WordPress theme.

## What is included

- A complete services website: 23 connected pages, three service detail pages, eight original preparation guides, contact details, FAQs, an inspiration gallery and a room-area planner.
- Seven local WebP pictures, with original FBS image URLs as fallbacks. Inspiration pictures are not represented as completed FBS work.
- All HTML, CSS, JavaScript, editable JSON content, build scripts and WordPress theme source.
- A standalone installable WordPress theme ZIP with a simple owner dashboard. No commerce plugin is needed.

## Recommended permanent setup

For an owner who does not code, use **managed WordPress hosting on the business's own domain**. This is the project recommendation because it provides a visual editor, familiar photo uploads and full access to the theme, database and backups. Choose a hosting account owned by the business, with automatic backups and a staging copy.

Install the included theme on a staging copy first. Do not replace the existing live business website until the owner has reviewed the services, contact details, pictures and enquiries. This theme has not yet been installed and checked in a live WordPress environment.

The public review link is a separate hosted copy. It is accessible without ChatGPT sign-in. The current fbsflooring.ie website and its DNS have not been changed. A permanent move to another host requires access to that hosting account and the domain.

## Download and use the website

The `website/` folder is the ready-made static website. Upload it to a static hosting account as the public directory. It must be served by a web server because navigation uses root-relative URLs.

For local review, run `python3 -m http.server 8000 --directory website` from the exported folder and open http://localhost:8000. Opening an HTML file directly from the filesystem is not supported.

The static enquiry tool prepares a message and opens email or WhatsApp; it does **not** submit a form to an inbox. Visitors must review and send the message in the selected app. Nothing is sent by clicking “Prepare my enquiry”. No booking is confirmed automatically.

## WordPress setup — for the person installing it

1. Create a staging copy or new WordPress installation. Back it up first.
2. In Appearance → Themes → Add New → Upload Theme, upload `FBS-WordPress-Theme.zip` and activate it.
3. Open **FBS Website** in the dashboard. Click **Import the service website**. It imports services, the eight articles, editable inspiration images and website pages. It keeps existing same-slug content and does not delete products from an existing installation.
4. In Settings → Reading, choose the imported **Home** page as the homepage and **Advice & preparation** as the posts page. The importer sets these only if no pages are already selected.
5. In Settings → Permalinks, choose a readable post-name structure and save. Review every service URL, the blog and contact page. Configure navigation in Appearance → Menus if you want to replace the default links.
6. Test the theme on desktop and mobile. Confirm media uploads work on the server and the enquiry links use the correct business phone/email.
7. Keep the staging site hidden from search engines. For a launch, update canonical URLs, sitemap configuration, page titles and old-URL redirects for the final domain. The static build defaults to noindex to prevent a duplicate review website competing with the business domain.

### Owner editing after setup

- **FBS Website:** update the headline, introduction, top notice, business phone/email/address and homepage photo. Choose a picture, then save.
- **Services:** open a service, edit its title, summary and paragraphs with the visual WordPress block editor, then update.
- **Posts:** edit the preparation guides or add a new one. Choose a category and featured image.
- **Inspiration photos:** add a photo, title and featured image. Only publish completed-work claims when the photo and project details have been verified.
- **Media:** upload new pictures through the standard media library.
- **Pages:** About, How it works, FAQs and the privacy/cookie pages use the visual block editor. Homepage, services and gallery have managed layouts. Contact and the planner contain functional HTML blocks; ask a developer to change their layout or form fields. Business contact details are edited in FBS Website.

For direct website form submissions, the installer can add a WordPress form with configured email delivery. The included enquiry builder remains email/WhatsApp based and does not pretend to have a working backend or calendar booking.

## Developer editing

Inside `source/`, edit `content/site.json`, `content/guides.json`, `content/images.json`, `assets/site.css` and `assets/site.js`.

Install the validation dependency with `python3 -m pip install -r requirements.txt`. Run `python3 build.py`, `python3 validate.py` `node verify_interactions.cjs` and `node verify_motion.cjs`. To package the theme and website again, run `python3 package_handoff.py`.

For production, set `FBS_STAGING=0` and `FBS_SITE_ORIGIN=https://fbsflooring.ie` when building. Confirm the launch domain and URL mapping before changing indexing settings.

The included theme PHP has been syntax-checked. It still needs a WordPress installation test before launch. The static website has passed local route, metadata, image and interaction checks; this environment does not support rendered browser testing for its static Sites format.

See `CONTENT-AUDIT.md` for the factual sources, imagery context and design references.
