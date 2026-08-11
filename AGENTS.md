# AI Agent Instructions – Rezidencia Lomnica MU Plugins

- Zdroj pravdy je GitHub, nie ručné úpravy vo WordPresse.
- Custom PHP, CSS a JS ukladaj iba do mu-plugins/.
- Top-level súbor mu-plugins/rezidencia-lomnica-loader.php načíta moduly z podadresára.
- Každý modul musí mať ABSPATH guard.
- Nepoužívaj functions.php, Fluent Snippets ani Bricks custom code ako produkčný zdroj tejto logiky.
- Zachovávaj existujúce shortcode názvy rs_*, kým nebude dokončená migrácia Bricks šablón; ide o kompatibilitnú vrstvu so Štúrovou.
- Dizajn a obsah upravuj v nových lomnických moduloch, nie kopírovaním stúrovských URL alebo textov.
- Pred úpravou skontroluj loader, relevantný modul a dokumentáciu v docs/.
- Pri PHP výstupe používaj escaping; vstupy sanitizuj; pri AJAX/REST a admin akciách rešpektuj nonce a capability kontroly.
- Do repozitára nikdy neukladaj heslá, tokeny, API kľúče, .env, wp-config.php ani databázové exporty.
- Po zmene spusti PHP lint a kontrolu, že aktívny kód neobsahuje FluentSnippets metadata ani stúrovské produkčné URL.

