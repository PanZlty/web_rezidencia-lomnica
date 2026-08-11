<?php
/**
 * Image Map logo/hover guard loaded as a MU-plugin module.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_footer', static function () {
    if (is_admin()) {
        return;
    }
    ?>
    <script id="image-map-hover-guard">
document.addEventListener('DOMContentLoaded', function () {
  var mapRootSelector = '.imp-wrap, .imp-main, .imp-canvas-wrap, .imp-ui, .imp-object-menu, .imp-object-list, .imp-tooltips-container';
  var realTargetSelector = '.imp-object, .imp-shape, .imp-spot, .imp-object-poly, .imp-object-rect, .imp-object-ellipse, .imp-object-list-item, .imp-ui-layers-select, .imp-fullscreen-button, .imp-tooltip .squares-element';

  function hasImageMapDescendant(el) {
    return !!(el && el.querySelector && el.querySelector('.imp-wrap, .imp-main, .imp-canvas-wrap'));
  }

  function isInsideImageMapArea(el) {
    return !!(el && el.closest && (el.closest(mapRootSelector) || hasImageMapDescendant(el)));
  }

  function isRealImageMapTarget(el) {
    return !!(el && el.closest && el.closest(realTargetSelector));
  }

  function guardImageMapHover(e) {
    var target = e.target;

    if (!isInsideImageMapArea(target)) {
      return;
    }

    if (isRealImageMapTarget(target)) {
      return;
    }

    e.stopImmediatePropagation();
    e.stopPropagation();
  }

  window.addEventListener('pointerover', guardImageMapHover, true);
  window.addEventListener('pointerout', guardImageMapHover, true);
});
    </script>
    <?php
}, 98);
