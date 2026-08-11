<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_footer', function () {
    if (is_admin()) {
        return;
    }
    ?>
    <style>
      .imp-object-list-item {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
      }

      .imp-object-list-item .rs-imp-list-label {
        display: inline-flex !important;
        align-items: center !important;
        min-width: 0 !important;
      }

      .rs-imp-list-pill {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 24px !important;
        padding: 0 10px !important;
        border-radius: 999px !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        white-space: nowrap !important;
        text-transform: none !important;
        letter-spacing: 0 !important;
        color: var(--white) !important;
        flex-shrink: 0 !important;
      }

      .rs-imp-list-pill--available {
        background: var(--status-available) !important;
      }

      .rs-imp-list-pill--reserved {
        background: var(--status-reserved) !important;
      }

      .rs-imp-list-pill--sold {
        background: var(--status-sold) !important;
      }
    </style>

    <script>
    (function () {
      function clean(value) {
        return String(value || '').replace(/\s+/g, ' ').trim();
      }

      function allUnits() {
        return window.RS_IMP_MAP_UNITS || window.RS_IMP_APARTMENTS || {};
      }

      function normalizeCode(text) {
        text = clean(text);

        var codeMatch = text.match(/\b([BSPG])\s*-?\s*(\d{1,2})\b/i);
        if (codeMatch) {
          var prefix = codeMatch[1].toUpperCase();
          var number = String(parseInt(codeMatch[2], 10)).padStart(2, '0');
          if (prefix === 'P' || prefix === 'G' || prefix === 'S') {
            return prefix + number;
          }
          return prefix + '-' + number;
        }

        var humanMatch = text.match(/\bByt\s*-?\s*(\d{1,2})\b/i);
        if (humanMatch) {
          return 'B-' + String(parseInt(humanMatch[1], 10)).padStart(2, '0');
        }

        var apartmentMatch = text.match(/\bApartm[aá]n\s*-?\s*([A-Z])\b/i);
        if (apartmentMatch) {
          return 'AP-' + apartmentMatch[1].toUpperCase();
        }

        var storageMatch = text.match(/\bSklad\s*-?\s*(\d{1,2})\b/i);
        if (storageMatch) {
          return 'S' + String(parseInt(storageMatch[1], 10)).padStart(2, '0');
        }

        var parkingMatch = text.match(/\b(?:Parkovacie miesto|Parking|Miesto)\s*-?\s*(\d{1,2})\b/i);
        if (parkingMatch) {
          return 'P' + String(parseInt(parkingMatch[1], 10)).padStart(2, '0');
        }

        return '';
      }

      function codeCandidates(text) {
        text = clean(text);
        var normalized = normalizeCode(text) || text;
        if (!normalized) return [];

        var out = [normalized];
        var prefixed = normalized.match(/^([PGS])\s*-?\s*(\d+)$/i);
        if (prefixed) {
          var number = String(parseInt(prefixed[2], 10));
          var padded = number.padStart(2, '0');
          if (prefixed[1].toUpperCase() === 'S') {
            out.push('S' + padded, 'S-' + padded);
          } else {
            out.push(padded, number, prefixed[1].toUpperCase() + padded);
          }
        } else if (/^\d+$/.test(normalized)) {
          var raw = String(parseInt(normalized, 10));
          var pad = raw.padStart(2, '0');
          out.push(raw, pad, 'P' + pad, 'G' + pad);
        }

        return out.filter(function (item, index) {
          return item && out.indexOf(item) === index;
        });
      }

      function statusLabel(status, unitType) {
        if (unitType === 'storage') {
          if (status === 'available') return 'Dostupný';
          if (status === 'reserved') return 'Rezervovaný';
          if (status === 'sold') return 'Predaný';
        }

        if (unitType === 'parking') {
          if (status === 'available') return 'Dostupné';
          if (status === 'reserved') return 'Rezervované';
          if (status === 'sold') return 'Predané';
        }

        if (status === 'available') return 'Na predaj';
        if (status === 'reserved') return 'Rezervovaný';
        if (status === 'sold') return 'Predaný';
        return '';
      }

      function getUnitByButtonText(text) {
        var units = allUnits();
        if (!units) return null;

        text = clean(text);
        if (text && units[text]) {
          return units[text];
        }

        var candidates = codeCandidates(text);
        for (var i = 0; i < candidates.length; i++) {
          if (units[candidates[i]]) {
            return units[candidates[i]];
          }
        }

        var lower = text.toLowerCase();
        for (var key in units) {
          if (!Object.prototype.hasOwnProperty.call(units, key)) continue;
          var item = units[key] || {};
          if (clean(item.title).toLowerCase() === lower) return item;
          if (candidates.map(function (candidate) { return candidate.toLowerCase(); }).indexOf(clean(item.code).toLowerCase()) > -1) return item;
          if (clean(item.code).toLowerCase() === lower) return item;
        }

        return null;
      }

      function getLabelNode(item) {
        var pill = item.querySelector('.rs-imp-list-pill');
        var text = clean(item.textContent || '');
        if (pill) {
          text = clean(text.replace(clean(pill.textContent || ''), ''));
        }

        var existing = item.querySelector('.rs-imp-list-label');
        if (existing) {
          existing.textContent = text;
          return existing;
        }

        item.textContent = '';
        var label = document.createElement('span');
        label.className = 'rs-imp-list-label';
        label.textContent = text;
        item.appendChild(label);
        return label;
      }

      function displayLabel(unit, fallbackText) {
        var code = clean(unit && unit.code);
        if ((unit && unit.type) === 'apartment' && /^AP-[A-Z]$/i.test(code)) {
          return code.toUpperCase();
        }
        return clean(fallbackText);
      }

      function decorateImageMapButtons() {
        var units = allUnits();
        if (!units) return;

        document.querySelectorAll('.imp-object-list-item').forEach(function (item) {
          var currentText = clean(item.querySelector('.rs-imp-list-label') ? item.querySelector('.rs-imp-list-label').textContent : item.textContent);
          var unit = getUnitByButtonText(currentText);
          if (!unit || !unit.status) return;

          var label = getLabelNode(item);
          label.textContent = displayLabel(unit, label.textContent);
          var status = unit.status;
          var statusText = statusLabel(status, unit.type || 'apartment');
          if (!statusText) return;

          item.setAttribute('data-rs-apartment-code', unit.code || normalizeCode(label.textContent));
          item.setAttribute('data-rs-map-unit-code', unit.code || normalizeCode(label.textContent));
          item.setAttribute('data-rs-map-unit-type', unit.type || 'apartment');
          item.setAttribute('data-rs-status', status);

          var pill = item.querySelector('.rs-imp-list-pill');
          if (!pill) {
            pill = document.createElement('span');
            item.appendChild(pill);
          }

          pill.className = 'rs-imp-list-pill rs-imp-list-pill--' + status;
          pill.textContent = statusText;
        });
      }

      function scheduleDecorate() {
        decorateImageMapButtons();
        setTimeout(decorateImageMapButtons, 250);
        setTimeout(decorateImageMapButtons, 800);
        setTimeout(decorateImageMapButtons, 1600);
      }

      document.addEventListener('DOMContentLoaded', scheduleDecorate);
      window.addEventListener('load', scheduleDecorate);
      document.addEventListener('click', function () {
        setTimeout(decorateImageMapButtons, 150);
        setTimeout(decorateImageMapButtons, 600);
      });
    })();
    </script>
    <?php
}, 50);

