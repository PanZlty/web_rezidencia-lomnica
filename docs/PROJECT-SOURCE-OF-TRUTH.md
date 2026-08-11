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

Doplnkové jednotky:

| Typ | CPT | Kód | Cena | Status |
|---|---|---|---|---|
| Sklad | rs sklady / sklad | rs cellar_code | rs price / cellar_price | rs status / cellar_status |
| Parkovanie | rs parking / parkovacie-miesta | rs parking_number | rs parking_price | rs parking_status |

## 4. Statusy

Technické hodnoty sú rs available, rs reserved, rs sold.

- byt: Na predaj / Rezervovaný / Predaný,
- sklad: Dostupný / Rezervovaný / Predaný,
- parkovanie: Dostupné / Rezervované / Predané.

Farby a CSS tokeny sú v brand-tokens.php; dizajn sa dá meniť bez zásahu do dátovej logiky.

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

