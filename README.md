# web_rezidencia-lomnica

Zdroj pravdy pre WordPress / Bricks / ACF / WooCommerce a custom MU-pluginy projektu Rezidencia Lomnica.

## Cieľ migrácie

Projekt preberá funkčnú logiku z PanZlty/web_rezidencia-sturova:

- CPT a ACF dáta bytov,
- synchronizácia ceny/statusu z Google Sheets do WordPressu,
- Image Map Pro tooltipy, statusy, aliasy a object-list pills,
- sklad ako súčasť konkrétneho bytu, nie ako samostatne predávanú jednotku,
- galériu s tlačidlom „Načítať viac“,
- termín výstavby s progress barom,
- dynamický status pill,
- ochranu formulárov a vypnutie komentárov,
- analytiku a frontend CSS/JS korekcie.

Mení sa dizajn, obsah, assety a lomnické WordPress konfigurácie. Aktívny custom code sa nasadzuje výhradne ako MU-plugin.

## Workflow

- GitHub je jediný zdroj pravdy pre custom code.
- mu-plugins/rezidencia-lomnica-loader.php je top-level loader.
- Moduly patria do mu-plugins/rezidencia-lomnica/.
- Push do main pri zmene mu-plugins/** spustí .github/workflows/deploy-mu-plugins.yml.
- Workflow kopíruje MU-plugin súbory cez SFTP do wp-content/mu-plugins/.
- SFTP údaje patria iba do GitHub Secrets: SFTP_HOST, SFTP_PORT, SFTP_USERNAME, SFTP_PASSWORD, SFTP_TARGET.

## Dôležité

Pred nasadením doplň lomnické ID/slugy, ACF konfiguráciu, kontakty, analytický identifikátor, Image Map assety a SFTP target. Do repozitára neukladaj heslá, tokeny, .env, wp-config.php ani databázové exporty.
