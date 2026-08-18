# Centrálne kontaktné údaje (ACF Options)

Telefón, e-mail, adresa a údaje o maklérovi majú jedno miesto. Zmena v ACF sa prejaví všade, kde sa na web vypisujú – žiadne prepisovanie po jednotlivých Bricks elementoch.

> Výnimka z pravidla v `AGENTS.md`: tieto polia sa **nedefinujú v mu-plugins**, ale priamo v ACF. Repozitár drží len export a túto dokumentáciu. Pri zmene štruktúry polí treba export nižšie znova stiahnuť z ACF a commitnúť, inak sa stav v repe rozíde s realitou.

## 1. Options stránka

| Položka | Hodnota |
|---|---|
| Názov | Kontaktné údaje |
| Slug (menu_slug) | rs rezidencia-lomnica-kontakt |
| Oprávnenie | rs edit_posts |
| Ikona | rs dashicons-phone |

Vytvorenie: **ACF → Options Pages → Add New** (ACF PRO 6.2 a novšie). V starších verziách ACF sa options stránka dá spraviť iba kódom cez `acf_add_options_page()`.

Slug musí sedieť presne, inak sa field group nezobrazí – lokácia skupiny je naviazaná naň.

## 2. Polia

Import: **ACF → Tools → Import Field Groups** → `docs/acf/kontaktne-udaje.json`.

### Záložka „Všeobecné"

| Pole | Name | Typ | Poznámka |
|---|---|---|---|
| Telefón | rs contact_phone | text | Tvar `+421 900 123 456` |
| E-mail | rs contact_email | email | |
| Adresa | rs contact_address | textarea | Nové riadky ako `<br>` |
| Názov spoločnosti | rs contact_company | text | |

### Záložka „Maklér"

| Pole | Name | Typ | Poznámka |
|---|---|---|---|
| Meno a priezvisko | rs agent_name | text | |
| Pozícia | rs agent_role | text | napr. Realitný maklér |
| Telefón makléra | rs agent_phone | text | Ak prázdne, použi `contact_phone` |
| E-mail makléra | rs agent_email | email | Ak prázdne, použi `contact_email` |
| Fotka makléra | rs agent_photo | image | Return format **array** |

Názvy polí (`name`) sú kontrakt – na nich visia všetky Bricks dynamic tagy. Meniť sa dá label, nie name.

## 3. Použitie v Bricks

Options polia sa v Bricks adresujú s príponou `:option`:

```
{acf_contact_phone:option}
{acf_contact_email:option}
{acf_agent_name:option}
{acf_agent_role:option}
{acf_agent_photo:option}
```

**Telefón ako odkaz:** do elementu Button/Text link daj do poľa URL `tel:{acf_contact_phone:option}`. Medzery v čísle väčšina prehliadačov znesie; ak by robili problém, drž v ACF číslo bez medzier a formátuj ho cez CSS `letter-spacing`, prípadne pridaj druhé pole na zobrazovaný tvar.

**E-mail ako odkaz:** `mailto:{acf_contact_email:option}`.

**Fotka makléra:** element Image → Dynamic Data → `{acf_agent_photo:option}`. Return format musí byť array, inak Bricks dostane len ID.

**Fallback maklérových kontaktov** Bricks sám nerieši. Buď obe polia vypĺňaj, alebo v šablóne použi podmienku na `agent_phone` a ako druhú vetvu `contact_phone`.

## 4. Kde to na webe treba napojiť

Po vytvorení polí treba prejsť tieto miesta a nahradiť natvrdo napísané hodnoty dynamickými tagmi:

- [ ] Hlavička – telefón, e-mail
- [ ] Pätička – telefón, e-mail, adresa, názov spoločnosti
- [ ] Stránka Kontakt – celý kontaktný blok vrátane makléra
- [ ] Formuláre – adresát notifikácií vo FluentForms
- [ ] Sekcia „Máte otázku?" a podobné CTA bloky
- [ ] Schema.org / SEO plugin, ak sa tam kontakty duplikujú

Dovtedy platí, že zmena v ACF sa prejaví len tam, kde už dynamický tag je.

## 5. Čo nie je vyriešené

- Options stránka aj `return_format` obrázka vyžadujú **ACF PRO**.
- Polia nie sú verzionované v kóde, takže zmena v ACF UI sa do repa nedostane sama.
- Fallback maklér → všeobecný kontakt je konvencia, nie vynútené správanie.
