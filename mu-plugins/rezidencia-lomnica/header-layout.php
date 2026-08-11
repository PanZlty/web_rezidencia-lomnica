<?php
/**
 * Header layout CSS loaded through the MU-plugin enqueue layer.
 * The module is intentionally loaded through WordPress enqueue hooks.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', static function () {
    if (is_admin()) {
        return;
    }

    wp_register_style('rezidencia-lomnica-header-layout', false, [], null);
    wp_enqueue_style('rezidencia-lomnica-header-layout');
    wp_add_inline_style('rezidencia-lomnica-header-layout', <<<'CSS'
/*
 * Bricks classes:
 * HEADER section:        site-header
 * NAV - desktop:         site-header__desktop
 * Left Nav:              site-header__nav-left
 * Logo desktop block:    site-header__logo
 * Right Nav:             site-header__nav-right
 *
 * NAV - mobile:          site-header__mobile
 */

.site-header {
  width: 100%;
  z-index: 999;
  background: var(--primary, #142335) !important;
  background-image: none !important;
  box-shadow: none !important;
  transition: none !important;
}

/* Byty: bez gradientu, hneÄŹ plnĂˇ farba */
body.rs-is-byty #brx-header .site-header,
body.rs-is-byty .site-header {
  background: var(--primary, #142335) !important;
  background-image: none !important;
}

.site-header__desktop {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  width: 100%;
  column-gap: clamp(1.5rem, 3vw, 4rem);
}

.site-header__nav-left {
  order: 2;
  margin-left: auto;
  justify-self: initial;
}

.site-header__logo {
  order: 1;
  flex: 0 1 auto;
  justify-self: initial;
}

#brx-header .site-header__logo,
#brx-header .site-header__mobile-logo,
#brx-header .brxe-logo,
#brx-header .site-logo,
#brx-header [class*="logo"],
#brx-footer .brxe-logo,
#brx-footer .site-logo,
#brx-footer [class*="logo"],
header .brxe-logo,
header .site-logo,
header [class*="logo"],
footer .brxe-logo,
footer .site-logo,
footer [class*="logo"] {
  filter: none !important;
  opacity: 1 !important;
  mix-blend-mode: normal !important;
  forced-color-adjust: none;
}

#brx-header .site-header__logo a,
#brx-header .site-header__logo a:hover,
#brx-header .site-header__logo a:focus,
#brx-header .site-header__mobile-logo a,
#brx-header .site-header__mobile-logo a:hover,
#brx-header .site-header__mobile-logo a:focus,
#brx-header .brxe-logo a,
#brx-header .brxe-logo a:hover,
#brx-header .brxe-logo a:focus,
#brx-header .site-logo a,
#brx-header .site-logo a:hover,
#brx-header .site-logo a:focus,
#brx-header [class*="logo"] a,
#brx-header [class*="logo"] a:hover,
#brx-header [class*="logo"] a:focus,
#brx-footer .brxe-logo a,
#brx-footer .brxe-logo a:hover,
#brx-footer .brxe-logo a:focus,
#brx-footer .site-logo a,
#brx-footer .site-logo a:hover,
#brx-footer .site-logo a:focus,
#brx-footer [class*="logo"] a,
#brx-footer [class*="logo"] a:hover,
#brx-footer [class*="logo"] a:focus,
header [class*="logo"] a,
header [class*="logo"] a:hover,
header [class*="logo"] a:focus,
footer [class*="logo"] a,
footer [class*="logo"] a:hover,
footer [class*="logo"] a:focus {
  filter: none !important;
  opacity: 1 !important;
  mix-blend-mode: normal !important;
  forced-color-adjust: none;
}

#brx-header img[src*=".svg"][src*="logo"],
#brx-header a:hover img[src*=".svg"][src*="logo"],
#brx-header a:focus img[src*=".svg"][src*="logo"],
#brx-header [class*="logo"] img,
#brx-header [class*="logo"] a:hover img,
#brx-header [class*="logo"] a:focus img,
#brx-footer img[src*=".svg"][src*="logo"],
#brx-footer a:hover img[src*=".svg"][src*="logo"],
#brx-footer a:focus img[src*=".svg"][src*="logo"],
#brx-footer [class*="logo"] img,
#brx-footer [class*="logo"] a:hover img,
#brx-footer [class*="logo"] a:focus img,
header [class*="logo"] img,
header [class*="logo"] a:hover img,
header [class*="logo"] a:focus img,
footer [class*="logo"] img,
footer [class*="logo"] a:hover img,
footer [class*="logo"] a:focus img {
  filter: none !important;
  opacity: 1 !important;
  mix-blend-mode: normal !important;
  forced-color-adjust: none;
}

#brx-header .site-header__logo svg,
#brx-header .site-header__logo a:hover svg,
#brx-header .site-header__logo a:focus svg,
#brx-header .site-header__mobile-logo svg,
#brx-header .site-header__mobile-logo a:hover svg,
#brx-header .site-header__mobile-logo a:focus svg,
#brx-header .brxe-logo svg,
#brx-header .brxe-logo a:hover svg,
#brx-header .brxe-logo a:focus svg,
#brx-header .site-logo svg,
#brx-header .site-logo a:hover svg,
#brx-header .site-logo a:focus svg,
#brx-header [class*="logo"] svg,
#brx-header [class*="logo"] a:hover svg,
#brx-header [class*="logo"] a:focus svg,
#brx-footer .brxe-logo svg,
#brx-footer .brxe-logo a:hover svg,
#brx-footer .brxe-logo a:focus svg,
#brx-footer .site-logo svg,
#brx-footer .site-logo a:hover svg,
#brx-footer .site-logo a:focus svg,
#brx-footer [class*="logo"] svg,
#brx-footer [class*="logo"] a:hover svg,
#brx-footer [class*="logo"] a:focus svg,
header [class*="logo"] svg,
header [class*="logo"] a:hover svg,
header [class*="logo"] a:focus svg,
footer [class*="logo"] svg,
footer [class*="logo"] a:hover svg,
footer [class*="logo"] a:focus svg {
  filter: none !important;
  opacity: 1 !important;
  mix-blend-mode: normal !important;
  forced-color-adjust: none;
}

.site-header__nav-right {
  order: 3;
  justify-self: initial;
}

.site-header__mobile {
  display: none;
}

.site-header__nav-left ul,
.site-header__nav-right ul {
  gap: 3.2rem;
  justify-content: flex-end;
}

/* Desktop nav hover/focus/active underline: ÄŤiara sa roztiahne zo stredu do strĂˇn */
.site-header__nav-left a,
.site-header__nav-right a {
  position: relative;
  display: inline-flex;
  align-items: center;
  text-decoration: none;
  transition: color 0.25s ease;
}

.site-header__nav-left a::after,
.site-header__nav-right a::after {
  content: "";
  position: absolute;
  left: 0;
  right: 0;
  bottom: -0.45em;
  height: 1px;
  background: currentColor;
  opacity: 0.9;
  transform: scaleX(0);
  transform-origin: center;
  transition: transform 0.32s ease, opacity 0.32s ease;
}

.site-header__nav-left a:hover,
.site-header__nav-left a:focus-visible,
.site-header__nav-left .current-menu-item > a,
.site-header__nav-left .current-menu-ancestor > a,
.site-header__nav-left .current_page_item > a,
.site-header__nav-left .current_page_ancestor > a,
.site-header__nav-left a[aria-current="page"],
.site-header__nav-right a:hover,
.site-header__nav-right a:focus-visible,
.site-header__nav-right .current-menu-item > a,
.site-header__nav-right .current-menu-ancestor > a,
.site-header__nav-right .current_page_item > a,
.site-header__nav-right .current_page_ancestor > a,
.site-header__nav-right a[aria-current="page"] {
  color: var(--tertiary);
}

.site-header__nav-left a:hover::after,
.site-header__nav-left a:focus-visible::after,
.site-header__nav-left .current-menu-item > a::after,
.site-header__nav-left .current-menu-ancestor > a::after,
.site-header__nav-left .current_page_item > a::after,
.site-header__nav-left .current_page_ancestor > a::after,
.site-header__nav-left a[aria-current="page"]::after,
.site-header__nav-right a:hover::after,
.site-header__nav-right a:focus-visible::after,
.site-header__nav-right .current-menu-item > a::after,
.site-header__nav-right .current-menu-ancestor > a::after,
.site-header__nav-right .current_page_item > a::after,
.site-header__nav-right .current_page_ancestor > a::after,
.site-header__nav-right a[aria-current="page"]::after {
  transform: scaleX(1);
}

.site-header__nav-left a:focus-visible,
.site-header__nav-right a:focus-visible {
  outline: none;
}

@media (max-width: 767px) {
  .site-header__desktop {
    display: none !important;
  }

  .site-header__mobile {
    display: flex !important;
    align-items: center;
    justify-content: space-between;
    width: 100%;
  }

  .site-header__mobile-logo {
    margin-right: auto;
  }
}

/* Bricks sticky / scrolling state */
#brx-header.scrolling .site-header,
#brx-header.sticky.scrolling .site-header,
#brx-header.brx-sticky.scrolling .site-header {
  background: var(--primary, #142335) !important;
  background-image: none !important;
  backdrop-filter: none !important;
  box-shadow: none !important;
  transition: none !important;
}
CSS
    );

    $rl_logo_light_url = wp_get_attachment_url(2011);

    if ($rl_logo_light_url) {
        wp_add_inline_style(
            'rezidencia-lomnica-header-layout',
            sprintf(
                <<<'CSS'
/* Header logo selected from the Lomnica Media Library assets. */
#brx-header .site-header__logo img,
#brx-header .site-header__mobile-logo img,
#brx-header .brxe-logo img,
#brx-header .site-logo img,
#brx-header [class*="logo"] img {
  content: url("%s") !important;
}
CSS,
                esc_url_raw($rl_logo_light_url)
            )
        );
    }
}, 100);

