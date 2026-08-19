<?php
/**
 * Overlay s poctom skrytych fotiek na poslednej viditelnej dlazdici
 * galerie v sablone jedneho apartmanu (.apartment-gallery).
 *
 * Vzhlad overlayu je uz definovany v CSS galerie:
 *   .bricks-layout-item:nth-child(5).has-extra-count::before/::after
 *   .bricks-layout-item:nth-child(4).has-extra-count-mobile::before/::after  (do 640px)
 * a text sa berie z atributov data-extra-count / data-extra-count-mobile.
 * Chybala len logika, ktora tie triedy a atributy nasadi, takze overlay
 * sa nikdy nezobrazil.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', static function () {
    if (is_admin()) {
        return;
    }

    wp_register_script('rezidencia-lomnica-apartment-gallery-overlay', '', [], null, true);
    wp_enqueue_script('rezidencia-lomnica-apartment-gallery-overlay');
    wp_add_inline_script('rezidencia-lomnica-apartment-gallery-overlay', <<<'JS'
(function () {
  if (window.rsApartmentGalleryOverlayInit) {
    return;
  }

  window.rsApartmentGalleryOverlayInit = true;

  /* Kolko dlazdic mriezka realne ukazuje. Zodpoveda grid-area pravidlam
     galerie: nad 640px je viditelnych 5 dlazdic, pod 640px je piata
     schovana, takze ich ostava 4. */
  var DESKTOP_VISIBLE = 5;
  var MOBILE_VISIBLE = 4;

  function items(gallery) {
    return Array.prototype.slice.call(gallery.querySelectorAll(':scope > .bricks-layout-item'));
  }

  function clear(item) {
    item.classList.remove('has-extra-count', 'has-extra-count-mobile');
    item.removeAttribute('data-extra-count');
    item.removeAttribute('data-extra-count-mobile');
  }

  function mark(item, hidden, className, attribute) {
    if (!item || hidden < 1) {
      return;
    }

    item.setAttribute(attribute, '+' + hidden);
    item.classList.add(className);
  }

  function sync(gallery) {
    var cards = items(gallery);

    if (!cards.length) {
      return;
    }

    cards.forEach(clear);

    /* Obe varianty nasadzujeme naraz, o tom ktora sa zobrazi rozhoduje
       media query v CSS, takze netreba pocuvat na resize. */
    mark(cards[DESKTOP_VISIBLE - 1], cards.length - DESKTOP_VISIBLE, 'has-extra-count', 'data-extra-count');
    mark(cards[MOBILE_VISIBLE - 1], cards.length - MOBILE_VISIBLE, 'has-extra-count-mobile', 'data-extra-count-mobile');
  }

  function init() {
    Array.prototype.slice.call(document.querySelectorAll('.apartment-gallery')).forEach(function (gallery) {
      sync(gallery);

      if (gallery.getAttribute('data-rs-gallery-overlay-bound') === '1') {
        return;
      }

      gallery.setAttribute('data-rs-gallery-overlay-bound', '1');

      if (typeof MutationObserver === 'undefined') {
        return;
      }

      /* Bricks doplna dlazdice lazy loadom, preto sledujeme iba childList.
         Zmeny tried a atributov, ktore robime my, observer nespustia. */
      new MutationObserver(function () {
        sync(gallery);
      }).observe(gallery, { childList: true });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  window.addEventListener('load', init);
})();
JS
    );
}, 100);
