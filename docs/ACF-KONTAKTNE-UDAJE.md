# Centrálne kontaktné údaje (ACF Options)

Telefón, e-mail a údaje o maklérovi majú jedno miesto v ACF. Zmena tam sa prejaví všade, kde je na webe dynamický tag.

**Toto už na webe existuje a je vyplnené.** Nič sa nevytvára nanovo – dokument popisuje skutočný stav zistený cez WordPress MCP.

## 1. Kde to je

| Položka | Hodnota |
|---|---|
| Options stránka | Nastavenia projektu |
| Slug | rs project-settings |
| Field group | Globálne nastavenia projektu |
| Group key | rs group_6a00e3ef6c04c |
| Oprávnenie | rs edit_posts |

Polia sú v záložke **Kontaktné údaje**.

Na tej istej options stránke sedí aj skupina **Option Page** (`group_638315a281bf1`) – tá patrí pluginu Advanced Themer, jej polia majú prefix `brxc_` a nemajú s kontaktmi nič spoločné. Nechať tak.

## 2. Polia

| Pole | Name | Typ |
|---|---|---|
| Meno agenta | rs agent_name | text |
| Telefón agenta | rs agent_phone | text |
| Email agenta | rs agent_email | email |
| Email webu | rs site_email | email |
| Web agenta | rs agent_website | url |
| Fotka agenta | rs agent_photo | image |
| Logo agenta | rs agent_logo | image |
| Pracovné hodiny | rs business_hours | text |
| Poznámky | rs notes | textarea |

Názvy polí sú kontrakt – visia na nich Bricks dynamic tagy. Meniť sa dá label, nie name.

Samostatné pole pre všeobecný telefón webu neexistuje; `agent_phone` slúži ako jediné telefónne číslo. Pre e-mail existujú dve polia (`agent_email` a `site_email`) a dnes majú rovnakú hodnotu.

## 3. Kde to už web číta

| Miesto | Použité tagy |
|---|---|
| Šablóna FOOTER (661) | rs {acf_agent_phone}, {acf_site_email}, {acf_business_hours} |
| Šablóna HEADER (838) | rs {acf_agent_phone}, {acf_site_email} |
| Stránka Kontakt (1394) | rs {acf_agent_name}, {acf_site_email}, {acf_agent_email}, {acf_agent_phone} |

Nikde v Bricks obsahu sa nenašiel natvrdo napísaný telefón, e-mail ani meno agenta. Centrálna správa teda funguje na týchto troch miestach.

## 4. Šablóna CTA a shortcodes

Šablóna **CTA (658)** používa shortcodes `[rs_contact_email]` a `[rs_contact_phone]` – pozostatok po Štúrovej. V projekte chýbali, takže neregistrovaný shortcode WordPress vypisoval doslovne a v CTA sekcii sa návštevníkovi ukazoval text `[rs_contact_phone]`.

Rieši to `mu-plugins/rezidencia-lomnica/contact-shortcodes.php`, ktorý ich registruje a hodnoty číta z ACF options. Žiadne polia nedefinuje.

| Shortcode | Zdroj | Výstup |
|---|---|---|
| rs [rs_contact_phone] | agent_phone | rs 0948 757 959 |
| rs [rs_contact_phone_url] | agent_phone | rs tel:+421948757959 |
| rs [rs_contact_email] | site_email | rs info@partnersreal.sk |
| rs [rs_contact_email_url] | site_email | rs mailto:info@partnersreal.sk |
| rs [rs_agent_name] | agent_name | rs Ing. Ladislav Uzík |

Vedúca nula sa do `tel:` prepisuje na `+421`; znesie aj tvary `+421 948 …` a `00421948…`.

Pole `notes` na options stránke obsahuje aj `[rs_contact_cta_text]`, ku ktorému neexistuje zodpovedajúce ACF pole. Nie je nikde použitý, preto sa neregistruje.

### Prečo shortcodes a nie dynamic tagy

Čistejšie by bolo prepnúť CTA na `{acf_agent_phone}` a `{acf_site_email}`, ako to má FOOTER a HEADER. **Do tejto šablóny sa ale nedá zapísať cez Bricks REST/MCP.** Obsahuje element typu `next-arrow-button-v2`, ktorý sa mimo builderu neregistruje, a Bricks pri ukladaní odmietne celý strom s chybou `Unknown element type`.

Rovnaký problém má šablóna **HERO - slider ken burns (859)** kvôli elementu `brf-lottie`. Ostatných 15 Bricks objektov je zapisovateľných.

Keď sa CTA raz prepne v builderi na dynamic tagy, tento modul sa dá zmazať.

## 5. Ako to používať v Bricks

Options polia sa v tomto projekte adresujú bez prípony, tak ako to má FOOTER:

```
{acf_agent_phone}
{acf_site_email}
{acf_agent_name}
{acf_agent_photo}
```

Telefón ako odkaz: do URL daj `tel:{acf_agent_phone}`. E-mail: `mailto:{acf_site_email}`.

## 6. Poznámka k zdroju pravdy

Polia sú definované v ACF, nie v `mu-plugins/`. Je to zámerná výnimka z pravidla v `AGENTS.md`. Dôsledok: zmena štruktúry polí v ACF UI sa do repozitára sama nedostane a tento dokument treba aktualizovať ručne.
