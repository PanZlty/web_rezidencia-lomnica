<?php
/**
 * Styly galerie v sablone jedneho apartmanu (.apartment-gallery).
 *
 * Presunute z Bricks global class "apartment-gallery" (id fyimjy, _cssCustom),
 * aby produkcnym zdrojom bol repozitar a nie Bricks custom code.
 * Pravidla su prevzate 1:1, aby sa vzhlad galerie nezmenil.
 *
 * Triedy .has-extra-count / .has-extra-count-mobile a atributy
 * data-extra-count / data-extra-count-mobile nasadzuje apartment-gallery-overlay.php.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', static function () {
    if (is_admin()) {
        return;
    }

    wp_register_style('rezidencia-lomnica-apartment-gallery-styles', false, [], null);
    wp_enqueue_style('rezidencia-lomnica-apartment-gallery-styles');
    wp_add_inline_style('rezidencia-lomnica-apartment-gallery-styles', <<<'CSS'
/* ===== GALERIA BYTU ===== */

.apartment-gallery.bricks-layout-wrapper,
.apartment-gallery .bricks-layout-wrapper {
  display: grid !important;
  grid-template-columns: minmax(0, 2fr) minmax(0, 1fr) minmax(0, 1fr) !important;
  grid-template-rows: repeat(2, minmax(0, 1fr)) !important;
  gap: var(--space-s) !important;
  width: 100% !important;
  aspect-ratio: 3 / 1 !important;
  align-items: stretch !important;
  grid-auto-flow: dense !important;
}

/* položky */
.apartment-gallery.bricks-layout-wrapper .bricks-layout-item,
.apartment-gallery .bricks-layout-wrapper .bricks-layout-item {
  position: relative !important;
  overflow: hidden !important;
  border-radius: var(--radius-l) !important;
  width: 100% !important;
  height: 100% !important;
  background: var(--surface-alt) !important;
  min-width: 0 !important;
  min-height: 0 !important;
  outline: none !important;
  box-shadow: none !important;
  -webkit-tap-highlight-color: transparent !important;
}

/* vnútorné wrappery */
.apartment-gallery .bricks-layout-item a,
.apartment-gallery .bricks-layout-item figure,
.apartment-gallery .bricks-layout-item picture {
  display: block !important;
  width: 100% !important;
  height: 100% !important;
  outline: none !important;
  box-shadow: none !important;
  text-decoration: none !important;
  -webkit-tap-highlight-color: transparent !important;
}

/* odstráni stroke/focus */
.apartment-gallery .bricks-layout-item:focus,
.apartment-gallery .bricks-layout-item:focus-visible,
.apartment-gallery .bricks-layout-item a:focus,
.apartment-gallery .bricks-layout-item a:focus-visible {
  outline: none !important;
  box-shadow: none !important;
}

/* desktop layout */
.apartment-gallery .bricks-layout-item:nth-child(1) {
  grid-column: 1 / 2 !important;
  grid-row: 1 / 3 !important;
}

.apartment-gallery .bricks-layout-item:nth-child(2) {
  grid-column: 2 / 3 !important;
  grid-row: 1 / 2 !important;
}

.apartment-gallery .bricks-layout-item:nth-child(3) {
  grid-column: 3 / 4 !important;
  grid-row: 1 / 2 !important;
}

.apartment-gallery .bricks-layout-item:nth-child(4) {
  grid-column: 2 / 3 !important;
  grid-row: 2 / 3 !important;
}

.apartment-gallery .bricks-layout-item:nth-child(5) {
  grid-column: 3 / 4 !important;
  grid-row: 2 / 3 !important;
}

/* skry ďalšie */
.apartment-gallery .bricks-layout-item:nth-child(n+6) {
  display: none !important;
}

/* obrázky center / center */
.apartment-gallery .bricks-layout-item img,
.apartment-gallery img {
  width: 100% !important;
  height: 100% !important;
  object-fit: cover !important;
  object-position: center center !important;
  display: block !important;
  transition: transform 0.35s ease, filter 0.35s ease !important;
}

