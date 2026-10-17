/**
 * Darkso 5.1 — widgets.js
 * کروسل، فیلتر پورتفولیو + لایت‌باکس و فرم AJAX
 */
(function () {
    'use strict';

    var data = window.darksoData || {};

    function qsa(root, selector) {
        return Array.prototype.slice.call(root.querySelectorAll(selector));
    }

    function isRtl(el) {
        return window.getComputedStyle(el).direction === 'rtl';
    }

    /* ------------------------------------------------------------------ *
     * Carousel
     * ------------------------------------------------------------------ */
    function initCarousel(root) {
        var view = root.querySelector('.darkso-carousel__viewport');
        var track = root.querySelector('.darkso-carousel__track');
        if (!view || !track) {
            return;
        }

        var slides = qsa(track, '.darkso-carousel__slide');
        var prev = root.querySelector('.darkso-carousel__prev');
        var next = root.querySelector('.darkso-carousel__next');
        var dotsBox = root.querySelector('.darkso-carousel__dots');
        var autoplay = root.getAttribute('data-autoplay') === '1';
        var delay = parseInt(root.getAttribute('data-delay'), 10) || 4000;
        var pauseOnHover = root.getAttribute('data-pause') === '1';
        var hovered = false;
        var ticking = false;
        var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        function step() {
            if (!slides.length) {
                return view.clientWidth || 1;
            }
            var gap = parseFloat(window.getComputedStyle(track).columnGap) || 0;
            return slides[0].getBoundingClientRect().width + gap;
        }

        function perView() {
            var gap = parseFloat(window.getComputedStyle(track).columnGap) || 0;
            return Math.max(1, Math.round((view.clientWidth + gap) / step()));
        }

        function pages() {
            return Math.max(1, slides.length - perView() + 1);
        }

        function index() {
            return Math.round(Math.abs(view.scrollLeft) / step());
        }

        function go(i) {
            var max = pages() - 1;
            i = Math.max(0, Math.min(max, i));
            var sign = isRtl(view) ? -1 : 1;
            view.scrollTo({ left: sign * i * step(), behavior: reduced ? 'auto' : 'smooth' });
        }

        function buildDots() {
            if (!dotsBox) {
                return;
            }
            dotsBox.innerHTML = '';
            var n = pages();
            if (n <= 1) {
                dotsBox.style.display = 'none';
                return;
            }
            dotsBox.style.display = '';
            for (var i = 0; i < n; i++) {
                (function (n_i) {
                    var dot = document.createElement('button');
                    dot.type = 'button';
                    dot.className = 'darkso-carousel__dot';
                    dot.setAttribute('aria-label', String(n_i + 1));
                    dot.addEventListener('click', function () {
                        go(n_i);
                    });
                    dotsBox.appendChild(dot);
                })(i);
            }
            sync();
        }

        function sync() {
            if (!dotsBox) {
                return;
            }
            var current = Math.min(index(), pages() - 1);
            qsa(dotsBox, '.darkso-carousel__dot').forEach(function (dot, i) {
                dot.classList.toggle('is-active', i === current);
            });
        }

        function nextSlide() {
            var i = index();
            go(i >= pages() - 1 ? 0 : i + 1);
        }

        function prevSlide() {
            var i = index();
            go(i <= 0 ? pages() - 1 : i - 1);
        }

        if (next) {
            next.addEventListener('click', nextSlide);
        }
        if (prev) {
            prev.addEventListener('click', prevSlide);
        }

        view.addEventListener('scroll', function () {
            if (ticking) {
                return;
            }
            ticking = true;
            window.requestAnimationFrame(function () {
                ticking = false;
                sync();
            });
        }, { passive: true });

        view.addEventListener('keydown', function (e) {
            if (e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') {
                return;
            }
            var forward = (e.key === 'ArrowRight') !== isRtl(view);
            e.preventDefault();
            if (forward) {
                nextSlide();
            } else {
                prevSlide();
            }
        });

        window.addEventListener('resize', buildDots);

        if (pauseOnHover) {
            root.addEventListener('mouseenter', function () { hovered = true; });
            root.addEventListener('mouseleave', function () { hovered = false; });
            root.addEventListener('focusin', function () { hovered = true; });
            root.addEventListener('focusout', function () { hovered = false; });
        }

        if (autoplay && !reduced) {
            var timer = window.setInterval(function () {
                if (!document.body.contains(root)) {
                    window.clearInterval(timer);
                    return;
                }
                if (hovered || document.hidden) {
                    return;
                }
                nextSlide();
            }, delay);
        }

        buildDots();
    }

    /* ------------------------------------------------------------------ *
     * Lightbox
     * ------------------------------------------------------------------ */
    function openLightbox(src, title) {
        if (!src) {
            return;
        }
        var box = document.createElement('div');
        box.className = 'darkso-lightbox';
        box.setAttribute('role', 'dialog');
        box.setAttribute('aria-modal', 'true');

        var img = document.createElement('img');
        img.className = 'darkso-lightbox__img';
        img.src = src;
        img.alt = title || '';

        var close = document.createElement('button');
        close.type = 'button';
        close.className = 'darkso-lightbox__close';
        close.setAttribute('aria-label', 'close');
        close.innerHTML = '&times;';

        box.appendChild(close);
        box.appendChild(img);

        if (title) {
            var cap = document.createElement('p');
            cap.className = 'darkso-lightbox__caption';
            cap.textContent = title;
            box.appendChild(cap);
        }

        function remove() {
            document.removeEventListener('keydown', onKey);
            box.classList.remove('is-open');
            setTimeout(function () {
                if (box.parentNode) {
                    box.parentNode.removeChild(box);
                }
            }, 260);
        }

        function onKey(e) {
            if (e.key === 'Escape') {
                remove();
            }
        }

        box.addEventListener('click', function (e) {
            if (e.target !== img) {
                remove();
            }
        });
        document.addEventListener('keydown', onKey);
        document.body.appendChild(box);
        requestAnimationFrame(function () {
            box.classList.add('is-open');
            close.focus();
        });
    }

    /* ------------------------------------------------------------------ *
     * Portfolio
     * ------------------------------------------------------------------ */
    function initPortfolio(root) {
        var buttons = qsa(root, '.darkso-portfolio__filter');
        var items = qsa(root, '.darkso-portfolio__item');

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var filter = btn.getAttribute('data-filter') || '*';
                buttons.forEach(function (b) {
                    b.classList.toggle('is-active', b === btn);
                });
                items.forEach(function (item) {
                    var cats = (item.getAttribute('data-cats') || '').split(' ');
                    var show = filter === '*' || cats.indexOf(filter) !== -1;
                    item.hidden = !show;
                    if (show) {
                        item.style.animation = 'none';
                        void item.offsetHeight;
                        item.style.animation = '';
                    }
                });
            });
        });

        root.addEventListener('click', function (e) {
            var link = e.target.closest ? e.target.closest('[data-darkso-lightbox]') : null;
            if (!link) {
                return;
            }
            e.preventDefault();
            openLightbox(link.getAttribute('data-full'), link.getAttribute('data-title'));
        });
    }

    /* ------------------------------------------------------------------ *
     * Form
     * ------------------------------------------------------------------ */
    function initForm(form) {
        var status = form.querySelector('.darkso-form__status');
        var ajaxUrl = data.ajaxUrl || '/wp-admin/admin-ajax.php';

        function setStatus(type, message) {
            if (!status) {
                return;
            }
            status.className = 'darkso-form__status' + (type ? ' is-' + type : '');
            status.textContent = message || '';
        }

        function getNonce() {
            return fetch(ajaxUrl + '?action=darkso_form_nonce', {
                credentials: 'same-origin',
                cache: 'no-store',
            })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res && res.success && res.data && res.data.nonce) {
                        return res.data.nonce;
                    }
                    throw new Error('nonce');
                });
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (form.classList.contains('is-loading')) {
                return;
            }
            if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                if (typeof form.reportValidity === 'function') {
                    form.reportValidity();
                }
                return;
            }

            form.classList.add('is-loading');
            setStatus('', '');

            getNonce()
                .then(function (nonce) {
                    var fd = new FormData(form);
                    fd.append('action', 'darkso_form_submit');
                    fd.append('nonce', nonce);
                    return fetch(ajaxUrl, {
                        method: 'POST',
                        body: fd,
                        credentials: 'same-origin',
                    });
                })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res && res.success) {
                        setStatus('success', (res.data && res.data.message) || 'پیام شما ارسال شد.');
                        form.reset();
                    } else {
                        setStatus('error', (res && res.data && res.data.message) || 'ارسال انجام نشد. لطفاً دوباره تلاش کنید.');
                    }
                })
                .catch(function () {
                    setStatus('error', 'خطا در ارتباط با سرور. لطفاً دوباره تلاش کنید.');
                })
                .then(function () {
                    form.classList.remove('is-loading');
                });
        });
    }

    /* ------------------------------------------------------------------ *
     * Boot
     * ------------------------------------------------------------------ */
    function initAll(scope) {
        scope = scope && scope.querySelectorAll ? scope : document;

        var targets = [];
        if (scope.matches) {
            ['[data-darkso-carousel]', '[data-darkso-portfolio]', '[data-darkso-form]'].forEach(function (sel) {
                if (scope.matches(sel)) {
                    targets.push(scope);
                }
            });
        }

        qsa(scope, '[data-darkso-carousel]').forEach(function (el) {
            if (!el.__darksoReady) {
                el.__darksoReady = true;
                initCarousel(el);
            }
        });

        qsa(scope, '[data-darkso-portfolio]').forEach(function (el) {
            if (!el.__darksoReady) {
                el.__darksoReady = true;
                initPortfolio(el);
            }
        });

        qsa(scope, '[data-darkso-form]').forEach(function (el) {
            if (!el.__darksoReady) {
                el.__darksoReady = true;
                initForm(el);
            }
        });
    }

    function hookElementor() {
        if (!window.elementorFrontend || !window.elementorFrontend.hooks) {
            return;
        }
        window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($el) {
            var node = $el && $el[0] ? $el[0] : $el;
            initAll(node);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { initAll(document); });
    } else {
        initAll(document);
    }

    if (window.jQuery) {
        window.jQuery(window).on('elementor/frontend/init', hookElementor);
    }
    hookElementor();
})();
