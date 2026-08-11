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
  background: linear-gradient(
    180deg,
    rgba(32, 42, 47, 0.72) 0%,
    rgba(32, 42, 47, 0.34) 58%,
    rgba(32, 42, 47, 0) 100%
  );
  transition:
    background 0.4s ease,
    box-shadow 0.4s ease,
    backdrop-filter 0.4s ease;
}

/* Byty: bez gradientu, hneÄŹ plnĂˇ farba */
body.rs-is-byty #brx-header .site-header,
body.rs-is-byty .site-header {
  background: rgba(32, 42, 47, 0.96) !important;
}

.site-header__desktop {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
  width: 100%;
  column-gap: clamp(5rem, 6vw, 10rem);
}

.site-header__nav-left {
  justify-self: end;
}

.site-header__logo {
  justify-self: center;
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
  justify-self: start;
}

.site-header__mobile {
  display: none;
}

.site-header__nav-left ul,
.site-header__nav-right ul {
  gap: 3.2rem;
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
  }
}

/* Bricks sticky / scrolling state */
#brx-header.scrolling .site-header,
#brx-header.sticky.scrolling .site-header,
#brx-header.brx-sticky.scrolling .site-header {
  background: rgba(32, 42, 47, 0.96) !important;
  backdrop-filter: blur(14px);
  box-shadow: 0 12px 34px rgba(0, 0, 0, 0.18);
}
CSS
    );
}, 100);