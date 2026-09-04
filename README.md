# Sukino Elementor — WordPress Theme & Demo Content

A WordPress theme + demo-content package built for **Sukino Healthcare**
(sukino.com), India's continuum-of-care provider for post-hospital
rehabilitation, home healthcare, palliative care and end-of-life care
across Bangalore, Kochi and Coimbatore.

Every page is 100% editable in **Elementor** (free version — no paid
add-ons required to run the site as shipped). Content that changes
often — Services, Centres, Team Members, Testimonials, FAQs — lives in
WordPress custom post types, not hard-coded into a page design, so
non-technical staff can add or edit it from `wp-admin` and it appears
everywhere it's referenced automatically. The centerpiece is a dedicated
**International Patients & Family** page with a working enquiry form,
multilingual-care messaging, a step-by-step "care journey," and
testimonials filtered to international families.

> Content and structure are inspired by the public sukino.com site
> (services offered, cities served, continuum-of-care positioning). No
> text, images or code were copied from sukino.com — this is original
> content written for this package and meant to be reviewed/replaced
> with the client's actual copy and photography before launch.

## What's in the repo

```
wp-content/themes/sukino-elementor/   # the WordPress theme
  functions.php                       # bootstraps everything below
  inc/
    theme-setup.php                   # menus, sidebars, asset enqueue
    customizer.php                    # phone/WhatsApp/email/social settings
    cpt-service.php                   # "Services" custom post type
    cpt-location.php                  # "Centres" custom post type
    cpt-team-member.php               # "Our Team" custom post type
    cpt-testimonial.php               # "Testimonials" custom post type
    cpt-faq.php                       # "FAQs" custom post type
    cpt-enquiry.php                   # private "IP Enquiries" inbox
    shortcodes.php                    # [sukino_services], [sukino_locations], etc.
    enquiry-form.php                  # International Patient enquiry form + handler
  acf-json/                           # Advanced Custom Fields field groups (auto-synced)
  assets/css/theme.css, assets/js/theme.js
  header.php, footer.php, page.php, index.php, archive.php, single.php, 404.php
content/
  demo-content.xml                    # WordPress eXtended RSS (WXR) import file
tools/
  generate-demo-content.py            # regenerates content/demo-content.xml
```

## Requirements

- WordPress 6.0+, PHP 7.4+
- Plugins (all free, from WordPress.org / Elementor.com):
  - **Elementor** — the page builder itself
  - **Advanced Custom Fields (ACF)** — powers the Service/Centre/Team/Testimonial
    field editing screens in wp-admin (the theme still works without it —
    it falls back to plain text fields — but ACF gives editors a proper UI)
- Recommended for production, not required to run the demo:
  - An SMTP plugin (e.g. **WP Mail SMTP**) so the enquiry-form emails
    reliably reach your inbox instead of relying on the host's default mailer
  - An SEO plugin (e.g. **Yoast SEO** or **Rank Math**)
  - A backup plugin (e.g. **UpdraftPlus**)
  - **Elementor Pro**, if you later want its Theme Builder, Popup Builder,
    or native Forms widget — nothing here depends on it

## Installation

1. **Get a WordPress site running** — any host that gives you file/DB
   access, or a local environment (LocalWP, XAMPP, `wp-env`) for
   development.
2. **Copy the theme.** Copy `wp-content/themes/sukino-elementor/` into
   your WordPress install's `wp-content/themes/` directory.
3. **Install plugins.** In `wp-admin → Plugins → Add New`, install and
   activate **Elementor** and **Advanced Custom Fields**.
4. **Activate the theme.** `wp-admin → Appearance → Themes → Sukino Elementor → Activate`.
   Activation automatically flushes rewrite rules and, if a menu named
   "Primary Menu" already exists (e.g. after importing demo content),
   assigns it to the header navigation.
5. **Import the demo content.**
   - `wp-admin → Tools → Import → WordPress` (install the "WordPress"
     importer if prompted).
   - Upload `content/demo-content.xml`.
   - When asked about authors, map to your own admin user.
   - You do **not** need "Download and import file attachments" checked —
     the demo ships without images so the file stays small; add your own
     logo/photos afterwards (see below).
   - This creates 7 Elementor-built pages (Home, About, Services, Our
     Centres, Our Team, **International Patients & Family**, Contact),
     sample entries for every custom post type, and the Primary/Footer
     navigation menus.
