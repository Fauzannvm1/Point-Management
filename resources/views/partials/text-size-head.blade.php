{{--
    Text Size Preference — Early Restore Script & CSS Styles
    ─────────────────────────────────────────────────────────────
    Include this partial inside the <head> of every page:
        @include('partials.text-size-head')

    Why it's in <head>:
      1. Reads saved text-size & widget position from localStorage before rendering,
         preventing any Flash of Wrong-Sized Text (FOUC).
      2. Provides universal, device-responsive text scaling:
         - Desktop: Scales up to 200% (WCAG 2.1 AA accessibility benchmark)
         - Tablet: Responsive scaling up to 175%
         - Mobile: Responsive scaling up to 155% (maintains mobile layout integrity)
      3. Supplies understated, elegant, non-flashy neutral styling for the widget.
      4. Safe positioning: Floats above mobile navigation bars to never block tabs.
--}}

<!-- Early Font Size Restore Script (prevents flicker) -->
<script>
(function() {
    try {
        var validSizes = ['text-size-xs', 'text-size-md', 'text-size-lg', 'text-size-xl', 'text-size-2xl', 'text-size-3xl'];
        var legacyMap = {
            'text-xs': 'text-size-xs',
            'text-sm': 'text-size-md',
            'text-md': 'text-size-md',
            'text-lg': 'text-size-lg',
            'text-xl': 'text-size-xl'
        };
        var saved = localStorage.getItem('pointms-text-size');
        if (saved && legacyMap[saved]) {
            saved = legacyMap[saved];
            localStorage.setItem('pointms-text-size', saved);
        }
        if (saved && validSizes.indexOf(saved) !== -1) {
            document.documentElement.classList.add(saved);
        }
    } catch (e) {
        /* Ignore storage errors in restricted contexts */
    }
})();
</script>

