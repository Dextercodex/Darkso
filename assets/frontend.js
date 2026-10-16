(function () {
    var data = window.darksoData || {};
    var maps = Array.isArray(data.maps) ? data.maps : [];
    var storageKey = data.storageKey || 'darkso_mode';
    var htmlClass = data.htmlClass || 'darkso-dark';
    var originalInline = new WeakMap();
    var cachedMapped = '';
    var mappedReady = false;
    var building = false;
    var mappedTag = null;
    var varsTag = null;

    function expandHex(hex) {
        hex = String(hex || '').trim().toLowerCase();
        if (/^#([0-9a-f]{3})$/.test(hex)) {
            return '#' + hex[1] + hex[1] + hex[2] + hex[2] + hex[3] + hex[3];
        }
        return hex;
    }

    function rgbToHex(r, g, b) {
        return (
            '#' +
            [r, g, b]
                .map(function (n) {
                    var h = Math.max(0, Math.min(255, n | 0)).toString(16);
                    return h.length === 1 ? '0' + h : h;
                })
                .join('')
        );
    }

    function parseToHex(value) {
        if (!value) {
            return '';
        }
        var v = String(value).trim().toLowerCase();
        if (/^#([0-9a-f]{3}|[0-9a-f]{6})$/.test(v)) {
            return expandHex(v);
        }
        var m = v.match(/^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/);
        if (m) {
            return rgbToHex(+m[1], +m[2], +m[3]);
        }
        m = v.match(/^rgba?\(\s*(\d+)\s+(\d+)\s+(\d+)/);
        if (m) {
            return rgbToHex(+m[1], +m[2], +m[3]);
        }
        return '';
    }

    function buildReplacer() {
        var pairs = [];
        maps.forEach(function (row) {
            var from = expandHex(row.from);
            var to = expandHex(row.to);
            if (!from || !to) {
                return;
            }
            var r = parseInt(from.slice(1, 3), 16);
            var g = parseInt(from.slice(3, 5), 16);
            var b = parseInt(from.slice(5, 7), 16);
            pairs.push({
                from: from,
                to: to,
                patterns: [
                    new RegExp(from.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi'),
                    new RegExp('#' + from[1] + from[3] + from[5] + '(?![0-9a-f])', 'gi'),
                    new RegExp('rgba?\\(\\s*' + r + '\\s*,\\s*' + g + '\\s*,\\s*' + b + '(?:\\s*,\\s*[0-9.]+)?\\s*\\)', 'gi'),
                    new RegExp('rgba?\\(\\s*' + r + '\\s+' + g + '\\s+' + b + '(?:\\s*\\/\\s*[0-9.%]+)?\\s*\\)', 'gi'),
                ],
            });
        });
        return pairs;
    }

    var replacers = buildReplacer();

    function replaceColors(cssText) {
        if (!cssText) {
            return cssText;
        }
        var out = cssText;
        replacers.forEach(function (item) {
            item.patterns.forEach(function (re) {
                out = out.replace(re, item.to);
            });
        });
        return out;
    }

    function isDark() {
        return document.documentElement.classList.contains(htmlClass);
    }

    function setStored(mode) {
        try {
            localStorage.setItem(storageKey, mode);
        } catch (e) {}
    }

    function isOurs(node) {
        return node && (node.id === 'darkso-mapped-css' || node.id === 'darkso-var-overrides');
    }

    function rulesToText(sheet) {
        try {
            var rules = sheet.cssRules || sheet.rules;
            if (!rules) {
                return '';
            }
            var out = [];
            for (var i = 0; i < rules.length; i++) {
                out.push(rules[i].cssText);
            }
            return out.join('\n');
        } catch (e) {
            return '';
        }
    }

    function collectCssText() {
        var parts = [];
        document.querySelectorAll('style').forEach(function (node) {
            if (isOurs(node)) {
                return;
            }
            var text = node.textContent || '';
            if (text) {
                parts.push(text);
            }
        });
        Array.prototype.forEach.call(document.styleSheets, function (sheet) {
            var owner = sheet.ownerNode;
            if (owner && (owner.tagName === 'STYLE' || isOurs(owner))) {
                return;
            }
            var text = rulesToText(sheet);
            if (text) {
                parts.push(text);
            }
        });
        return parts.join('\n');
    }

    function ensureMappedTag() {
        if (mappedTag && mappedTag.parentNode) {
            return mappedTag;
        }
        mappedTag = document.getElementById('darkso-mapped-css');
        if (!mappedTag) {
            mappedTag = document.createElement('style');
            mappedTag.id = 'darkso-mapped-css';
            (document.head || document.documentElement).appendChild(mappedTag);
        }
        return mappedTag;
    }

    function buildMappedCss() {
        if (!replacers.length || mappedReady || building) {
            return;
        }
        building = true;
        cachedMapped = replaceColors(collectCssText());
        mappedReady = true;
        building = false;
        if (isDark()) {
            ensureMappedTag().textContent = cachedMapped;
        }
    }

    function scheduleBuild() {
        if (mappedReady || building || !replacers.length) {
            return;
        }
        var run = function () {
            buildMappedCss();
        };
        if (window.requestIdleCallback) {
            window.requestIdleCallback(run, { timeout: 1200 });
        } else {
            setTimeout(run, 1);
        }
    }

    function applyInlineOnce() {
        document.querySelectorAll('[style]').forEach(function (el) {
            if (originalInline.has(el)) {
                return;
            }
            var raw = el.getAttribute('style') || '';
            originalInline.set(el, raw);
            el.setAttribute('style', replaceColors(raw));
        });
    }

    function restoreInline() {
        document.querySelectorAll('[style]').forEach(function (el) {
            if (originalInline.has(el)) {
                el.setAttribute('style', originalInline.get(el));
            }
        });
    }

    function applyCssVars() {
        var styles = getComputedStyle(document.documentElement);
        var overrides = [];
        for (var i = 0; i < styles.length; i++) {
            var name = styles[i];
            if (name.indexOf('--') !== 0) {
                continue;
            }
            var hex = parseToHex(styles.getPropertyValue(name).trim());
            if (!hex) {
                continue;
            }
            for (var j = 0; j < replacers.length; j++) {
                if (replacers[j].from === hex) {
                    overrides.push(name + ':' + replacers[j].to);
                    break;
                }
            }
        }
        if (!varsTag) {
            varsTag = document.getElementById('darkso-var-overrides');
            if (!varsTag) {
                varsTag = document.createElement('style');
                varsTag.id = 'darkso-var-overrides';
                (document.head || document.documentElement).appendChild(varsTag);
            }
        }
        varsTag.textContent = overrides.length
            ? 'html.' + htmlClass + '{ ' + overrides.join(';') + '; }'
            : '';
    }

    function applyDarkStyles() {
        if (!replacers.length) {
            return;
        }
        if (mappedReady) {
            ensureMappedTag().textContent = cachedMapped;
        } else {
            scheduleBuild();
        }
        applyInlineOnce();
        applyCssVars();
    }

    function restoreAll() {
        if (mappedTag) {
            mappedTag.textContent = '';
        }
        if (varsTag) {
            varsTag.textContent = '';
        }
        restoreInline();
    }

    function enableDark() {
        document.documentElement.classList.add(htmlClass);
        if (document.body) {
            document.body.classList.add(htmlClass);
        }
        setStored('dark');
        applyDarkStyles();
    }

    function disableDark() {
        document.documentElement.classList.remove(htmlClass);
        if (document.body) {
            document.body.classList.remove(htmlClass);
        }
        setStored('light');
        restoreAll();
    }

    function withTransition(run) {
        if (document.startViewTransition) {
            document.startViewTransition(run);
            return;
        }
        run();
    }

    function toggleNow() {
        if (isDark()) {
            disableDark();
        } else {
            enableDark();
        }
    }

    function toggle() {
        withTransition(toggleNow);
    }

    function notify(message) {
        if (!message) {
            return;
        }
        var existing = document.getElementById('darkso-notice');
        if (existing) {
            existing.parentNode.removeChild(existing);
        }
        var box = document.createElement('div');
        box.id = 'darkso-notice';
        box.className = 'darkso-notice';
        box.setAttribute('role', 'alert');
        box.textContent = message;
        document.body.appendChild(box);
        requestAnimationFrame(function () {
            box.classList.add('darkso-notice--show');
        });
        setTimeout(function () {
            box.classList.remove('darkso-notice--show');
            setTimeout(function () {
                if (box.parentNode) {
                    box.parentNode.removeChild(box);
                }
            }, 280);
        }, 2600);
    }

    function buttonMessage(btn, fallback) {
        if (btn && btn.getAttribute) {
            var custom = btn.getAttribute('data-darkso-msg');
            if (custom) {
                return custom;
            }
        }
        return fallback;
    }

    function forceLight(btn) {
        if (!isDark()) {
            notify(buttonMessage(btn, (data.messages && data.messages.light_already) || 'شما در حالت روشن هستید'));
            return;
        }
        withTransition(disableDark);
    }

    function forceDark(btn) {
        if (isDark()) {
            notify(buttonMessage(btn, (data.messages && data.messages.dark_already) || 'شما در حالت تاریک هستید'));
            return;
        }
        withTransition(enableDark);
    }

    window.darksoToggle = toggle;
    window.darksoForceLight = forceLight;
    window.darksoForceDark = forceDark;

    function boot() {
        var stored = '';
        try {
            stored = localStorage.getItem(storageKey) || '';
        } catch (e) {}
        if (stored === 'dark' || document.documentElement.classList.contains(htmlClass)) {
            enableDark();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    window.addEventListener('load', function () {
        mappedReady = false;
        cachedMapped = '';
        if (isDark()) {
            scheduleBuild();
            applyCssVars();
        }
    });
})();
