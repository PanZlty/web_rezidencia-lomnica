# Rezidencia Lomnica – Migration Checklist

## Inventár prenesený zo Štúrovej

- [x] Image Map Pro dynamické karty, ceny, statusy a aliasy.
- [x] Image Map object-list status pills a mapovanie hoveru.
- [x] Mobilné Image Map ovládanie a fullscreen label.
- [x] Status pill shortcode a normalizácia statických Bricks pillov.
- [x] Termín výstavby a progress bar.
- [x] Galéria s load-more.
- [x] Form spam guard pre Fluent Forms a Gravity Forms.
- [x] Globálne vypnutie komentárov, pingbackov a trackbackov.
- [x] Analytics hook ako samostatný modul.
- [x] Header/Image Map CSS prevedené na MU-plugin enqueue.
- [x] FluentSnippets metadata odstránené z aktívneho runtime.

## Pred prvým produkčným deployom

- [ ] Vytvoriť/exportovať lomnický CPT byty.
- [ ] Overiť ACF field names a status values.
- [ ] Overiť CPT iba pre samostatne predávané parkovanie; sklad ostáva atribútom bytu.
- [ ] Doplniť lomnické assety do wp-content/uploads/rezidencia-lomnica/.
- [ ] Nastaviť rezidencia_lomnica_asset_url, ak budú assety v inom umiestnení.
- [ ] Nastaviť kontaktné údaje a texty v Bricks/ACF Options.
- [ ] Doplniť lomnické page ID/slugy; nikdy nepoužiť Štúrové ID bez overenia.
- [ ] Doplniť lomnický analytics site ID alebo modul dočasne deaktivovať.
- [ ] Nastaviť GitHub Secrets a overiť SFTP_TARGET.
- [ ] Spustiť workflow_dispatch na staging/preview WordPress.
- [ ] Vypnúť staré FluentSnippets verzie až po vizuálnom a funkčnom porovnaní.

## QA

- [ ] Detail bytu: cena, status, galéria, pôdorys, miestnosti, PDF, kontakt.
- [ ] Image Map: tooltip, status farba, hatch pre sold, object list, hover, fullscreen.
- [ ] Mobil: breakpointy, menu, Image Map select/burger/fullscreen.
- [ ] Formuláre: odoslanie, Turnstile/CAPTCHA, upload, rate limit, spam payload.
- [ ] Galéria: počiatočný počet položiek a load-more.
- [ ] Accessibility: nadpisová hierarchia, focus states, aria atribúty.
- [ ] Bezpečnosť: PHP lint, žiadne secrets, žiadne debug texty na fronte.