<!-- Responsive Scaling Rules & Understated Widget Styles -->
<style id="pointms-text-size-styles">
    /* ══════════════════════════════════════════════════════════
       RESPONSIVE TEXT SCALING (Desktop / Default)
       Scales up to 200% (WCAG 2.1 AA accessibility benchmark)
       ══════════════════════════════════════════════════════════ */
    html.text-size-xs  { zoom: 0.88; }
    html.text-size-md  { zoom: 1.00; }
    html.text-size-lg  { zoom: 1.25; }
    html.text-size-xl  { zoom: 1.50; }
    html.text-size-2xl { zoom: 1.75; }
    html.text-size-3xl { zoom: 2.00; }

    /* ── Tablet Responsive Scaling (max-width: 1024px) ── */
    @media (max-width: 1024px) {
        html.text-size-xs  { zoom: 0.90; }
        html.text-size-md  { zoom: 1.00; }
        html.text-size-lg  { zoom: 1.20; }
        html.text-size-xl  { zoom: 1.38; }
        html.text-size-2xl { zoom: 1.58; }
        html.text-size-3xl { zoom: 1.75; }
    }

    /* ── Mobile Responsive Scaling (max-width: 640px) ── */
    @media (max-width: 640px) {
        html.text-size-xs  { zoom: 0.92; }
        html.text-size-md  { zoom: 1.00; }
        html.text-size-lg  { zoom: 1.12; }
        html.text-size-xl  { zoom: 1.25; }
        html.text-size-2xl { zoom: 1.40; }
        html.text-size-3xl { zoom: 1.55; }
    }

    /* Fallback for browsers that do not support zoom */
    @supports not (zoom: 1) {
        html.text-size-xs  { font-size: 13.5px; }
        html.text-size-md  { font-size: 16px;   }
        html.text-size-lg  { font-size: 20px;   }
        html.text-size-xl  { font-size: 24px;   }
        html.text-size-2xl { font-size: 28px;   }
        html.text-size-3xl { font-size: 32px;   }

        @media (max-width: 640px) {
            html.text-size-lg  { font-size: 18px; }
            html.text-size-xl  { font-size: 20px; }
            html.text-size-2xl { font-size: 22.5px; }
            html.text-size-3xl { font-size: 25px; }
        }
    }

    /* ══════════════════════════════════════════════════════════
       ELEGANT, NON-FLASHY ACCESSIBILITY WIDGET (.ts-*)
       Neutral slate palette, unobtrusive, fully mobile safe
       ══════════════════════════════════════════════════════════ */
    .ts-widget {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 999999;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
        pointer-events: none;
        touch-action: none;
    }

    .ts-widget * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    /* Safely float ABOVE mobile bottom navigation bars */
    @media (max-width: 768px) {
        .ts-widget {
            bottom: calc(76px + env(safe-area-inset-bottom, 0px));
            right: 14px;
        }
    }

    /* Dynamic alignment based on screen quadrant */
    .ts-widget.ts-align-left {
        align-items: flex-start;
    }
    .ts-widget.ts-align-right {
        align-items: flex-end;
    }
    .ts-widget.ts-drop-down {
        flex-direction: column-reverse;
    }

    /* Floating toggle button: Clean, non-flashy neutral pill / circle */
    .ts-toggle-btn {
        pointer-events: auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        background: #1e293b;
        color: #f8fafc;
        border: 1.5px solid #334155;
        border-radius: 9999px;
        padding: 8px 14px;
        box-shadow: 0 4px 14px -2px rgba(15, 23, 42, 0.28), 0 2px 4px -1px rgba(15, 23, 42, 0.12);
        cursor: grab;
        font-weight: 600;
        font-size: 12.5px;
        letter-spacing: 0.01em;
        transition: background 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
        user-select: none;
        -webkit-user-select: none;
        text-decoration: none;
        touch-action: none;
    }

    .ts-toggle-btn:hover {
        background: #0f172a;
        border-color: #475569;
        box-shadow: 0 6px 18px -2px rgba(15, 23, 42, 0.35);
    }

    .ts-toggle-btn:active,
    .ts-toggle-btn.ts-dragging {
        cursor: grabbing;
        transform: scale(0.96);
    }

    .ts-toggle-btn:focus-visible {
        outline: 2px solid #64748b;
        outline-offset: 2px;
    }

    .ts-toggle-icon {
        display: inline-flex;
        align-items: baseline;
        font-weight: 800;
        line-height: 1;
        color: #e2e8f0;
    }

    .ts-toggle-icon .ts-a-big {
        font-size: 15px;
    }

    .ts-toggle-icon .ts-a-small {
        font-size: 10px;
        opacity: 0.8;
    }

    .ts-toggle-text {
        font-size: 12px;
        font-weight: 600;
        color: #f1f5f9;
    }

    /* On mobile / small screens: Compact 42px circular button */
    @media (max-width: 768px) {
        .ts-toggle-btn {
            width: 44px;
            height: 44px;
            padding: 0;
            border-radius: 50%;
            box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.38);
        }
        .ts-toggle-text {
            display: none;
        }
        .ts-toggle-icon .ts-a-big {
            font-size: 16px;
        }
        .ts-toggle-icon .ts-a-small {
            font-size: 11px;
        }
    }

    /* Flyout panel: Crisp white card with soft neutral border and shadow */
    .ts-panel {
        pointer-events: auto;
        display: none;
        flex-direction: column;
        gap: 10px;
        width: 290px;
        max-width: calc(100vw - 28px);
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 14px;
        box-shadow: 0 12px 30px -4px rgba(15, 23, 42, 0.16), 0 4px 10px -2px rgba(15, 23, 42, 0.08);
        animation: tsFadeIn 0.18s ease-out;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
    }

    .ts-panel.ts-open {
        display: flex;
    }

    @keyframes tsFadeIn {
        from {
            opacity: 0;
            transform: translateY(6px) scale(0.97);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Panel Header */
    .ts-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
    }

    .ts-panel-title {
        font-size: 12.5px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .ts-badge {
        font-size: 11px;
        font-weight: 700;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
        padding: 2px 7px;
        border-radius: 9999px;
    }

    /* Stepper Buttons (Bigger / Smaller) */
    .ts-stepper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 7px;
    }

    .ts-step-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 8px 10px;
        font-size: 12.5px;
        font-weight: 600;
        color: #1e293b;
        cursor: pointer;
        transition: all 0.15s ease;
        user-select: none;
    }

    .ts-step-btn:hover:not(:disabled) {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }

    .ts-step-btn:active:not(:disabled) {
        transform: translateY(1px);
    }

    .ts-step-btn:disabled {
        opacity: 0.38;
        cursor: not-allowed;
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    .ts-step-btn:focus-visible {
        outline: 2px solid #64748b;
        outline-offset: 1px;
    }

    /* Preset Chips */
    .ts-presets-label {
        font-size: 9.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748b;
        margin-top: 1px;
    }

    .ts-presets {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 4px;
    }

    .ts-chip {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 2px;
        font-size: 11px;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s ease;
        line-height: 1.2;
    }

    .ts-chip span.ts-chip-sub {
        font-size: 8px;
        font-weight: 500;
        color: #94a3b8;
        margin-top: 2px;
    }

    .ts-chip:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #1e293b;
    }

    /* Active chip: Solid calm slate, no electric colors */
    .ts-chip.ts-active {
        background: #1e293b;
        border-color: #1e293b;
        color: #ffffff;
        box-shadow: 0 2px 6px -1px rgba(15, 23, 42, 0.2);
    }

    .ts-chip.ts-active span.ts-chip-sub {
        color: #cbd5e1;
    }

    .ts-chip:focus-visible {
        outline: 2px solid #64748b;
        outline-offset: 1px;
    }

    /* Footer reset and close */
    .ts-panel-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 6px;
        border-top: 1px solid #f1f5f9;
        font-size: 11px;
    }

    .ts-footer-btn {
        background: none;
        border: none;
        color: #64748b;
        font-size: 11px;
        font-weight: 500;
        cursor: pointer;
        padding: 4px;
        transition: color 0.15s ease;
    }

    .ts-footer-btn:hover {
        color: #0f172a;
    }

    .ts-footer-btn.ts-underline {
        text-decoration: underline;
        text-underline-offset: 2px;
    }
</style>
