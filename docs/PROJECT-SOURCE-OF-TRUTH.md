# Rezidencia Lomnica – Project Source of Truth

## 1. Dátový tok

    Google Sheets → import/sync ceny a statusu → WordPress CPT + ACF → Bricks / Image Map / formuláre

Frontend číta ceny a statusy z WordPress ACF, nie zo Sheets pri každej návšteve.

## 2. Technológie

- WordPress
- Bricks Builder
- ACF PRO
- Image Map Pro alebo neskôr vlastné SVG
- Fluent Forms alebo Gravity Forms
- Complianz podľa nasadenia
- GitHub Actions + SFTP deploy MU-pluginov

## 3. CPT a ACF kontrakt

Zachovaný kontrakt zo Štúrovej:

| Oblasť | Hodnota |
|---|---|
| Byty CPT | rs byty |
| Kód bytu | rs apartment_code |
| Cena | rs price |
| Status | rs status |
| Izby | rs rooms |
| Výmera | rs area_total |
| Balkón/terasa | rs balcony_area |
| Galéria | rs apartment_gallery |
| Pôdorys | rs floorplan_ground, rs floorplan_upper |
| PDF karta | rs apartment_pdf |
| Image Map shortcode | rs floor_map_shortcode |
| Popis | rs apartment_description |
| Rozpis miestností | rs room_areas_ground, rs room_areas_upper |
| Sklad patriaci k bytu | rs cellar_code, rs cellar_area |

Samostatne predávané jednotky:

| Typ | CPT | Kód | Cena | Status |
|---|---|---|---|---|
| Parkovanie | rs parking / parkovacie-miesta | rs parking_number | rs parking_price | rs parking_status |

Sklad nie je samostatný produkt ani samostatný CPT. Každý byt môže mať jeden sklad evidovaný ako súčasť bytu. Sklad sa nezobrazuje v samostatnom zozname, nemá vlastný status predaja a nepoužíva samostatný Image Map tooltip.

## 4. Statusy

Technické hodnoty sú rs available, rs reserved, rs sold.

- byt: Na predaj / Rezervovaný / Predaný,
- parkovanie: Dostupné / Rezervované / Predané.

Farby a CSS tokeny sú definované výhradne v Bricks (farebná paleta a globálne premenné `--status-available`, `--status-reserved`, `--status-sold`); dizajn sa dá meniť bez zásahu do dátovej logiky. MU-pluginy nesmú definovať vlastný `:root` s farebnými premennými, aby neprepisovali Bricks paletu. Moduly používajú tvar `var(--status-sold, #d32f2f)` – hex za čiarkou je iba záchrana pre prípad, že premenná v Bricks chýba, Bricks paletu neprepisuje. SVG tvary v Image Mape nevedia vyhodnotiť `var()` v atribúte `fill`, preto premennú rozbaľuje `cssPaint()` v JS.

### Pravidlo pre cenu

Cena sa na fronte zobrazuje **iba pri statuse `available`**. Pri `reserved`, `sold` aj neznámom statuse sa zobrazí placeholder `-`. Rovnaké pravidlo platí, ak je status `available`, ale cena nie je vyplnená.

Pravidlo je centralizované v `rs_imp_price_display($price, $status)` v `image-map-shortcodes.php` a používajú ho `[rs_apartment_price]`, tooltip bytu, tooltip parkovania aj `window.RS_IMP_MAP_UNITS`. Placeholder sa dá zmeniť filtrom `rezidencia_lomnica_price_placeholder`. Nové miesta, ktoré vypisujú cenu, musia volať `rs_imp_price_display()`, nie `rs_imp_price_text()`.

## 4a. Google Sheets sync

Zdroj: tabuľka `RS_GSHEETS_SPREADSHEET_ID`, hárok `Hárok1`. Beží cez WP-Cron každých 15 minút (`rs_gsheets_sync_hook`), bez API kľúča cez verejný gviz endpoint.

| Stĺpec | Obsah | ACF pole |
|---|---|---|
| A | kód jednotky (AP-01, P01…) | rs apartment_code / parking_number |
| B | úžitková plocha v m² | rs area_total |
| C | terasa / balkón v m² | rs balcony_area |
| D | cena | rs price / parking_price |
| E | status | rs status / parking_status |

