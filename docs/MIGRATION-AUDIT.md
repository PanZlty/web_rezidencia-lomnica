# Migration Audit

## Referenčné repozitáre

- PanZlty/web_rezidencia-sturova – zdroj funkcionality a ACF/Image Map kontraktu.
- PanZlty/web_monaviza – vzor top-level MU loadera, modulárnych súborov, WordPress enqueue hookov a jednoduchého SFTP deployu.

## Zámerné zmeny

1. FluentSnippets file-based metadata a snippets/** workflow sa nepřenášajú do aktívneho runtime.
2. PHP moduly sú načítané cez mu-plugins/rezidencia-lomnica-loader.php.
3. CSS/JS sa vkladajú cez wp_enqueue_scripts, wp_head alebo wp_footer.
4. Stúrovské asset URL boli nahradené rl_asset_url().
5. Krátke kódy rs_* ostávajú kvôli kompatibilite s Bricks šablónami.
6. Design tokeny sú lomnické a sústredené v brand-tokens.php.
7. Skladová logika bola zúžená na atribút bytu; samostatné skladové jednotky a ich statusy sa neaktivujú.

## Legacy, ktorý sa zámerne neaktivuje

- vypnutý Image Map hover-fix,
- vypnutý nested-shortcode helper,
- duplicitné CSS mirror súbory,
- duplicitný JS load-more súbor, ktorý je už obsiahnutý v aktívnom PHP module,
- .js súbor, ktorý pôvodný FluentSnippets workflow nenasadzoval.

Tieto položky sú zdokumentované ako audit, nie načítané v produkcii.
