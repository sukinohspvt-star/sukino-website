#!/usr/bin/env python3
"""
Generates content/demo-content.xml — a standard WordPress eXtended RSS
(WXR) file that, when imported via Tools > Import > WordPress on a fresh
install (with the Sukino Elementor theme + Elementor + Advanced Custom
Fields active), creates:

  * Pages fully designed in Elementor (Home, About, Services, Centres,
    Our Team, International Patients & Family, Contact) — every one of
    them opens straight into the Elementor editor, ready to tweak.
  * Sample entries for every custom post type the theme registers
    (Services, Centres, Team Members, Testimonials, FAQs) so the
    [sukino_*] shortcodes used on those pages render real content
    immediately instead of "add content in wp-admin" placeholders.

Re-run this script any time the page layouts or sample content need to
change, then re-import (or copy/paste the regenerated XML) — it is the
source of truth for content/demo-content.xml, not something to hand-edit.

Usage: python3 tools/generate-demo-content.py
"""

import datetime
import json
import random
import string
import xml.sax.saxutils as sx

RNG = random.Random(42)  # deterministic ids across regenerations


def eid():
    return "".join(RNG.choice(string.hexdigits.lower()[:16]) for _ in range(7))


# ---------------------------------------------------------------------------
# Elementor element builders
# ---------------------------------------------------------------------------

def widget(widget_type, settings=None):
    return {
        "id": eid(),
        "elType": "widget",
        "settings": settings or {},
        "elements": [],
        "widgetType": widget_type,
    }


def column(elements, size=100, settings=None):
    s = {"_column_size": size, "_inline_size": None}
    if settings:
        s.update(settings)
    return {
        "id": eid(),
        "elType": "column",
        "settings": s,
        "elements": elements,
        "isInner": False,
    }


def columns(*col_specs):
    """col_specs: list of (size, [elements]) tuples."""
    return [column(elements, size) for size, elements in col_specs]


def section(cols, settings=None, css_id=None):
    s = dict(settings or {})
    if css_id:
        s["_element_id"] = css_id
    return {
        "id": eid(),
        "elType": "section",
        "settings": s,
        "elements": cols,
        "isInner": False,
    }


def heading(text, size="h2", align=None, color=None, tag="h2"):
    settings = {"title": text, "header_size": tag}
    if align:
        settings["align"] = align
    if color:
        settings["title_color"] = color
    return widget("heading", settings)


def text_editor(html, align=None):
    settings = {"editor": html}
    if align:
        settings["align"] = align
    return widget("text-editor", settings)


def button(text, url, align=None, style="primary"):
    settings = {
        "text": text,
        "link": {"url": url, "is_external": "" if url.startswith("#") or url.startswith("/") else "true", "nofollow": ""},
        "button_type": style,
    }
    if align:
        settings["align"] = align
    return widget("button", settings)


def shortcode(sc):
    return widget("shortcode", {"shortcode": sc})


def spacer(space=40):
    return widget("spacer", {"space": {"unit": "px", "size": space}})


def icon_box(icon_char, title, text):
    return widget("icon-box", {
        "title_text": title,
        "description_text": text,
        "selected_icon": {"value": "", "library": ""},
        "icon_text": icon_char,
    })


def divider():
    return widget("divider", {})


# ---------------------------------------------------------------------------
# Page content trees
# ---------------------------------------------------------------------------

BRAND_DARK = "#0B3D3A"
BRAND_PRIMARY = "#0F7D6C"


def hero_section(eyebrow, title, subtitle, primary_cta, secondary_cta=None, bg=BRAND_DARK):
    els = [
        text_editor(f"<p style='color:#9fd8cc;letter-spacing:.08em;text-transform:uppercase;font-weight:600;'>{eyebrow}</p>"),
        heading(title, tag="h1", color="#ffffff"),
        text_editor(f"<p style='color:#dce9e6;font-size:18px;max-width:640px;'>{subtitle}</p>"),
    ]
    buttons_row = [button(primary_cta[0], primary_cta[1])]
    if secondary_cta:
        buttons_row.append(spacer(16))
        buttons_row.append(button(secondary_cta[0], secondary_cta[1], style="info"))
    els.extend(buttons_row)
    return section(
        columns((100, els)),
        settings={
            "background_background": "classic",
            "background_color": bg,
            "padding": {"unit": "px", "top": "100", "bottom": "100", "left": "0", "right": "0"},
        },
    )


