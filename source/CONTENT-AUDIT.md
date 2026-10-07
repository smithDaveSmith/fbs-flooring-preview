# FBS website — content and design audit

Checked 6 October 2026.

## Business model

The project owner's current instruction is authoritative: **FBS offers services and does not sell flooring.** The existing public website contains conflicting product-sales, distribution and showroom copy. Those claims were not carried into this rebuild. The earlier retail design was incorrect for the current brief.

The rebuilt navigation and pages focus on free home consultation, measuring and advice, professional fitting, new homes and renovations. Residential and commercial fitting are mentioned in the published About page. No sanding, restoration, repairs, retail delivery, samples, prices, warranties or availability guarantees have been added.

## Facts and their origins

| Information | Source | Treatment |
| --- | --- | --- |
| Free home consultations with fitting specialists, assessment, measurement and tailored advice | https://fbsflooring.ie/services/ | Kept as service scope; free consultation is separate from quoted work. |
| New-home projects | https://fbsflooring.ie/services/ | Dedicated project-planning service page. |
| Professional installation; residential and commercial work | https://fbsflooring.ie/about-us/ | General fitting scope. No unverified material-specific installation promises. |
| Owner Ilir Sulaj | https://fbsflooring.ie/about-us/ | About page. |
| Unit J2, Malahide Road Industrial Estate, Coolock, Dublin 17, D17 FR58; +353 83 044 1400; info@fbsflooring.ie | https://fbsflooring.ie/contact-us/ and published footer | Business contact location, not a claimed retail showroom. No opening hours invented. |
| Service-only business model | User's correction in this project | Overrides conflicting retail claims on the old website. |

## Photography

The original gallery includes filenames identifying manufacturer lifestyle photography. It does not provide enough reliable project detail to turn those images into FBS case studies.

The new website bundles a small selected set of existing FBS website images in `assets/`. Image origin URLs and context are recorded in `content/images.json`. Captions describe illustrative interiors and inspiration. No invented project locations, dates, completed-work claims, team portraits or customer testimonials are used.

For launch, the business should verify its image reuse rights and can replace illustrations with confirmed photographs of its own work through the WordPress media editor. Reference-site photographs and logos have not been copied.

## Design references reviewed

| Reference | Pattern used for this services website |
| --- | --- |
| https://int.quick-step.com/en | Clear guidance, room photography and simple next steps. |
| https://www.sekelskifte.com/en | Editorial hierarchy, serif headings and a useful advice section. |
| https://kullabergflooring.se/en/home/ | Calm spacing, large interior imagery and considered storytelling. |
| https://luxuryflooring.co.uk/ | Prominent contact routes, practical help and clear preparation content. |

The design interpretation is original: warm off-white, dark green, restrained terracotta accents, editorial type, three concise service paths and a consistent consultation action. Shopping navigation, checkout, discounts, product filters and retail promises from the references were deliberately excluded from the service brief.

## Advice and migration

The eight articles are newly written practical project-preparation guides. They do not reproduce the old website's full blog archive, product promotions, legal/medical assertions, detailed fitting rates or technical guarantees. Several relevant original guide URLs are retained, while new consultation and measuring guides have their own URLs. The old article archive remains on the original business domain; it was not deleted.

The review website publishes 23 content pages and five compatibility redirects. Old retail detail URLs are not published. Before a real-domain migration, review each old URL and its relevance; do not automatically redirect unrelated product pages to a generic service page.

## Motion references — 6 October 2026

Bright Avenue (https://bright-avenue.jp/) was reviewed in the browser for its layered typography, large imagery and entrance treatment. Eugenia Grab (https://www.eugeniagrab.com/en) was not readable through web retrieval, but its publicly served application script was inspected: it contains masked image reveals, scroll-triggered parallax and eased pointer interactions. No reference code or assets have been copied into FBS.

FBS uses original native-browser animation code, with a short headline entrance, once-only section/image reveals, staggered cards and a maximum 16px image drift on desktop with a fine pointer. Reduced motion cancels active animations and removes parallax; focus makes animated controls immediately available. Static content remains readable when JavaScript or animation support is unavailable.
