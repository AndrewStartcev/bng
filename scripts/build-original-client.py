#!/usr/bin/env python3
"""Restore approved BNG RU/EN HTML, CSS, scripts and binary assets from initial Git history.
Never redesign the original client markup. Requires only Python standard library.
"""
from pathlib import Path
import base64
import gzip
import html
import json
import re
import subprocess

ROOT = Path(__file__).resolve().parents[1]
ORIGINAL_COMMIT = "e06efb51425c"
SECTIONS = dict(Home="home", Products="products", Rock="rock", Flow="flowsheet",
                Evidence="evidence", Milestones="milestones", News="news", Company="company")
EXTENSIONS = {
    "image/svg+xml": ".svg",
    "application/vnd.openxmlformats-officedocument.wordprocessingml.document": ".docx",
    "text/html": ".html",
    "application/javascript": ".js",
    "text/javascript": ".js",
    "font/woff2": ".woff2",
}

def bundled_json(content, kind):
    pattern = r'<script\\s+type=["\\\']__bundler/' + kind + r'["\\\']\\s*>(.*?)</script>'
    match = re.search(pattern, content, re.S)
    if not match:
        raise ValueError("Original standalone has no " + kind)
    return json.loads(html.unescape(match.group(1)))

def extract(locale):
    name = "BNG Elements " + locale.upper() + " - standalone.html"
    source = subprocess.check_output([
        "git", "show", ORIGINAL_COMMIT + ":resourse/" + name
    ], cwd=ROOT).decode("utf-8")
    template = bundled_json(source, "template")
    manifest = bundled_json(source, "manifest")
    external = bundled_json(source, "ext_resources")
    is_en = locale == "en"
    folder = ROOT / "assets/original"
    if is_en:
        folder /= "en"
    folder.mkdir(parents=True, exist_ok=True)
    urls = {}
    for uid, asset in manifest.items():
        suffix = EXTENSIONS.get(asset["mime"], ".bin")
        binary = base64.b64decode(asset["data"])
        if asset.get("compressed"):
            binary = gzip.decompress(binary)
        (folder / (uid + suffix)).write_bytes(binary)
        urls[uid] = "__BNG_THEME_URI__/assets/original/" + ("en/" if is_en else "") + uid + suffix
    for uid, url in urls.items():
        template = template.replace(uid, url)

    # Only URL resolution + WordPress data hooks change; the client CSS and markup remain untouched.
    template = template.replace("const lang = '" + locale + "';\\n    const path",
                                "const lang = this.state.lang;\\n    const path")
    for domain in ["'https://' + DOMAIN['ru'] + path", "'https://' + DOMAIN['en'] + path",
                   "'https://' + DOMAIN.ru + path", "'https://' + DOMAIN.en + path"]:
        template = template.replace(domain, "window.location.origin + path")

    bridge = """if (window.BNG_WP_COPY) {
  for (const locale of ['ru', 'en']) {
    for (const [section, values] of Object.entries(window.BNG_WP_COPY[locale] || {})) {
      if (COPY[locale] && COPY[locale][section]) Object.assign(COPY[locale][section], values);
    }
  }
}
"""
    anchor = "class Component extends DCLogic {"
    if anchor not in template:
        raise ValueError("Cannot locate client component data hook")
    template = template.replace(anchor, bridge + anchor, 1)
    template = template.replace("</head>", "__BNG_WP_HEAD__\\n</head>", 1)
    template = template.replace("<body>", '<body class="bng-original">\\n__BNG_WP_BRIDGE__', 1)
    template = template.replace("</body>", "__BNG_WP_FOOT__\\n</body>", 1)

    pattern = r'<sc-if\\b[^>]*value="\\{\\{ is(Home|Products|Rock|Flow|Evidence|Milestones|News|Company) \\}\\}"[^>]*>'
    matches = list(re.finditer(pattern, template))
    if len(matches) != 8:
        raise ValueError("Expected eight original client sections, got " + str(len(matches)))
    section_dir = (folder / "sections") if is_en else (ROOT / "template-parts/original")
    section_dir.mkdir(parents=True, exist_ok=True)
    for idx in reversed(range(len(matches))):
        match = matches[idx]
        end = matches[idx + 1].start() if idx + 1 < len(matches) else template.index("</main>", match.end())
        fragment = template[match.start():end]
        last = fragment.rfind("</sc-if>")
        if last < 0:
            raise ValueError("Unclosed original section " + match.group(1))
        last += len("</sc-if>")
        section = SECTIONS[match.group(1)]
        (section_dir / (section + ".html")).write_text(fragment[:last], encoding="utf-8")
        template = template[:match.start()] + "<!--BNG_SECTION_" + section + "-->" + fragment[last:] + template[end:]
    (folder / "page.html").write_text(template, encoding="utf-8")
    resources = {item["id"]: urls[item["uuid"]] for item in external if item.get("uuid") in urls}
    (folder / "resources.json").write_text(json.dumps(resources, ensure_ascii=False, indent=2), encoding="utf-8")
    print(locale, len(manifest), "client assets, 8 screens extracted")

if __name__ == "__main__":
    for language in ("ru", "en"):
        extract(language)
