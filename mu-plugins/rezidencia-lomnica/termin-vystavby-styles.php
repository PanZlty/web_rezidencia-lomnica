<?php
/**
 * Construction progress styles loaded as a MU-plugin module.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', static function () {
    if (is_admin()) {
        return;
    }

    wp_register_style('rezidencia-lomnica-termin-vystavby-styles', false, [], null);
    wp_enqueue_style('rezidencia-lomnica-termin-vystavby-styles');
    wp_add_inline_style('rezidencia-lomnica-termin-vystavby-styles', <<<'CSS'
.rs-construction-progress-wrap,
.brxe-shortcode > strong > .rs-construction-progress-wrap,
.brxe-shortcode:has(> .construction-progress),
.brxe-shortcode:has(> strong > .construction-progress),
.brxe-shortcode > strong:has(> .construction-progress) {
  display: block;
  width: 100%;
  font-weight: inherit;
}

.construction-progress,
.construction-progress * {
  box-sizing: border-box;
}

.construction-progress {
  width: min(100%, 86rem);
  margin: 0 auto;
  padding: clamp(2.2rem, 3vw, 3rem);
  border-radius: var(--radius-l, 2rem);
  background: var(--white, #ffffff);
  border: 1px solid rgba(255, 255, 255, 0.28);
  color: var(--heading, #202a2f);
  box-shadow: 0 2.4rem 6rem rgba(0, 0, 0, 0.16);
  font-family: inherit;
  font-weight: 400;
  text-align: left;
}

.construction-progress__top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: clamp(1.6rem, 3vw, 3rem);
  margin-bottom: 2rem;
}

.construction-progress__content {
  min-width: 0;
}

.construction-progress__title {
  color: var(--heading, #202a2f);
  font-family: "Cormorant Garamond", serif;
  font-size: clamp(2.6rem, 3vw, 4rem);
  line-height: 1.05;
  font-weight: 700;
}

.construction-progress__label {
  margin-top: 0.7rem;
  color: var(--text, #596469);
  font-size: var(--text-s, 1.5rem);
  line-height: 1.45;
  font-weight: 500;
}

.construction-progress__days {
  flex: 0 0 auto;
  min-width: 10.8rem;
  padding: 0;
  border-radius: 0;
  background: transparent;
  color: var(--primary, #4e707a);
  text-align: right;
  white-space: nowrap;
}

.construction-progress__days strong {
  display: block;
  color: inherit;
  font-size: clamp(3rem, 3.6vw, 4.6rem);
  line-height: 0.9;
  font-weight: 800;
}

.construction-progress__days span {
  display: block;
  margin-top: 0.45rem;
  color: var(--muted, #7d8788);
  font-size: 1.25rem;
  line-height: 1.2;
  font-weight: 600;
}

.construction-progress__bar {
  display: block;
  width: 100%;
  height: 1.2rem;
  border-radius: 999px;
  overflow: hidden;
  background: rgba(21, 38, 33, 0.1);
}

.construction-progress__fill {
  display: block;
  height: 100%;
  min-width: 0.8rem;
  border-radius: inherit;
  background: linear-gradient(90deg, var(--tertiary-d-2, #7f4637), var(--tertiary, #b86b50));
  transition: width 0.6s ease;
}

.construction-progress__bottom {
  display: flex;
  justify-content: space-between;
  gap: 1.5rem;
  margin-top: 1.2rem;
  color: var(--muted, #7d8788);
  font-size: 1.3rem;
  line-height: 1.35;
  font-weight: 600;
}

.construction-progress__bottom-label,
.construction-progress__bottom-value {
  display: inline;
}

@media (max-width: 767px) {
  .construction-progress {
    padding: 2rem;
    border-radius: 1.8rem;
    text-align: center;
  }

  .construction-progress__top {
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
  }

  .construction-progress__days {
    width: 100%;
    text-align: center;
  }

  .construction-progress__bottom {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.45rem 1rem;
    text-align: left;
  }

  .construction-progress__bottom > span:nth-child(2) {
    grid-column: 1 / -1;
    grid-row: 1;
    text-align: center;
  }

  .construction-progress__bottom > span:nth-child(1),
  .construction-progress__bottom > span:nth-child(3) {
    grid-row: 2;
    text-align: center;
  }

  .construction-progress__bottom-label,
  .construction-progress__bottom-value {
    display: block;
  }
}
CSS
    );
}, 100);