def stats_row(stats):
    return section(
        columns(*[(100 // len(stats), [shortcode(f'[sukino_stat number="{n}" label="{l}"]')]) for n, l in stats]),
        settings={"background_background": "classic", "background_color": "#F4F9F8", "padding": {"unit": "px", "top": "48", "bottom": "48", "left": "0", "right": "0"}},
    )


def heading_intro_section(eyebrow, title, text, align="center"):
    return section(
        columns((100, [
            text_editor(f"<p style='color:{BRAND_PRIMARY};letter-spacing:.08em;text-transform:uppercase;font-weight:600;text-align:{align};'>{eyebrow}</p>"),
            heading(title, align=align),
            text_editor(f"<p style='text-align:{align};max-width:760px;margin:0 auto;color:#55665f;'>{text}</p>"),
        ])),
        settings={"padding": {"unit": "px", "top": "64", "bottom": "16", "left": "0", "right": "0"}},
    )


def build_home_page():
    sections = [
        hero_section(
            "Post-Hospital Rehab · Home Nursing · Palliative Care",
            "India's First Continuum Care Provider",
            "Sukino guides patients and families through every step of recovery — from hospital discharge to rehabilitation at home — across Bangalore, Kochi and Coimbatore, and now for families arriving from around the world.",
            ("Explore Our Services", "/services/"),
            ("International Patients & Family", "/international-patients-family/"),
        ),
        stats_row([("10+", "Years of Continuum Care"), ("3", "Cities Across South India"), ("20+", "Countries Served"), ("24/7", "Clinical Support")]),
        heading_intro_section(
            "What We Do",
            "A Seamless Bridge Between Hospital and Home",
            "From post-surgical rehabilitation to home healthcare, palliative care and end-of-life support, our interdisciplinary teams design a single continuous care plan — so nothing is lost in the handover.",
        ),
        section(columns((100, [shortcode('[sukino_services limit="4"]')]))),
        heading_intro_section(
            "Why Families Choose Sukino",
            "Clinical Expertise, Delivered with Empathy",
            "",
        ),
        section(columns(
            (33, [icon_box("★", "Continuum of Care", "One care plan follows the patient from hospital bed to home recovery — no gaps, no repeated paperwork.")]),
            (33, [icon_box("👩‍⚕️", "Expert Clinical Team", "Rehabilitation physicians, palliative specialists, nurses and therapists working as one interdisciplinary team.")]),
            (34, [icon_box("🏡", "Family-Centred Support", "We treat the whole family — with counselling, training and 24/7 support for caregivers at home.")]),
        )),
        heading_intro_section("Our Centres", "Rehabilitation & Care Centres Across South India", ""),
        section(columns((100, [shortcode('[sukino_locations columns="4"]')]))),
        heading_intro_section("Stories of Recovery", "What Patients & Families Say", ""),
        section(columns((100, [shortcode('[sukino_testimonials limit="3" columns="3"]')]))),
        section(
            columns((100, [
                heading("Bringing Your Loved One to India for Care?", align="center", color="#ffffff"),
                text_editor("<p style='text-align:center;color:#dce9e6;max-width:680px;margin:0 auto;'>Our International Patients & Family desk coordinates medical visas, travel, accommodation and multilingual care coordinators — so you can focus on your loved one, not logistics.</p>"),
                button("Plan Your Care Journey", "/international-patients-family/", align="center"),
            ])),
            settings={"background_background": "classic", "background_color": BRAND_PRIMARY, "padding": {"unit": "px", "top": "72", "bottom": "72", "left": "0", "right": "0"}},
        ),
    ]
    return sections


def build_about_page():
    return [
        hero_section(
            "About Sukino Healthcare",
            "India's First Continuum Care Provider",
            "Sukino was founded to close the most dangerous gap in healthcare — the one between hospital discharge and full recovery at home.",
            ("Meet Our Team", "/our-team/"),
        ),
        heading_intro_section("Our Story", "Care That Continues Where the Hospital Stops", ""),
        section(columns((100, [
            text_editor(
                "<p>Most patients leave hospital only partly recovered — and are left to navigate physiotherapy, nursing, medication and emotional recovery on their own. Sukino was built to change that.</p>"
                "<p>As India's first continuum-of-care provider, we combine inpatient rehabilitation centres, home healthcare, palliative care and end-of-life support into a single, uninterrupted care journey — backed by an interdisciplinary team of rehabilitation physicians, palliative specialists, nurses, physiotherapists, occupational therapists, psychologists and nutritionists.</p>"
                "<p>Today, families across Bangalore, Kochi and Coimbatore — and increasingly from overseas — trust Sukino to manage recovery with the same clinical rigour as a hospital, and the warmth of home.</p>"
            )
        ]))),
        section(columns(
            (50, [heading("Our Mission", tag="h3"), text_editor("<p>To make world-class continuum care accessible to every family navigating a health crisis — whoever and wherever they are.</p>")]),
            (50, [heading("Our Vision", tag="h3"), text_editor("<p>A world where recovery doesn't end at hospital discharge, and no family faces it alone.</p>")]),
        )),
        heading_intro_section("Leadership & Clinical Team", "The People Behind Your Care", ""),
        section(columns((100, [shortcode('[sukino_team]')]))),
        stats_row([("10+", "Years of Experience"), ("3", "Cities"), ("50+", "Clinical Specialists"), ("20+", "Countries Served")]),
    ]


def build_services_page():
    return [
        hero_section(
            "Our Services",
            "A Full Continuum of Care",
            "Every service is designed to connect seamlessly with the next — from the hospital bed to full recovery at home.",
            ("Talk to a Care Coordinator", "/contact-us/"),
        ),
        section(columns((100, [shortcode('[sukino_services columns="2"]')]))),
        heading_intro_section("Not Sure Where to Start?", "Every Care Plan Begins with a Conversation", "Share your medical reports and our clinical team will recommend the right service and centre for your situation — usually within one business day."),
        section(columns((100, [button("Enquire Now", "/contact-us/", align="center")]))),
    ]


def build_locations_page():
    return [
        hero_section(
            "Our Centres",
            "Rehabilitation & Care Centres Across South India",
            "State-of-the-art inpatient rehabilitation centres and home-healthcare hubs in Bangalore, Kochi and Coimbatore.",
            ("Get Directions & Contact a Centre", "#centres"),
        ),
        section(columns((100, [shortcode('[sukino_locations columns="2"]')])), css_id="centres"),
        heading_intro_section("Travelling from Out of Town or Overseas?", "We Coordinate Your Entire Stay", "Our International Patients & Family team arranges airport pickup, accommodation and interpreter support for every centre."),
        section(columns((100, [button("International Patients & Family", "/international-patients-family/", align="center")]))),
    ]


def build_team_page():
    return [
        hero_section(
            "Our Team",
            "Meet the People Behind Your Care",
            "An interdisciplinary team of rehabilitation physicians, palliative specialists, nurses, therapists and care coordinators.",
            ("Contact Us", "/contact-us/"),
        ),
        section(columns((100, [shortcode('[sukino_team]')]))),
    ]


def build_contact_page():
    return [
        hero_section(
            "Contact Us",
            "Talk to Our Care Team",
            "Whether you have a question about a service, a centre, or need to speak to someone urgently — we're here.",
            ("Call Us", "tel:+918047184718"),
        ),
        section(columns(
            (50, [shortcode('[sukino_international_patient_form heading="Send Us a Message"]')]),
            (50, [
                heading("Our Centres", tag="h3"),
                shortcode('[sukino_locations columns="1"]'),
            ]),
        )),
    ]


def build_international_patients_page():
    sections = [
        hero_section(
            "International Patients & Family",
            "Bringing Your Loved One to India, Handled End to End",
            "From your first enquiry to the flight home, Sukino's International Patients & Family desk coordinates medical visas, travel, accommodation, interpreters and a personalised continuum-of-care plan — in your language, on your timezone.",
            ("Start Your Enquiry", "#sukino-enquiry-form"),
            ("Chat on WhatsApp", "https://wa.me/919591945233"),
        ),
        stats_row([
            ("20+", "Countries Served"),
            ("< 24 hrs", "Average First Response"),
            ("10+", "Languages Supported"),
            ("3", "Care Centres in South India"),
        ]),
        heading_intro_section(
            "Why International Families Choose Sukino",
            "Continuum Care, Built for Families Arriving from Abroad",
            "Coordinating recovery care from another country is stressful enough without navigating hospitals, visas and logistics alone. Here's how we remove that burden.",
        ),
        section(columns(
            (33, [icon_box("🌍", "Multilingual Care Coordinators", "Dedicated coordinators fluent in English, Arabic, French and more guide your family through every step.")]),
            (33, [icon_box("🛂", "Visa & Travel Assistance", "We provide medical invitation letters and guidance for medical visas, plus help arranging flights and airport pickup.")]),
            (34, [icon_box("🏨", "Family Accommodation", "Partnered guest houses and serviced apartments near every centre, matched to your family's budget and needs.")]),
        )),
        section(columns(
            (33, [icon_box("💬", "Transparent Pricing", "A detailed cost estimate before you travel — no surprise bills once treatment begins.")]),
            (33, [icon_box("📞", "Tele-Follow-Up After You Return", "Recovery doesn't stop at the airport — our clinicians stay connected by video call after you're home.")]),
            (34, [icon_box("🕌", "Cultural & Dietary Care", "Halal and other dietary accommodations, prayer space access, and culturally aware nursing staff.")]),
        )),
        heading_intro_section("Your Care Journey", "Six Steps, One Dedicated Coordinator", "You'll have a single point of contact from your very first message until your family is safely back home."),
        section(columns(
            (33, [icon_box("1", "Enquiry & Medical Records Review", "Share your medical reports through our secure form; our clinical team reviews them within one business day.")]),
            (33, [icon_box("2", "Personalised Care Plan & Estimate", "Receive a recommended treatment plan, centre, timeline and transparent cost estimate.")]),
            (34, [icon_box("3", "Visa, Travel & Accommodation", "We help with medical visa documentation, flight guidance, and book family accommodation near the centre.")]),
        )),
        section(columns(
            (33, [icon_box("4", "Airport Pickup & Admission", "Our team greets your family at the airport and manages a smooth admission at the centre.")]),
            (33, [icon_box("5", "Treatment, Rehab & Family Support", "Clinical care proceeds alongside caregiver training, counselling and interpreter support for your family.")]),
            (34, [icon_box("6", "Discharge & Tele-Follow-Up", "A discharge plan travels home with you, followed by scheduled video check-ins with your care team.")]),
        )),
        heading_intro_section("Care Available to International Patients", "The Full Continuum, from Day One", ""),
        section(columns((100, [shortcode('[sukino_services]')]))),
        heading_intro_section("Accommodation & Family Support", "You Won't Navigate This Alone", ""),
        section(columns((100, [
            text_editor(
                "<p>Recovery is a family journey, and travelling to another country for care shouldn't mean travelling alone. Our International Patients & Family desk arranges:</p>"
                "<ul>"
                "<li>Partnered guest houses and serviced apartments within minutes of each centre</li>"
                "<li>Airport pickup and local transport for the duration of treatment</li>"
                "<li>Professional interpreters for consultations and daily care updates</li>"
                "<li>A dedicated family lounge and orientation session on arrival</li>"
                "<li>Local SIM cards, currency guidance and everyday logistics support</li>"
                "<li>Halal and other dietary accommodations on request</li>"
                "</ul>"
            )
        ]))),
        heading_intro_section("Our Centres", "Care Available Across South India", ""),
        section(columns((100, [shortcode('[sukino_locations]')]))),
        heading_intro_section("Families Who Trusted Us from Abroad", "Stories from International Families", ""),
        section(columns((100, [shortcode('[sukino_testimonials international_only="true" columns="2"]')]))),
        heading_intro_section("Frequently Asked Questions", "International Patients & Family FAQs", ""),
        section(columns((100, [shortcode('[sukino_faqs category="international-patients"]')]))),
        section(
            columns((100, [
                heading("Start Your Enquiry", align="center"),
                text_editor("<p style='text-align:center;color:#55665f;max-width:680px;margin:0 auto 24px;'>Tell us about the patient's condition and travel timeline — our International Patients desk will respond within one business day.</p>"),
                shortcode('[sukino_international_patient_form]'),
            ])),
            settings={"background_background": "classic", "background_color": "#F4F9F8", "padding": {"unit": "px", "top": "64", "bottom": "80", "left": "0", "right": "0"}},
            css_id="sukino-enquiry-form",
        ),
    ]
    return sections


# ---------------------------------------------------------------------------
# WXR assembly
# ---------------------------------------------------------------------------

SITE_URL = "https://www.sukino.com"
NOW = datetime.datetime(2026, 9, 4, 9, 0, 0)
PUB_DATE = NOW.strftime("%a, %d %b %Y %H:%M:%S +0000")
POST_DATE = NOW.strftime("%Y-%m-%d %H:%M:%S")
POST_DATE_GMT = POST_DATE

_id_counter = [100]


def next_id():
    _id_counter[0] += 1
    return _id_counter[0]


def esc(text):
    return sx.escape(str(text))


def cdata(text):
    return f"<![CDATA[{text}]]>"


def elementor_meta(elements):
    data_json = json.dumps(elements, separators=(",", ":"))
    return [
        ("_elementor_edit_mode", "builder"),
        ("_elementor_template_type", "wp-page"),
        ("_elementor_version", "3.24.0"),
        ("_elementor_data", data_json),
    ]


def page_item(title, slug, elements, menu_order=0, fallback_text=""):
    post_id = next_id()
    meta = elementor_meta(elements)
    meta_xml = "\n".join(
        f"\t\t\t<wp:postmeta><wp:meta_key>{esc(k)}</wp:meta_key><wp:meta_value>{cdata(v)}</wp:meta_value></wp:postmeta>"
        for k, v in meta
    )
    return f"""\t\t<item>
\t\t\t<title>{esc(title)}</title>
\t\t\t<link>{SITE_URL}/{slug}/</link>
\t\t\t<pubDate>{PUB_DATE}</pubDate>
\t\t\t<dc:creator><![CDATA[admin]]></dc:creator>
\t\t\t<guid isPermaLink="false">{SITE_URL}/?page_id={post_id}</guid>
\t\t\t<description></description>
\t\t\t<content:encoded>{cdata(fallback_text)}</content:encoded>
\t\t\t<excerpt:encoded>{cdata('')}</excerpt:encoded>
\t\t\t<wp:post_id>{post_id}</wp:post_id>
\t\t\t<wp:post_date>{POST_DATE}</wp:post_date>
\t\t\t<wp:post_date_gmt>{POST_DATE_GMT}</wp:post_date_gmt>
\t\t\t<wp:comment_status>closed</wp:comment_status>
\t\t\t<wp:ping_status>closed</wp:ping_status>
\t\t\t<wp:post_name>{esc(slug)}</wp:post_name>
\t\t\t<wp:status>publish</wp:status>
\t\t\t<wp:post_parent>0</wp:post_parent>
\t\t\t<wp:menu_order>{menu_order}</wp:menu_order>
\t\t\t<wp:post_type>page</wp:post_type>
\t\t\t<wp:post_password></wp:post_password>
\t\t\t<wp:is_sticky>0</wp:is_sticky>
{meta_xml}
\t\t</item>"""


CPT_REWRITE_SLUG = {
    "sukino_service": "service",
    "sukino_location": "centre",
    "sukino_team_member": "team",
    "sukino_testimonial": "testimonial",
    "sukino_faq": "faq",
}


def cpt_item(post_type, title, slug, content, meta=None, thumbnail=False, categories=None):
    post_id = next_id()
    meta = meta or {}
    meta_lines = []
    for k, v in meta.items():
        if isinstance(v, list):
            # repeater: key_features -> field_service_key_features style handled by caller
            continue
        meta_lines.append(
            f"\t\t\t<wp:postmeta><wp:meta_key>{esc(k)}</wp:meta_key><wp:meta_value>{cdata(v)}</wp:meta_value></wp:postmeta>"
        )
    cat_lines = []
    for domain, name in (categories or []):
        cat_lines.append(
            f'\t\t\t<category domain="{esc(domain)}" nicename="{esc(slug_for_term(name))}">{cdata(name)}</category>'
        )
    rewrite_slug = CPT_REWRITE_SLUG.get(post_type, post_type)
    return f"""\t\t<item>
\t\t\t<title>{esc(title)}</title>
\t\t\t<link>{SITE_URL}/{rewrite_slug}/{slug}/</link>
\t\t\t<pubDate>{PUB_DATE}</pubDate>
\t\t\t<dc:creator><![CDATA[admin]]></dc:creator>
\t\t\t<guid isPermaLink="false">{SITE_URL}/?post_type={post_type}&#38;p={post_id}</guid>
\t\t\t<description></description>
\t\t\t<content:encoded>{cdata(content)}</content:encoded>
\t\t\t<excerpt:encoded>{cdata('')}</excerpt:encoded>
\t\t\t<wp:post_id>{post_id}</wp:post_id>
\t\t\t<wp:post_date>{POST_DATE}</wp:post_date>
\t\t\t<wp:post_date_gmt>{POST_DATE_GMT}</wp:post_date_gmt>
\t\t\t<wp:comment_status>closed</wp:comment_status>
\t\t\t<wp:ping_status>closed</wp:ping_status>
\t\t\t<wp:post_name>{esc(slug)}</wp:post_name>
\t\t\t<wp:status>publish</wp:status>
\t\t\t<wp:post_parent>0</wp:post_parent>
\t\t\t<wp:menu_order>0</wp:menu_order>
\t\t\t<wp:post_type>{post_type}</wp:post_type>
\t\t\t<wp:post_password></wp:post_password>
\t\t\t<wp:is_sticky>0</wp:is_sticky>
{chr(10).join(cat_lines)}
{chr(10).join(meta_lines)}
\t\t</item>"""


def slug_for_term(name):
    return name.lower().replace(" ", "-").replace(",", "").replace("&", "and")


def nav_menu_items(menu_slug, menu_name, links):
    """links: list of (title, url_or_slug) — internal slugs resolved to page links."""
    term_id = next_id()
    xml = [f"""\t\t<wp:term>
\t\t\t<wp:term_id>{term_id}</wp:term_id>
\t\t\t<wp:term_taxonomy>nav_menu</wp:term_taxonomy>
\t\t\t<wp:term_slug>{esc(menu_slug)}</wp:term_slug>
\t\t\t<wp:term_name><![CDATA[{menu_name}]]></wp:term_name>
\t\t</wp:term>"""]
    items = []
    for order, (title, url) in enumerate(links, start=1):
        item_id = next_id()
        items.append(f"""\t\t<item>
\t\t\t<title>{esc(title)}</title>
\t\t\t<link>{esc(url)}</link>
\t\t\t<guid isPermaLink="false">{SITE_URL}/?p={item_id}</guid>
\t\t\t<pubDate>{PUB_DATE}</pubDate>
\t\t\t<dc:creator><![CDATA[admin]]></dc:creator>
\t\t\t<content:encoded><![CDATA[]]></content:encoded>
\t\t\t<excerpt:encoded><![CDATA[]]></excerpt:encoded>
\t\t\t<wp:post_id>{item_id}</wp:post_id>
\t\t\t<wp:post_date>{POST_DATE}</wp:post_date>
\t\t\t<wp:post_date_gmt>{POST_DATE_GMT}</wp:post_date_gmt>
\t\t\t<wp:comment_status>closed</wp:comment_status>
\t\t\t<wp:ping_status>closed</wp:ping_status>
\t\t\t<wp:post_name>menu-item-{item_id}</wp:post_name>
\t\t\t<wp:status>publish</wp:status>
\t\t\t<wp:post_parent>0</wp:post_parent>
\t\t\t<wp:menu_order>{order}</wp:menu_order>
\t\t\t<wp:post_type>nav_menu_item</wp:post_type>
\t\t\t<wp:post_password></wp:post_password>
\t\t\t<wp:is_sticky>0</wp:is_sticky>
\t\t\t<category domain="nav_menu" nicename="{esc(menu_slug)}">{cdata(menu_name)}</category>
\t\t\t<wp:postmeta><wp:meta_key>_menu_item_type</wp:meta_key><wp:meta_value>{cdata('custom')}</wp:meta_value></wp:postmeta>
\t\t\t<wp:postmeta><wp:meta_key>_menu_item_object</wp:meta_key><wp:meta_value>{cdata('custom')}</wp:meta_value></wp:postmeta>
\t\t\t<wp:postmeta><wp:meta_key>_menu_item_url</wp:meta_key><wp:meta_value>{cdata(url)}</wp:meta_value></wp:postmeta>
\t\t\t<wp:postmeta><wp:meta_key>_menu_item_menu_item_parent</wp:meta_key><wp:meta_value>{cdata('0')}</wp:meta_value></wp:postmeta>
\t\t</item>""")
    return xml[0], items


def build():
    items = []

    items.append(page_item("Home", "home", build_home_page(), fallback_text="Welcome to Sukino Healthcare."))
    items.append(page_item("About Sukino Healthcare", "about-sukino-healthcare", build_about_page()))
    items.append(page_item("Services", "services", build_services_page()))
    items.append(page_item("Our Centres", "locations", build_locations_page()))
    items.append(page_item("Our Team", "our-team", build_team_page()))
    items.append(page_item("International Patients & Family", "international-patients-family", build_international_patients_page()))
    items.append(page_item("Contact Us", "contact-us", build_contact_page()))

    # --- Services CPT ---
    services = [
        ("Post-Hospital Rehabilitation", "post-hospital-rehabilitation",
         "Personalised inpatient and outpatient rehabilitation for neurological, orthopaedic and post-surgical recovery, led by rehabilitation physicians and therapists.",
         "Rehabilitation"),
        ("Home Healthcare", "home-healthcare",
         "Skilled nursing, physiotherapy and trained caregiver support delivered at home for post-operative recovery and chronic condition management.",
         "Home Healthcare"),
        ("Palliative Care", "palliative-care",
         "Compassionate, 24/7 comfort-focused care for patients with serious illness, supporting both pain management and quality of life.",
         "Palliative Care"),
        ("End-of-Life Care", "end-of-life-care",
         "Dignified, family-supported care focused on comfort, emotional wellbeing and quality time in the final stages of life.",
         "Palliative Care"),
    ]
    for title, slug, desc, cat in services:
        meta = {
            "short_description": desc,
            "cta_label": "Learn More",
        }
        items.append(cpt_item("sukino_service", title, slug, f"<p>{desc}</p>", meta, categories=[("service_category", cat)]))

    # --- Centres (locations) CPT ---
    locations = [
        ("Koramangala, Bangalore", "koramangala-bangalore",
         "Sukino Inpatient Rehab Centre, 4th Block, Koramangala, Bengaluru, Karnataka",
         "+91 80 4718 4718", "care@sukino.com", "Bangalore", True),
        ("Whitefield, Bangalore", "whitefield-bangalore",
         "Sukino Advanced Recovery Care, Whitefield, Bengaluru, Karnataka",
         "+91 80 4718 4719", "care@sukino.com", "Bangalore", False),
        ("Kochi", "kochi",
         "Sukino Home Healthcare & Palliative Care, Kochi, Kerala",
         "+91 484 471 8471", "kochi@sukino.com", "Kochi", False),
        ("Coimbatore", "coimbatore",
         "Sukino Home Healthcare & Palliative Care, Coimbatore, Tamil Nadu",
         "+91 422 471 8471", "coimbatore@sukino.com", "Coimbatore", False),
    ]
    for title, slug, address, phone, email, city, flagship in locations:
        meta = {
            "address": address,
            "city": city,
            "phone": phone,
            "email": email,
            "working_hours": "24/7",
            "is_flagship": "1" if flagship else "0",
        }
        items.append(cpt_item("sukino_location", title, slug, f"<p>{address}</p>", meta, categories=[("location_city", city)]))

    # --- Team members CPT ---
    team = [
        ("Dr. Anjali Rao", "dr-anjali-rao", "Medical Director, Rehabilitation Medicine",
         "MD, DNB (Physical Medicine & Rehabilitation)", "rehabilitation", "English, Hindi, Kannada",
         "Dr. Rao leads Sukino's rehabilitation programme, overseeing personalised recovery plans for neurological and orthopaedic patients."),
        ("Dr. Thomas Mathew", "dr-thomas-mathew", "Head of Palliative Care",
         "MD (Palliative Medicine), Fellowship in Pain Management", "palliative-care", "English, Malayalam, Hindi",
         "Dr. Mathew has spent over a decade building palliative and end-of-life care programmes across South India."),
        ("Sarah Fernandes", "sarah-fernandes", "International Patient Care Coordinator",
         "MBA Healthcare Management", "administration", "English, Arabic, French",
         "Sarah is the first point of contact for families arriving from overseas, coordinating visas, travel and accommodation."),
    ]
    for title, slug, designation, quals, dept, langs, bio in team:
        meta = {
            "designation": designation,
            "qualifications": quals,
            "department": dept,
            "languages_spoken": langs,
            "bio_short": bio,
        }
        items.append(cpt_item("sukino_team_member", title, slug, f"<p>{bio}</p>", meta))

    # --- Testimonials CPT ---
    testimonials = [
        ("Priya M.", "priya-m", "Daughter of patient", "India", False, 5,
         "Sukino's team managed my father's entire recovery after his stroke — from the hospital handover to physiotherapy at home. We never felt alone."),
        ("Ramesh K.", "ramesh-k", "Son of patient", "India", False, 5,
         "The palliative care team gave our family dignity and comfort during the hardest months of our lives. Forever grateful."),
        ("Fatima Al-Sayed", "fatima-al-sayed", "Daughter of patient", "UAE", True, 5,
         "We flew in from Dubai not knowing where to start. Sukino's coordinator arranged everything — visa letters, accommodation, even an Arabic-speaking nurse. My father's recovery has been remarkable."),
        ("James Whitfield", "james-whitfield", "Son of patient", "United Kingdom", True, 5,
         "From our first email to the day we flew home, Sukino's international desk was responsive and transparent about costs. The follow-up video calls after we returned to London meant everything."),
    ]
    for name, slug, relation, country, is_intl, rating, quote in testimonials:
        meta = {
            "patient_name": name,
            "relation": relation,
            "country": country,
            "is_international": "1" if is_intl else "0",
            "rating": str(rating),
        }
        items.append(cpt_item("sukino_testimonial", name, slug, f"<p>{quote}</p>", meta))

    # --- FAQs CPT ---
    faqs = [
        ("What does 'continuum of care' mean?", "what-does-continuum-of-care-mean",
         "It means one care plan follows the patient from hospital discharge through rehabilitation, home healthcare and, if needed, palliative care — instead of separate providers handling each stage.",
         "general"),
        ("Which cities does Sukino operate in?", "which-cities-does-sukino-operate-in",
         "Sukino operates inpatient rehabilitation centres and home healthcare services in Bangalore, Kochi and Coimbatore.",
         "general"),
        ("How do I get started with home healthcare?", "how-do-i-get-started-with-home-healthcare",
         "Share the patient's medical reports through our contact form or by phone, and a care coordinator will recommend a plan within one business day.",
         "general"),
        ("Do you assist with medical visas for India?", "do-you-assist-with-medical-visas-for-india",
         "Yes. Our International Patients & Family desk provides medical invitation letters and guidance on the documentation required for an Indian medical visa.",
         "international-patients"),
        ("Can a family member stay with the patient during treatment?", "can-a-family-member-stay-with-the-patient",
         "Yes, and we encourage it. We help arrange nearby guest house or serviced apartment accommodation and a dedicated family lounge at each centre.",
         "international-patients"),
        ("Do you provide interpreters for non-English speaking families?", "do-you-provide-interpreters",
         "Yes, professional interpreters are available for consultations and daily care updates, and several of our care coordinators are multilingual.",
         "international-patients"),
    ]
    for title, slug, answer, cat in faqs:
        items.append(cpt_item("sukino_faq", title, slug, f"<p>{answer}</p>", categories=[("faq_category", cat)]))

    # --- Primary + footer nav menus ---
    primary_term, primary_items = nav_menu_items("primary-menu", "Primary Menu", [
        ("Home", f"{SITE_URL}/"),
        ("About", f"{SITE_URL}/about-sukino-healthcare/"),
        ("Services", f"{SITE_URL}/services/"),
        ("Our Centres", f"{SITE_URL}/locations/"),
        ("Our Team", f"{SITE_URL}/our-team/"),
        ("International Patients & Family", f"{SITE_URL}/international-patients-family/"),
        ("Contact", f"{SITE_URL}/contact-us/"),
    ])
    footer_term, footer_items = nav_menu_items("footer-menu", "Footer Menu", [
        ("Privacy Policy", f"{SITE_URL}/privacy-policy/"),
        ("Contact", f"{SITE_URL}/contact-us/"),
    ])

    all_items = "\n".join(items + primary_items + footer_items)
    all_terms = "\n".join([primary_term, footer_term])

    xml = f"""<?xml version="1.0" encoding="UTF-8"?>
<!-- This is a WordPress eXtended RSS file generated for content transfer. -->
<!-- Generated by tools/generate-demo-content.py — see that file to regenerate. -->
<rss version="2.0"
	xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/1.2/"
>
<channel>
\t<title>Sukino Healthcare</title>
\t<link>{SITE_URL}</link>
\t<description>Post-Hospital Rehab, Home Nursing &amp; Palliative Care</description>
\t<pubDate>{PUB_DATE}</pubDate>
\t<language>en-US</language>
\t<wp:wxr_version>1.2</wp:wxr_version>
\t<wp:base_site_url>{SITE_URL}</wp:base_site_url>
\t<wp:base_blog_url>{SITE_URL}</wp:base_blog_url>
\t<wp:author>
\t\t<wp:author_id>1</wp:author_id>
\t\t<wp:author_login><![CDATA[admin]]></wp:author_login>
\t\t<wp:author_email><![CDATA[care@sukino.com]]></wp:author_email>
\t\t<wp:author_display_name><![CDATA[Sukino Healthcare]]></wp:author_display_name>
\t\t<wp:author_first_name><![CDATA[]]></wp:author_first_name>
\t\t<wp:author_last_name><![CDATA[]]></wp:author_last_name>
\t</wp:author>
{all_terms}
\t<generator>tools/generate-demo-content.py</generator>
{all_items}
</channel>
</rss>
"""
    return xml


if __name__ == "__main__":
    output_path = "content/demo-content.xml"
    xml = build()
    with open(output_path, "w", encoding="utf-8") as f:
        f.write(xml)
    print(f"Wrote {output_path} ({len(xml):,} bytes)")
