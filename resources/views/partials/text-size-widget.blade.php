{{--
    Text Size Preference — Interactive Accessibility Widget
    ────────────────────────────────────────────────────────
    Include this partial immediately before the closing </body> tag of every page:
        @include('partials.text-size-widget')

    Features:
      • Floats safely above mobile bottom navigation bars (never blocks tabs)
      • Compact 44px circular touch target on mobile devices
      • Freely draggable: touch/mouse drag to any position on screen
      • One-click [Bigger] and [Smaller] stepper buttons (ideal for elderly users)
      • Direct preset chips: 100%, 125%, 150%, 175%, 200%
      • Persistent across all pages via localStorage
--}}

<div class="ts-widget" id="tsWidget" aria-live="polite">

    <!-- Flyout Control Panel -->
    <div class="ts-panel" id="tsPanel" role="dialog" aria-modal="false" aria-label="Text Size Controls">
        
        <!-- Header: Title + Current Percentage -->
        <div class="ts-panel-header">
            <span class="ts-panel-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 2a10 10 0 0 1 0 20v-20z" fill="currentColor"></path>
                </svg>
                Text Size / Ukuran Teks
            </span>
            <span class="ts-badge" id="tsBadge" aria-label="Current text size">100%</span>
        </div>

        <!-- Stepper: Bigger & Smaller Buttons (Primary Elderly Interaction) -->
        <div class="ts-stepper">
            <button type="button" class="ts-step-btn" id="tsDecBtn" title="Make text smaller" aria-label="Decrease text size">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Smaller</span>
            </button>

            <button type="button" class="ts-step-btn" id="tsIncBtn" title="Make text bigger" aria-label="Increase text size">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Bigger</span>
            </button>
        </div>

        <!-- Direct Preset Chips -->
        <span class="ts-presets-label">Quick Presets:</span>
        <div class="ts-presets" role="radiogroup" aria-label="Text size presets">
            <button type="button" class="ts-chip" data-size="text-size-md" role="radio" aria-checked="true" title="Normal default size (100%)">
                <strong>100%</strong>
                <span class="ts-chip-sub">Normal</span>
            </button>
            <button type="button" class="ts-chip" data-size="text-size-lg" role="radio" aria-checked="false" title="Large size (125%)">
                <strong>125%</strong>
                <span class="ts-chip-sub">Large</span>
            </button>
            <button type="button" class="ts-chip" data-size="text-size-xl" role="radio" aria-checked="false" title="Extra Large — Elderly friendly (150%)">
                <strong>150%</strong>
                <span class="ts-chip-sub">Elderly</span>
            </button>
            <button type="button" class="ts-chip" data-size="text-size-2xl" role="radio" aria-checked="false" title="Huge size (175%)">
                <strong>175%</strong>
                <span class="ts-chip-sub">Huge</span>
            </button>
            <button type="button" class="ts-chip" data-size="text-size-3xl" role="radio" aria-checked="false" title="Maximum size — Low Vision (200%)">
                <strong>200%</strong>
                <span class="ts-chip-sub">Max</span>
            </button>
        </div>

        <!-- Footer: Reset, Reset Position & Close -->
        <div class="ts-panel-footer">
            <button type="button" class="ts-footer-btn ts-underline" id="tsResetBtn" title="Reset to standard 100% size">
                Reset Size
            </button>
            <button type="button" class="ts-footer-btn" id="tsResetPosBtn" title="Reset widget position to safe corner">
                Reset Position
            </button>
            <button type="button" class="ts-footer-btn" id="tsCloseBtn" title="Close text size settings">
                Close
            </button>
        </div>
    </div>

    <!-- Floating Toggle Pill / Circle (Draggable & Accessible) -->
    <button type="button" class="ts-toggle-btn" id="tsToggleBtn" aria-expanded="false" aria-controls="tsPanel" aria-label="Open text size accessibility menu (Draggable)" title="Adjust text size (Drag to move)">
        <span class="ts-toggle-icon" aria-hidden="true">
            <span class="ts-a-big">A</span><span class="ts-a-small">a</span>
        </span>
        <span class="ts-toggle-text">Text Size</span>
    </button>
</div>

<script>
/**
 * Text Size Accessibility Controller with Drag-to-Move & Safe Boundaries
 * ───────────────────────────────────────────────────────────────────────
 */
