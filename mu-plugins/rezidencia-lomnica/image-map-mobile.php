<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_head', function () {
    if (is_admin()) {
        return;
    }
    ?>
    <style id="rs-imp-mobile-controls">
      @media (max-width: 1024px) {
        :root {
          --rs-imp-mobile-control-height: 44px;
        }

        .rs-imp-map-scope,
        .rs-imp-map-scope.imp-ui-wrap {
          position: relative !important;
          overflow: visible !important;
        }

        .rs-imp-map-scope.imp-ui-wrap,
        .rs-imp-map-scope .imp-ui-wrap {
          padding-top: 116px !important;
          margin-bottom: 112px !important;
        }

        .rs-imp-map-scope .imp-ui,
        .rs-imp-map-scope .imp-ui-wrap,
        .rs-imp-map-scope .imp-ui-layers-menu-wrap,
        .rs-imp-map-scope .imp-ui-top-left,
        .rs-imp-map-scope .imp-ui-top-right,
        .rs-imp-map-scope .imp-ui-bottom-left,
        .rs-imp-map-scope .imp-ui-bottom-right {
          overflow: visible !important;
        }

        .rs-imp-map-scope .imp-ui,
        .rs-imp-map-scope .imp-ui-element,
        .rs-imp-map-scope .imp-ui-layers-select,
        .rs-imp-map-scope .imp-fullscreen-button {
          z-index: 20 !important;
        }

        .rs-imp-map-scope .imp-ui-top-left {
          position: absolute !important;
          top: calc(14px + env(safe-area-inset-top)) !important;
          left: 16px !important;
          right: auto !important;
          width: min(330px, calc(100% - 114px)) !important;
          min-width: 0 !important;
          max-width: none !important;
        }

        .rs-imp-map-scope .imp-ui-top-right {
          position: absolute !important;
          top: calc(14px + env(safe-area-inset-top)) !important;
          right: 16px !important;
          width: var(--rs-imp-mobile-control-height) !important;
          height: var(--rs-imp-mobile-control-height) !important;
        }

        .rs-imp-map-scope .imp-ui-layers-menu-wrap {
          position: relative !important;
          top: auto !important;
          left: auto !important;
          right: auto !important;
          width: 100% !important;
          max-width: none !important;
        }

        .rs-imp-map-scope .imp-ui-layers-menu-wrap > svg,
        .rs-imp-map-scope .imp-ui-layers-menu-wrap > i,
        .rs-imp-map-scope .imp-ui-layers-menu-wrap > span,
        .rs-imp-map-scope .imp-ui-layers-menu-wrap .imp-icon {
          display: none !important;
        }

        .rs-imp-map-scope .imp-ui-layers-menu-wrap::before,
        .rs-imp-map-scope .imp-ui-layers-menu-wrap::after {
          display: none !important;
          content: none !important;
        }

        .rs-imp-map-scope .imp-ui-top-left .imp-ui-layers-select,
        .rs-imp-map-scope .imp-ui-layers-menu-wrap .imp-ui-layers-select,
        .rs-imp-map-scope .imp-ui-layers-select {
          appearance: none !important;
          -webkit-appearance: none !important;
          width: 100% !important;
          min-width: 0 !important;
          max-width: none !important;
          min-height: var(--rs-imp-mobile-control-height) !important;
          height: var(--rs-imp-mobile-control-height) !important;
          max-height: var(--rs-imp-mobile-control-height) !important;
          box-sizing: border-box !important;
          line-height: var(--rs-imp-mobile-control-height) !important;
          padding: 0 48px 0 24px !important;
          border: 0 !important;
          border-radius: 999px !important;
          background-color: rgba(255, 255, 255, 0.94) !important;
          background-image: linear-gradient(45deg, transparent 50%, currentColor 50%), linear-gradient(135deg, currentColor 50%, transparent 50%) !important;
          background-position: calc(100% - 25px) 50%, calc(100% - 17px) 50% !important;
          background-size: 8px 8px, 8px 8px !important;
          background-repeat: no-repeat !important;
          color: var(--neutral-dark, #162520) !important;
          box-shadow: 0 18px 38px rgba(13, 28, 23, 0.14) !important;
          font-size: 14px !important;
          font-weight: 500 !important;
          letter-spacing: 0.08em !important;
          text-transform: uppercase !important;
        }

        .rs-imp-map-scope .imp-ui-top-left .imp-ui-layers-select:hover,
        .rs-imp-map-scope .imp-ui-top-left .imp-ui-layers-select:focus,
        .rs-imp-map-scope .imp-ui-layers-select:hover,
        .rs-imp-map-scope .imp-ui-layers-select:focus {
          background-color: var(--neutral-dark, #162520) !important;
          color: var(--white, #fff) !important;
        }

        .rs-imp-map-scope .imp-ui-bottom-left {
          position: absolute !important;
          top: calc(100% + 64px) !important;
          left: 0 !important;
          right: 0 !important;
          width: 100% !important;
          max-width: none !important;
          display: flex !important;
          justify-content: center !important;
        }

        .rs-imp-map-scope .imp-object-menu {
          z-index: 99998 !important;
        }

        .rs-imp-map-scope .imp-ui-top-right .imp-menu-button,
        .rs-imp-map-scope .imp-ui-top-right .imp-ui-element.imp-menu-button,
        .rs-imp-map-scope .imp-object-menu .imp-menu-button,
        .rs-imp-map-scope .imp-ui-element.imp-menu-button,
        .rs-imp-map-scope .imp-menu-button {
          position: absolute !important;
          top: 0 !important;
          right: 0 !important;
          width: 44px !important;
          min-width: var(--rs-imp-mobile-control-height) !important;
          max-width: var(--rs-imp-mobile-control-height) !important;
          height: var(--rs-imp-mobile-control-height) !important;
          min-height: var(--rs-imp-mobile-control-height) !important;
          max-height: var(--rs-imp-mobile-control-height) !important;
          margin: 0 !important;
          border: 0 !important;
          border-radius: 999px !important;
          box-sizing: border-box !important;
          line-height: 1 !important;
          background: rgba(255, 255, 255, 0.96) !important;
          color: #000 !important;
          box-shadow: 0 18px 38px rgba(13, 28, 23, 0.14) !important;
          display: flex !important;
          align-items: center !important;
          justify-content: center !important;
        }

        .rs-imp-map-scope .imp-ui-top-right .imp-menu-button svg,
        .rs-imp-map-scope .imp-ui-top-right .imp-ui-element.imp-menu-button svg,
        .rs-imp-map-scope .imp-menu-button svg,
        .rs-imp-map-scope .imp-ui-element.imp-menu-button svg {
          display: none !important;
        }

        .rs-imp-map-scope .imp-ui-top-right .imp-menu-button::before,
        .rs-imp-map-scope .imp-ui-top-right .imp-ui-element.imp-menu-button::before,
        .rs-imp-map-scope .imp-menu-button::before {
          content: "" !important;
          width: 22px !important;
          height: 16px !important;
          background: linear-gradient(#000, #000) 0 0 / 22px 2px no-repeat,
                      linear-gradient(#000, #000) 0 7px / 22px 2px no-repeat,
                      linear-gradient(#000, #000) 0 14px / 22px 2px no-repeat !important;
          display: block !important;
        }

        .rs-imp-map-scope .imp-ui-bottom-left .imp-fullscreen-button,
        .rs-imp-map-scope .imp-ui-bottom-left .imp-ui-element.imp-fullscreen-button,
        .rs-imp-map-scope .imp-fullscreen-button {
          position: relative !important;
          top: auto !important;
          left: auto !important;
          right: auto !important;
          width: min(280px, calc(100% - 96px)) !important;
          min-width: 220px !important;
          max-width: 280px !important;
          min-height: var(--rs-imp-mobile-control-height) !important;
          height: var(--rs-imp-mobile-control-height) !important;
          max-height: var(--rs-imp-mobile-control-height) !important;
          margin: 0 auto !important;
          padding: 0 22px !important;
          border: 0 !important;
          border-radius: 999px !important;
          box-sizing: border-box !important;
          line-height: 1 !important;
          background: rgba(255, 255, 255, 0.94) !important;
          color: #1a1814 !important;
          box-shadow: 0 16px 34px rgba(13, 28, 23, 0.14) !important;
          display: flex !important;
          align-items: center !important;
          justify-content: center !important;
          gap: 10px !important;
        }

        .rs-imp-map-scope .imp-ui-bottom-left .imp-fullscreen-button:hover,
        .rs-imp-map-scope .imp-ui-bottom-left .imp-fullscreen-button:focus,
        .rs-imp-map-scope .imp-fullscreen-button:hover,
        .rs-imp-map-scope .imp-fullscreen-button:focus {
          background: var(--neutral-dark, #162520) !important;
          color: var(--white, #fff) !important;
        }

        .rs-imp-map-scope .imp-ui-bottom-left .imp-fullscreen-button:hover svg.imp-icon,
        .rs-imp-map-scope .imp-ui-bottom-left .imp-fullscreen-button:focus svg.imp-icon,
        .rs-imp-map-scope .imp-ui-bottom-left .imp-fullscreen-button:hover .imp-fullscreen-label,
        .rs-imp-map-scope .imp-ui-bottom-left .imp-fullscreen-button:focus .imp-fullscreen-label,
        .rs-imp-map-scope .imp-fullscreen-button:hover svg.imp-icon,
        .rs-imp-map-scope .imp-fullscreen-button:focus svg.imp-icon,
        .rs-imp-map-scope .imp-fullscreen-button:hover .imp-fullscreen-label,
        .rs-imp-map-scope .imp-fullscreen-button:focus .imp-fullscreen-label {
          fill: var(--white, #fff) !important;
          color: var(--white, #fff) !important;
        }

        .imp-object-menu.imp-mobile,
        .imp-object-menu {
          border: 0 !important;
          border-left: 0 !important;
          border-right: 0 !important;
          outline: 0 !important;
          background: linear-gradient(to left, rgba(13, 28, 23, 0.34) 0%, rgba(13, 28, 23, 0.18) 42%, rgba(13, 28, 23, 0) 100%) !important;
          box-shadow: none !important;
          backdrop-filter: none !important;
        }

        .imp-object-menu.imp-mobile::before,
        .imp-object-menu.imp-mobile::after,
        .imp-object-menu.imp-active::before,
        .imp-object-menu.imp-active::after,
        .imp-object-menu::before,
        .imp-object-menu::after {
          display: none !important;
          content: none !important;
          width: 0 !important;
          height: 0 !important;
          background: transparent !important;
          border: 0 !important;
          box-shadow: none !important;
        }

        .imp-object-list {
          position: relative !important;
          top: auto !important;
          right: auto !important;
          bottom: auto !important;
          left: auto !important;
          width: 100% !important;
          height: auto !important;
          max-height: none !important;
          padding: calc(76px + env(safe-area-inset-top)) 18px calc(28px + env(safe-area-inset-bottom)) !important;
          overflow-y: auto !important;
          background: transparent !important;
          border: 0 !important;
          outline: 0 !important;
          box-shadow: none !important;
          backdrop-filter: none !important;
          z-index: auto !important;
        }

        .imp-object-list .imp-object-list-item {
          min-height: 58px !important;
          margin: 0 0 10px !important;
          padding: 0 20px !important;
          border: 1px solid rgba(255, 255, 255, 0.62) !important;
          border-radius: 999px !important;
          background: rgba(255, 255, 255, 0.92) !important;
          color: var(--neutral-dark, #162520) !important;
          box-shadow: 0 12px 30px rgba(0, 0, 0, 0.13) !important;
          font-size: 14px !important;
          letter-spacing: 0.06em !important;
          text-transform: uppercase !important;
        }

        .imp-object-list .imp-object-list-item:hover,
        .imp-object-list .imp-object-list-item.imp-selected,
        .imp-object-list .imp-object-list-item.active {
          background: var(--tertiary, #d8cda5) !important;
          border-color: var(--tertiary, #d8cda5) !important;
          color: var(--neutral-dark, #162520) !important;
        }

        .imp-object-menu .imp-menu-close-button,
        .imp-object-menu .imp-ui-element.imp-menu-close-button,
        .imp-menu-close-button,
        .imp-ui-element.imp-menu-close-button {
          display: flex !important;
          align-items: center !important;
          justify-content: center !important;
          position: absolute !important;
          top: calc(16px + env(safe-area-inset-top)) !important;
          right: 16px !important;
          width: 48px !important;
          height: 48px !important;
          margin: 0 !important;
          border: 0 !important;
          border-radius: 14px !important;
          background: #fff !important;
          color: #000 !important;
          box-shadow: 0 12px 30px rgba(0, 0, 0, 0.22) !important;
          opacity: 0 !important;
          visibility: hidden !important;
          pointer-events: none !important;
          transform: scale(0.92) !important;
          transition: opacity 160ms ease, transform 160ms ease, visibility 160ms ease !important;
          z-index: 100000 !important;
        }

        .imp-object-menu.imp-mobile.imp-active .imp-menu-close-button,
        .imp-object-menu.imp-mobile.imp-active .imp-ui-element.imp-menu-close-button,
        .imp-object-menu.imp-active .imp-menu-close-button,
        .imp-object-menu.imp-active .imp-ui-element.imp-menu-close-button {
          opacity: 1 !important;
          visibility: visible !important;
          pointer-events: auto !important;
          transform: scale(1) !important;
        }

        .imp-menu-close-button svg,
        .imp-ui-element.imp-menu-close-button svg {
          display: none !important;
        }

        .imp-menu-close-button::before,
        .imp-menu-close-button::after,
        .imp-ui-element.imp-menu-close-button::before,
        .imp-ui-element.imp-menu-close-button::after {
          content: "" !important;
          position: absolute !important;
          width: 23px !important;
          height: 2px !important;
          border-radius: 999px !important;
          background: #000 !important;
        }

        .imp-menu-close-button::before,
        .imp-ui-element.imp-menu-close-button::before {
          transform: rotate(45deg) !important;
        }

        .imp-menu-close-button::after,
        .imp-ui-element.imp-menu-close-button::after {
          transform: rotate(-45deg) !important;
        }

        .imp-ui-wrap:not(.rs-imp-map-scope) .imp-ui,
        .imp-ui-wrap:not(.rs-imp-map-scope) .imp-ui-top-left,
        .imp-ui-wrap:not(.rs-imp-map-scope) .imp-ui-top-right,
        .imp-ui-wrap:not(.rs-imp-map-scope) .imp-ui-bottom-left {
          overflow: visible !important;
        }

        .imp-ui-wrap:not(.rs-imp-map-scope) .imp-ui-top-left {
          position: absolute !important;
          top: calc(14px + env(safe-area-inset-top)) !important;
          left: 16px !important;
          right: auto !important;
          width: min(330px, calc(100% - 114px)) !important;
          min-width: 0 !important;
          max-width: none !important;
        }

        .imp-ui-wrap:not(.rs-imp-map-scope) .imp-ui-top-right {
          position: absolute !important;
          top: calc(14px + env(safe-area-inset-top)) !important;
          right: 16px !important;
          width: var(--rs-imp-mobile-control-height) !important;
          height: var(--rs-imp-mobile-control-height) !important;
        }

        .imp-ui-wrap:not(.rs-imp-map-scope) .imp-ui-layers-menu-wrap {
          position: relative !important;
          width: 100% !important;
          max-width: none !important;
        }

        .imp-ui-wrap:not(.rs-imp-map-scope) .imp-ui-layers-menu-wrap > svg,
        .imp-ui-wrap:not(.rs-imp-map-scope) .imp-ui-layers-menu-wrap .imp-icon {
          display: none !important;
        }

        .imp-ui-wrap:not(.rs-imp-map-scope) .imp-ui-layers-select {
          appearance: none !important;
          -webkit-appearance: none !important;
          width: 100% !important;
          min-width: 0 !important;
          max-width: none !important;
          height: var(--rs-imp-mobile-control-height) !important;
          min-height: var(--rs-imp-mobile-control-height) !important;
          max-height: var(--rs-imp-mobile-control-height) !important;
          box-sizing: border-box !important;
          padding: 0 48px 0 24px !important;
          border: 0 !important;
          border-radius: 999px !important;
          background-color: rgba(255, 255, 255, 0.94) !important;
          background-image: linear-gradient(45deg, transparent 50%, currentColor 50%), linear-gradient(135deg, currentColor 50%, transparent 50%) !important;
          background-position: calc(100% - 25px) 50%, calc(100% - 17px) 50% !important;
          background-size: 8px 8px, 8px 8px !important;
          background-repeat: no-repeat !important;
          color: var(--neutral-dark, #162520) !important;
          box-shadow: 0 18px 38px rgba(13, 28, 23, 0.14) !important;
          font-size: 14px !important;
          font-weight: 500 !important;
          letter-spacing: 0.08em !important;
          text-transform: uppercase !important;
        }

        .imp-ui-wrap:not(.rs-imp-map-scope) .imp-menu-button {
          position: absolute !important;
          top: 0 !important;
          right: 0 !important;
          width: var(--rs-imp-mobile-control-height) !important;
          min-width: var(--rs-imp-mobile-control-height) !important;
          max-width: var(--rs-imp-mobile-control-height) !important;
          height: var(--rs-imp-mobile-control-height) !important;
          min-height: var(--rs-imp-mobile-control-height) !important;
          max-height: var(--rs-imp-mobile-control-height) !important;
          margin: 0 !important;
          border: 0 !important;
          border-radius: 999px !important;
          box-sizing: border-box !important;
          background: rgba(255, 255, 255, 0.96) !important;
          box-shadow: 0 18px 38px rgba(13, 28, 23, 0.14) !important;
          display: flex !important;
          align-items: center !important;
          justify-content: center !important;
        }

        .imp-ui-wrap:not(.rs-imp-map-scope) .imp-menu-button svg {
          display: none !important;
        }

        .imp-ui-wrap:not(.rs-imp-map-scope) .imp-menu-button::before {
          content: "" !important;
          width: 22px !important;
          height: 16px !important;
          background: linear-gradient(#000, #000) 0 0 / 22px 2px no-repeat,
                      linear-gradient(#000, #000) 0 7px / 22px 2px no-repeat,
                      linear-gradient(#000, #000) 0 14px / 22px 2px no-repeat !important;
          display: block !important;
        }

        .imp-ui-wrap:not(.rs-imp-map-scope) .imp-ui-bottom-left {
          position: absolute !important;
          top: calc(100% + 64px) !important;
          left: 0 !important;
          right: 0 !important;
          width: 100% !important;
          display: flex !important;
          justify-content: center !important;
        }

        .imp-ui-wrap:not(.rs-imp-map-scope) .imp-fullscreen-button {
          width: min(280px, calc(100% - 96px)) !important;
          min-width: 220px !important;
          max-width: 280px !important;
          height: var(--rs-imp-mobile-control-height) !important;
          min-height: var(--rs-imp-mobile-control-height) !important;
          max-height: var(--rs-imp-mobile-control-height) !important;
          padding: 0 22px !important;
          border: 0 !important;
          border-radius: 999px !important;
          background: rgba(255, 255, 255, 0.94) !important;
          box-shadow: 0 16px 34px rgba(13, 28, 23, 0.14) !important;
          display: flex !important;
          align-items: center !important;
          justify-content: center !important;
          gap: 10px !important;
        }

        .imp-fullscreen-button[data-image-map-id*="[fullscreen]"],
        .imp-ui-element.imp-fullscreen-button[data-image-map-id*="[fullscreen]"] {
          position: fixed !important;
          left: 50% !important;
          right: auto !important;
          bottom: calc(24px + env(safe-area-inset-bottom)) !important;
          top: auto !important;
          transform: translateX(-50%) !important;
          z-index: 100001 !important;
          width: min(240px, calc(100vw - 72px)) !important;
          min-width: 170px !important;
          max-width: 240px !important;
          height: var(--rs-imp-mobile-control-height) !important;
          min-height: var(--rs-imp-mobile-control-height) !important;
          max-height: var(--rs-imp-mobile-control-height) !important;
          padding: 0 22px !important;
          border: 0 !important;
          border-radius: 999px !important;
          background: rgba(255, 255, 255, 0.94) !important;
          color: #1a1814 !important;
          box-shadow: 0 16px 34px rgba(0, 0, 0, 0.28) !important;
          display: flex !important;
          align-items: center !important;
          justify-content: center !important;
          gap: 10px !important;
        }

        .imp-tooltip {
          max-width: calc(100vw - 28px) !important;
        }

        .imp-tooltip .squares-element,
        .imp-tooltip .imp-tooltip-content > div,
        .imp-tooltip .imp-tooltip-plain-text > div {
          border-radius: 22px !important;
        }

        .rs-map-tooltip {
          max-width: calc(100vw - 42px) !important;
        }

        .rs-map-tooltip__image {
          height: 168px !important;
          margin-bottom: 20px !important;
        }

        .rs-map-tooltip__top {
          gap: 12px !important;
          margin-bottom: 18px !important;
        }

        .rs-map-tooltip__title {
          font-size: clamp(24px, 7vw, 32px) !important;
        }

        .rs-map-tooltip__price {
          font-size: clamp(32px, 9vw, 42px) !important;
          margin-bottom: 22px !important;
        }

        .rs-map-tooltip__meta {
          display: grid !important;
          grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
          gap: 12px !important;
          align-items: start !important;
          margin-bottom: 28px !important;
        }

        .rs-map-tooltip__meta-item {
          flex-direction: column !important;
          align-items: center !important;
          justify-content: flex-start !important;
          gap: 8px !important;
          text-align: center !important;
          white-space: normal !important;
          font-size: 13px !important;
          line-height: 1.15 !important;
        }

        .rs-map-tooltip__meta-icon {
          width: 24px !important;
          height: 24px !important;
          flex-basis: 24px !important;
        }

        .rs-map-tooltip__button {
          min-height: 56px !important;
        }
      }
    </style>
    <?php
}, 99);

add_action('wp_footer', function () {
    if (is_admin()) {
        return;
    }
    ?>
    <script id="rs-imp-fullscreen-label-fix">
      (function(){
        var scheduled = false;

        function syncFullscreenLabels(){
          document.querySelectorAll('.imp-fullscreen-button').forEach(function(button){
            var rawId = button.getAttribute('data-image-map-id') || '';
            var isClose = rawId.indexOf('[fullscreen]') !== -1;
            var text = isClose ? 'Zavrieť' : 'Na celú obrazovku';
            var label = button.querySelector('.imp-fullscreen-label');
            if(!label){
              label = document.createElement('span');
              label.className = 'imp-fullscreen-label';
              button.appendChild(label);
            }
            if(label.textContent !== text){
              label.textContent = text;
            }
          });
        }

        function scheduleSync(){
          if(scheduled){ return; }
          scheduled = true;
          setTimeout(function(){
            scheduled = false;
            syncFullscreenLabels();
          }, 50);
        }

        document.addEventListener('DOMContentLoaded', scheduleSync);
        window.addEventListener('load', scheduleSync);
        document.addEventListener('click', function(){
          setTimeout(scheduleSync, 80);
          setTimeout(scheduleSync, 350);
        }, true);

        if(typeof MutationObserver !== 'undefined'){
          new MutationObserver(scheduleSync).observe(document.documentElement, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['data-image-map-id', 'class']
          });
        }
      })();
    </script>
    <?php
}, 99);