Rozhoduje **poradie stĺpcov**, nie text v hlavičke – prvý riadok sa preskakuje. Celú mapu drží `rs_gsheets_columns()`; poradie aj nové stĺpce sa menia iba tam (alebo filtrom `rezidencia_lomnica_gsheets_columns`). Roly sú `code`, `price`, `status` a `number` (číslo do ACF poľa uvedeného v `field`).

Stĺpec statusu má v Sheets podmienené formátovanie – celá bunka zelená / oranžová / červená podľa Dostupný / Rezervovaný / Predaný, rovnaké odtiene ako pilly na webe.

- Kód v stĺpci `id` sa páruje cez `rs_imp_unit_id_by_code()`, takže sync funguje pre byty aj parkovanie.
- Tabuľka je zdroj pravdy: **prázdna bunka hodnotu v ACF vymaže**, ale iba ak má daný stĺpec aspoň jednu vyplnenú bunku. Úplne prázdny stĺpec sa berie ako „zatiaľ sa nepoužíva“ a ACF sa nedotkne, takže pridanie novej hlavičky nič nezmaže. Vypnuteľné filtrom `rezidencia_lomnica_gsheets_clear_empty`, ktorý dostáva názov poľa.
- Plochy znesú zápis `62,5`, `62.5`, `62,5 m²` aj `62.5 m2`; jednotka sa odstráni pred parsovaním.
- Nerozpoznaný status sa zámerne **neprepíše**, iba zaznamená, aby sa byt nepreklopil omylom.
- Zapisuje sa iba skutočná zmena; po zmene sa volá purge page cache a akcia `rezidencia_lomnica_units_synced`.
- Stav a manuálne spustenie: Nastavenia → Google Sheets sync, alebo `wp rs-gsheets sync`.
- WP-Cron je závislý od návštevnosti. Pri `DISABLE_WP_CRON` alebo nízkej návštevnosti musí `wp-cron.php` volať serverový cron, inak 15-minútový interval neplatí.

## 5. Image Map kontrakt

- Shape/hotspot identifikátor je rovnaký ako kód jednotky.
- Tooltip sa skladá dynamicky z CPT + ACF.
- Cena a status sa do tooltipu nepíšu ručne.
- window.RS_IMP_MAP_UNITS je spätne kompatibilný dátový zdroj pre object-list pills.
- Podporované sú aliasy parkovacích kódov P01, 01, 1, G01.
- Hover farby riadi Image Map Pro UI; custom JS rieši iba mapovanie a default status farby.
- Lomnické ikonky používa rl_asset_url() a predvolená cesta je /wp-content/uploads/rezidencia-lomnica/.

## 6. Shortcodes zachované pre migráciu

- [rs_apartment_price]
- [rs_apartment_context]
- [rs_apartment_map_card]
- [rs_map_unit_card]
- [rs_apartment_codes]
- [rs_map_unit_codes]
- [rs_apartment_status_pill]
- [termin_vystavby]

## 7. Stránky a obsah

Slugy, page ID, kontakty, doména, analytika, texty, Bricks element IDs a assety sa nesmú preberať zo Štúrovej naslepo. Doplniť ich podľa lomnického WordPress projektu a zaznamenať do docs/MIGRATION-CHECKLIST.md.

## 8. Kontaktné údaje

Telefón, e-mail a maklér majú jediný zdroj – ACF Options stránku **Nastavenia projektu** (slug `project-settings`), field group **Globálne nastavenia projektu** (`group_6a00e3ef6c04c`), záložka Kontaktné údaje. Bricks ich číta dynamickými tagmi, napr. `{acf_agent_phone}`.

Polia sú zámerne definované **v ACF, nie v mu-plugins**. Repozitár drží iba popis v docs/ACF-KONTAKTNE-UDAJE.md; zmena štruktúry polí v ACF UI sa do repa sama nedostane.

Názvy polí sú kontrakt: `agent_name`, `agent_phone`, `agent_email`, `site_email`, `agent_website`, `agent_photo`, `agent_logo`, `business_hours`, `notes`. Meniť sa dá label, nie name.

Šablóny FOOTER, HEADER a stránka Kontakt už z týchto polí čítajú. Šablóna CTA namiesto toho volá neexistujúce shortcodes `[rs_contact_phone]` a `[rs_contact_email]` – detail v docs/ACF-KONTAKTNE-UDAJE.md.