/* hover */
.apartment-gallery .bricks-layout-item:hover img {
  transform: scale(1.035) !important;
  filter: brightness(0.92) !important;
}

/* desktop overlay iba ak JS prida class */
.apartment-gallery .bricks-layout-item:nth-child(5).has-extra-count::before {
  content: "" !important;
  position: absolute !important;
  inset: 0 !important;
  background: rgba(31, 31, 31, 0.68) !important;
  z-index: 1 !important;
  pointer-events: none !important;
}

.apartment-gallery .bricks-layout-item:nth-child(5).has-extra-count::after {
  content: attr(data-extra-count) !important;
  position: absolute !important;
  inset: 0 !important;
  z-index: 2 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  color: var(--white) !important;
  font-size: var(--heading-m) !important;
  font-weight: 700 !important;
  line-height: 1 !important;
  pointer-events: none !important;
}

/* tablet / menší desktop */
@media (max-width: 1180px) {
  .apartment-gallery.bricks-layout-wrapper,
  .apartment-gallery .bricks-layout-wrapper {
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    grid-template-rows: auto auto !important;
    aspect-ratio: auto !important;
    gap: var(--space-xs) !important;
  }

  .apartment-gallery .bricks-layout-item {
    height: auto !important;
    aspect-ratio: 3 / 2 !important;
    border-radius: var(--radius-m) !important;
  }

  .apartment-gallery .bricks-layout-item:nth-child(1) {
    grid-column: 1 / -1 !important;
    grid-row: 1 / 2 !important;
  }

  .apartment-gallery .bricks-layout-item:nth-child(2) {
    grid-column: 1 / 2 !important;
    grid-row: 2 / 3 !important;
  }

  .apartment-gallery .bricks-layout-item:nth-child(3) {
    grid-column: 2 / 3 !important;
    grid-row: 2 / 3 !important;
  }

  .apartment-gallery .bricks-layout-item:nth-child(4) {
    grid-column: 3 / 4 !important;
    grid-row: 2 / 3 !important;
  }

  .apartment-gallery .bricks-layout-item:nth-child(5) {
    grid-column: 4 / 5 !important;
    grid-row: 2 / 3 !important;
  }
}

/* mobil */
@media (max-width: 640px) {
  .apartment-gallery.bricks-layout-wrapper,
  .apartment-gallery .bricks-layout-wrapper {
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    grid-template-rows: auto auto !important;
    gap: var(--space-2xs) !important;
  }

  .apartment-gallery .bricks-layout-item {
    aspect-ratio: 3 / 2 !important;
    border-radius: var(--radius-m) !important;
  }

  .apartment-gallery .bricks-layout-item:nth-child(1) {
    grid-column: 1 / -1 !important;
    grid-row: 1 / 2 !important;
  }

  .apartment-gallery .bricks-layout-item:nth-child(2) {
    grid-column: 1 / 2 !important;
    grid-row: 2 / 3 !important;
  }

  .apartment-gallery .bricks-layout-item:nth-child(3) {
    grid-column: 2 / 3 !important;
    grid-row: 2 / 3 !important;
  }

  .apartment-gallery .bricks-layout-item:nth-child(4) {
    grid-column: 3 / 4 !important;
    grid-row: 2 / 3 !important;
  }

  .apartment-gallery .bricks-layout-item:nth-child(5) {
    display: none !important;
  }

  .apartment-gallery .bricks-layout-item:nth-child(4).has-extra-count-mobile::before {
    content: "" !important;
    position: absolute !important;
    inset: 0 !important;
    background: rgba(31, 31, 31, 0.68) !important;
    z-index: 1 !important;
    pointer-events: none !important;
  }

  .apartment-gallery .bricks-layout-item:nth-child(4).has-extra-count-mobile::after {
    content: attr(data-extra-count-mobile) !important;
    position: absolute !important;
    inset: 0 !important;
    z-index: 2 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: var(--white) !important;
    font-size: var(--heading-s) !important;
    font-weight: 700 !important;
    line-height: 1 !important;
    pointer-events: none !important;
  }
}
CSS
    );
}, 100);