(function() {
    'use strict';

    var SCALE_ORDER = [
        'text-size-xs',  // 85%
        'text-size-md',  // 100% (Default)
        'text-size-lg',  // 125%
        'text-size-xl',  // 150% (Elderly)
        'text-size-2xl', // 175%
        'text-size-3xl'  // 200% (Maximum)
    ];

    var SCALE_LABELS = {
        'text-size-xs':  '85%',
        'text-size-md':  '100%',
        'text-size-lg':  '125%',
        'text-size-xl':  '150%',
        'text-size-2xl': '175%',
        'text-size-3xl': '200%'
    };

    var DEFAULT_SIZE = 'text-size-md';
    var STORAGE_KEY  = 'pointms-text-size';
    var POS_STORAGE  = 'pointms-widget-pos';

    var html        = document.documentElement;
    var widget      = document.getElementById('tsWidget');
    var panel       = document.getElementById('tsPanel');
    var toggleBtn   = document.getElementById('tsToggleBtn');
    var decBtn      = document.getElementById('tsDecBtn');
    var incBtn      = document.getElementById('tsIncBtn');
    var resetBtn    = document.getElementById('tsResetBtn');
    var resetPosBtn = document.getElementById('tsResetPosBtn');
    var closeBtn    = document.getElementById('tsCloseBtn');
    var badge       = document.getElementById('tsBadge');
    var chips       = document.querySelectorAll('.ts-chip');

    // ── Position Adjustment & Adaptation ──
    function adaptPanelOrientation() {
        if (!widget) return;
        var rect = widget.getBoundingClientRect();
        var isLeftHalf = rect.left < (window.innerWidth / 2);
        var isTopHalf  = rect.top < (window.innerHeight / 2);

        widget.classList.toggle('ts-align-left', isLeftHalf);
        widget.classList.toggle('ts-align-right', !isLeftHalf);
        widget.classList.toggle('ts-drop-down', isTopHalf);
    }

    function restoreCustomPosition() {
        try {
            var raw = localStorage.getItem(POS_STORAGE);
            if (raw) {
                var pos = JSON.parse(raw);
                if (pos && typeof pos.left === 'number' && typeof pos.top === 'number') {
                    // Validate within screen bounds
                    var maxL = window.innerWidth - 60;
                    var maxT = window.innerHeight - 60;
                    var l = Math.max(10, Math.min(maxL, pos.left));
                    var t = Math.max(10, Math.min(maxT, pos.top));

                    widget.style.bottom = 'auto';
                    widget.style.right = 'auto';
                    widget.style.left = l + 'px';
                    widget.style.top = t + 'px';
                    adaptPanelOrientation();
                }
            }
        } catch (e) {}
    }

    function resetPosition() {
        try {
            localStorage.removeItem(POS_STORAGE);
        } catch (e) {}
        if (widget) {
            widget.style.left = '';
            widget.style.top = '';
            widget.style.bottom = '';
            widget.style.right = '';
            widget.classList.remove('ts-align-left', 'ts-drop-down');
            widget.classList.add('ts-align-right');
        }
    }

    // ── Draggable Functionality ──
    var isDragging = false;
    var hasMoved = false;
    var startX = 0, startY = 0;
    var startLeft = 0, startTop = 0;

    if (toggleBtn) {
        toggleBtn.addEventListener('pointerdown', function(e) {
            if (e.button !== 0 && e.pointerType === 'mouse') return;

            isDragging = true;
            hasMoved = false;
            startX = e.clientX;
            startY = e.clientY;

            var rect = widget.getBoundingClientRect();
            startLeft = rect.left;
            startTop = rect.top;

            toggleBtn.setPointerCapture(e.pointerId);
            toggleBtn.classList.add('ts-dragging');
        });

        toggleBtn.addEventListener('pointermove', function(e) {
            if (!isDragging) return;

            var dx = e.clientX - startX;
            var dy = e.clientY - startY;

            if (Math.abs(dx) > 5 || Math.abs(dy) > 5) {
                hasMoved = true;
                panel.classList.remove('ts-open'); // Close panel while dragging

                var newLeft = startLeft + dx;
                var newTop  = startTop + dy;

                // Clamp within screen viewport
                var btnW = toggleBtn.offsetWidth || 44;
                var btnH = toggleBtn.offsetHeight || 44;
                newLeft = Math.max(8, Math.min(window.innerWidth - btnW - 8, newLeft));
                newTop  = Math.max(8, Math.min(window.innerHeight - btnH - 8, newTop));

                widget.style.bottom = 'auto';
                widget.style.right = 'auto';
                widget.style.left = newLeft + 'px';
                widget.style.top = newTop + 'px';

                adaptPanelOrientation();
            }
        });

        toggleBtn.addEventListener('pointerup', function(e) {
            if (!isDragging) return;
            isDragging = false;
            toggleBtn.classList.remove('ts-dragging');
            try { toggleBtn.releasePointerCapture(e.pointerId); } catch (err) {}

            if (hasMoved) {
                // Save custom position
                var rect = widget.getBoundingClientRect();
                try {
                    localStorage.setItem(POS_STORAGE, JSON.stringify({
                        left: Math.round(rect.left),
                        top: Math.round(rect.top)
                    }));
                } catch (err) {}
            } else {
                // Click without drag: Toggle panel
                togglePanel();
            }
        });

        toggleBtn.addEventListener('pointercancel', function(e) {
            isDragging = false;
            toggleBtn.classList.remove('ts-dragging');
            try { toggleBtn.releasePointerCapture(e.pointerId); } catch (err) {}
        });
    }

    // ── Sizing Functions ──
    function getCurrentSize() {
        for (var i = 0; i < SCALE_ORDER.length; i++) {
            if (html.classList.contains(SCALE_ORDER[i])) {
                return SCALE_ORDER[i];
            }
        }
        return DEFAULT_SIZE;
    }

    function applySize(sizeClass) {
        if (SCALE_ORDER.indexOf(sizeClass) === -1) {
            sizeClass = DEFAULT_SIZE;
        }

        // 1. Update <html> classes
        for (var i = 0; i < SCALE_ORDER.length; i++) {
            html.classList.remove(SCALE_ORDER[i]);
        }
        if (sizeClass !== DEFAULT_SIZE) {
            html.classList.add(sizeClass);
        }

        // 2. Update Badge text
        var pct = SCALE_LABELS[sizeClass] || '100%';
        if (badge) {
            badge.textContent = pct;
        }

        // 3. Update Chips active state
        for (var j = 0; j < chips.length; j++) {
            var chip = chips[j];
            var isCurrent = (chip.getAttribute('data-size') === sizeClass);
            if (isCurrent) {
                chip.classList.add('ts-active');
                chip.setAttribute('aria-checked', 'true');
            } else {
                chip.classList.remove('ts-active');
                chip.setAttribute('aria-checked', 'false');
            }
        }

        // 4. Update Stepper disabled states
        var currentIndex = SCALE_ORDER.indexOf(sizeClass);
        if (decBtn) {
            decBtn.disabled = (currentIndex <= 0);
        }
        if (incBtn) {
            incBtn.disabled = (currentIndex >= SCALE_ORDER.length - 1);
        }

        // 5. Persist to localStorage
        try {
            localStorage.setItem(STORAGE_KEY, sizeClass);
        } catch (e) {}
    }

    function stepSize(direction) {
        var current = getCurrentSize();
        var index = SCALE_ORDER.indexOf(current);
        if (index === -1) index = SCALE_ORDER.indexOf(DEFAULT_SIZE);

        var newIndex = index + direction;
        if (newIndex >= 0 && newIndex < SCALE_ORDER.length) {
            applySize(SCALE_ORDER[newIndex]);
        }
    }

    function togglePanel(open) {
        var isOpen = (typeof open === 'boolean') ? open : !panel.classList.contains('ts-open');
        if (isOpen) {
            adaptPanelOrientation();
            panel.classList.add('ts-open');
            toggleBtn.setAttribute('aria-expanded', 'true');
        } else {
            panel.classList.remove('ts-open');
            toggleBtn.setAttribute('aria-expanded', 'false');
        }
    }

    // ── Event Handlers ──
    if (incBtn) {
        incBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            stepSize(1);
        });
    }
    if (decBtn) {
        decBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            stepSize(-1);
        });
    }

    for (var k = 0; k < chips.length; k++) {
        chips[k].addEventListener('click', function(e) {
            e.stopPropagation();
            var targetSize = this.getAttribute('data-size');
            applySize(targetSize);
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            applySize(DEFAULT_SIZE);
        });
    }

    if (resetPosBtn) {
        resetPosBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            resetPosition();
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            togglePanel(false);
        });
    }

    document.addEventListener('click', function(e) {
        if (widget && !widget.contains(e.target)) {
            togglePanel(false);
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            togglePanel(false);
        }
    });

    window.addEventListener('resize', function() {
        if (widget && widget.style.left) {
            restoreCustomPosition();
        }
    });

    // ── Initialize ──
    applySize(getCurrentSize());
    restoreCustomPosition();

})();
</script>