6. **Set the homepage.** `wp-admin → Settings → Reading → Your homepage
   displays → A static page → Homepage: "Home"`.
7. **Check the menus.** `wp-admin → Appearance → Menus` — confirm
   "Primary Menu" is assigned to the **Primary Menu** location and
   "Footer Menu" to **Footer Menu** (step 4 does this automatically in
   most cases; assign manually if not).
8. **Set your contact details.** `wp-admin → Appearance → Customize →
   Sukino Contact & Social` — phone, WhatsApp number, email addresses,
   top-bar announcement text, and social links. These feed the top bar,
   footer and the floating WhatsApp button sitewide.
9. **Open any page in Elementor** — `Pages → All Pages → [page] → Edit
   with Elementor` — and start customizing. Every section, heading, and
   image is a normal Elementor element.
10. **Add real content.** Go to the new admin menu items — **Services**,
    **Centres**, **Our Team**, **Testimonials**, **FAQs** — and add/edit
    entries. Because the pages pull this content through shortcodes
    (`[sukino_services]`, `[sukino_locations]`, `[sukino_team]`,
    `[sukino_testimonials]`, `[sukino_faqs]`), it updates on the live
    pages immediately, no Elementor edit required.
11. **Create a Privacy Policy page** (linked from the footer menu by
    default) — required if you're collecting enquiry-form submissions
    from patients, especially international ones.

## The International Patients & Family page

`International Patients & Family` (`/international-patients-family/`) is
the page requested for this build and the most detailed one in the demo
content. It includes:

- A hero with a WhatsApp click-to-chat CTA and a jump link to the enquiry form
- Stats (countries served, response time, languages, centres)
- "Why international families choose Sukino" (multilingual coordinators,
  visa/travel assistance, family accommodation, transparent pricing,
  tele-follow-up, cultural/dietary care)
- A six-step "care journey" from first enquiry to post-discharge follow-up
- The full services list, centres list, and testimonials filtered to
  international families only (`[sukino_testimonials international_only="true"]`)
- An FAQ accordion filtered to the `international-patients` FAQ category
- A working enquiry form (see below) anchored at `#sukino-enquiry-form`

### How the enquiry form works

`[sukino_international_patient_form]` (already placed on the International
Patients page and reused on the Contact page) renders a plain HTML form —
it works with the **free** Elementor Shortcode widget, no Elementor Pro
Forms needed. On submit it:

1. Verifies a nonce and a hidden honeypot field (spam protection).
2. Sanitizes and validates the required fields.
3. Stores the submission as a private **IP Enquiry** post
   (`wp-admin → IP Enquiries`) so staff always have a record.
4. Emails your International Patients desk (the "International Patients
   Email" Customizer setting) with the full submission.
5. Sends an auto-reply confirmation to the enquirer.

Because it uses `wp_mail()`, install an SMTP plugin in production so
these emails don't land in spam or get silently dropped by your host.

## Regenerating the demo content

`content/demo-content.xml` is generated, not hand-written. If you need
to change a page's Elementor layout or the sample Services/Centres/Team/
Testimonials/FAQ entries, edit `tools/generate-demo-content.py` and
re-run:

```bash
python3 tools/generate-demo-content.py
```

This regenerates `content/demo-content.xml` with the same page slugs and
IDs (a fixed random seed keeps Elementor element IDs stable across runs),
ready to re-import.

## Extending the theme

- **New dynamic content type?** Follow the pattern in `inc/cpt-*.php`
  (register a CPT + optional taxonomy) and `acf-json/` (add a field
  group), then add a shortcode in `inc/shortcodes.php` so it can be
  dropped into any Elementor page.
- **New global setting?** Add it in `inc/customizer.php` and reference it
  with `get_theme_mod( 'your_setting' )` in the templates.
- **Design changes to the header/footer/floating button?** Those are
  the only parts of the site not built in Elementor — edit
  `header.php`, `footer.php` and `assets/css/theme.css` directly.
- Everything else — every page's actual layout and content — is meant to
  be edited by opening the page in Elementor, not by editing PHP.
