/**
 * Matrimonial Hub — progressive enhancement.
 *
 * Everything here is an upgrade to a page that already works without it: forms
 * post normally, links navigate, and content is visible. Nothing renders only
 * because JavaScript ran.
 */
(function () {
    'use strict';

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* -- Scroll reveal --------------------------------------------------- */
    /* The original bound its reveal to the scroll event only and never fired
       it on load, so on a short viewport the testimonials stayed invisible
       forever. An observer reports what is on screen the moment it starts. */
    function initReveal() {
        var targets = document.querySelectorAll('[data-reveal]');
        if (!targets.length) return;

        if (reduceMotion || !('IntersectionObserver' in window)) {
            targets.forEach(function (el) { el.classList.add('is-visible'); });
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

        targets.forEach(function (el) {
            var delay = el.style.getPropertyValue('--reveal-delay');
            if (delay) el.style.setProperty('--reveal-delay', delay);
            observer.observe(el);
        });
    }

    /* -- Header shadow on scroll ---------------------------------------- */
    function initHeader() {
        var header = document.querySelector('[data-header]');
        if (!header) return;

        var update = function () {
            header.classList.toggle('is-stuck', window.scrollY > 8);
        };

        update();
        window.addEventListener('scroll', update, { passive: true });
    }

    /* -- Mobile nav ------------------------------------------------------ */
    function initNav() {
        var toggle = document.querySelector('[data-nav-toggle]');
        var nav = document.querySelector('[data-nav]');
        if (!toggle || !nav) return;

        toggle.addEventListener('click', function () {
            var open = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', String(open));
        });

        // Close on Escape, and on any navigation within the menu.
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && nav.classList.contains('is-open')) {
                nav.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.focus();
            }
        });

        nav.addEventListener('click', function (e) {
            if (e.target.closest('a')) {
                nav.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /* -- Theme toggle ---------------------------------------------------- */
    function initTheme() {
        var buttons = document.querySelectorAll('[data-theme-toggle]');
        if (!buttons.length) return;

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                var root = document.documentElement;
                var systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                var current = root.dataset.theme || (systemDark ? 'dark' : 'light');
                var next = current === 'dark' ? 'light' : 'dark';

                root.dataset.theme = next;
                try { localStorage.setItem('theme', next); } catch (e) { /* private mode */ }
            });
        });
    }

    /* -- Flash messages -------------------------------------------------- */
    function initFlash() {
        document.querySelectorAll('[data-flash]').forEach(function (flash) {
            var dismiss = function () {
                flash.classList.add('is-leaving');
                setTimeout(function () { flash.remove(); }, 260);
            };

            var close = flash.querySelector('[data-flash-close]');
            if (close) close.addEventListener('click', dismiss);

            setTimeout(dismiss, 6000);
        });
    }

    /* -- Count-up numbers ------------------------------------------------ */
    function initCounters() {
        var counters = document.querySelectorAll('[data-count-to]');
        if (!counters.length) return;

        counters.forEach(function (el) {
            var target = parseInt(el.dataset.countTo, 10) || 0;

            if (reduceMotion || target === 0) {
                el.textContent = String(target);
                return;
            }

            var start = null;
            var duration = 900;

            var step = function (timestamp) {
                if (start === null) start = timestamp;
                var progress = Math.min((timestamp - start) / duration, 1);
                // Ease-out so it settles rather than stopping dead.
                var eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = String(Math.round(target * eased));
                if (progress < 1) requestAnimationFrame(step);
            };

            requestAnimationFrame(step);
        });
    }

    /* -- Progress bars --------------------------------------------------- */
    function initProgress() {
        document.querySelectorAll('[data-progress]').forEach(function (bar) {
            var value = Math.max(0, Math.min(100, parseInt(bar.dataset.progress, 10) || 0));
            // Next frame, so the transition has a 0 to animate from.
            requestAnimationFrame(function () { bar.style.width = value + '%'; });
        });
    }

    /* -- Tabs ------------------------------------------------------------ */
    function initTabs() {
        var tabs = document.querySelectorAll('[data-tab]');
        if (!tabs.length) return;

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                var name = tab.dataset.tab;

                tabs.forEach(function (t) {
                    t.setAttribute('aria-selected', String(t === tab));
                });

                document.querySelectorAll('[data-tab-panel]').forEach(function (panel) {
                    panel.hidden = panel.dataset.tabPanel !== name;
                });
            });
        });
    }

    /* -- Photo preview --------------------------------------------------- */
    function initPhotoPreview() {
        document.querySelectorAll('[data-photo-input]').forEach(function (input) {
            input.addEventListener('change', function () {
                var file = input.files && input.files[0];
                if (!file) return;

                var url = URL.createObjectURL(file);
                var preview = document.querySelector('[data-photo-preview]');
                var target = document.querySelector('[data-photo-preview-target]');

                if (preview) {
                    preview.src = url;
                    preview.hidden = false;
                }
                if (target) target.src = url;
            });
        });
    }

    /* -- Chat ------------------------------------------------------------ */
    /* The original re-fetched and re-rendered the entire transcript every
       500 ms — two requests a second per user, and it wiped any text you had
       selected. This asks only for messages after the last id it has, appends
       them, and backs off while the tab is hidden. */
    function initChat() {
        var root = document.querySelector('[data-chat]');
        if (!root) return;

        var log = root.querySelector('[data-chat-log]');
        var form = root.querySelector('[data-chat-form]');
        var input = root.querySelector('[data-chat-input]');
        var send = root.querySelector('[data-chat-send]');
        var presence = root.querySelector('[data-presence]');
        var presenceLabel = root.querySelector('[data-presence-label]');
        var token = form.querySelector('input[name="_csrf_token"]').value;

        var lastId = parseInt(root.dataset.lastId, 10) || 0;
        var polling = null;
        var sending = false;

        var scrollToEnd = function () { log.scrollTop = log.scrollHeight; };
        scrollToEnd();

        var atBottom = function () {
            return log.scrollHeight - log.scrollTop - log.clientHeight < 120;
        };

        var appendMessage = function (message) {
            var empty = log.querySelector('.empty');
            if (empty) empty.remove();

            var bubble = document.createElement('div');
            bubble.className = 'bubble ' + (message.mine ? 'bubble-out' : 'bubble-in');
            // textContent, not innerHTML — a message is never markup.
            bubble.textContent = message.body;

            var time = document.createElement('span');
            time.className = 'bubble-time';
            time.textContent = message.time;
            bubble.appendChild(time);

            log.appendChild(bubble);
        };

        var poll = function () {
            fetch(root.dataset.fetchUrl + '?since=' + lastId, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (data) {
                    if (!data) return;

                    var stick = atBottom();

                    (data.messages || []).forEach(function (message) {
                        if (message.id > lastId) {
                            lastId = message.id;
                            appendMessage(message);
                        }
                    });

                    if (stick) scrollToEnd();

                    if (presence) presence.classList.toggle('presence-on', !!data.peer_online);
                    if (presenceLabel && data.peer_online) presenceLabel.textContent = 'Online now';
                })
                .catch(function () { /* offline — the next tick tries again */ });
        };

        var startPolling = function () {
            if (polling) return;
            polling = setInterval(poll, 4000);
        };

        var stopPolling = function () {
            clearInterval(polling);
            polling = null;
        };

        // Don't poll a tab nobody is looking at.
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                stopPolling();
            } else {
                poll();
                startPolling();
            }
        });

        startPolling();

        // Grow the textarea with its content, up to the CSS max-height.
        var autosize = function () {
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 128) + 'px';
        };

        input.addEventListener('input', function () {
            autosize();
            send.disabled = input.value.trim() === '';
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.requestSubmit();
            }
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var body = input.value.trim();
            if (body === '' || sending) return;

            sending = true;
            send.disabled = true;

            var payload = new FormData();
            payload.append('body', body);
            payload.append('_csrf_token', token);

            fetch(root.dataset.sendUrl, {
                method: 'POST',
                body: payload,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
                .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
                .then(function (result) {
                    if (!result.ok) throw new Error(result.data.error || 'Could not send');

                    appendMessage(result.data.message);
                    lastId = Math.max(lastId, result.data.message.id);
                    input.value = '';
                    autosize();
                    scrollToEnd();
                })
                .catch(function (error) {
                    // Put the text back rather than losing what they typed.
                    input.value = body;
                    window.alert(error.message);
                })
                .finally(function () {
                    sending = false;
                    send.disabled = input.value.trim() === '';
                    input.focus();
                });
        });
    }

    /* -- Confirm destructive actions ------------------------------------- */
    function initConfirm() {
        document.querySelectorAll('[data-confirm]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (!window.confirm(form.dataset.confirm)) e.preventDefault();
            });
        });
    }

    function init() {
        initReveal();
        initHeader();
        initNav();
        initTheme();
        initFlash();
        initCounters();
        initProgress();
        initTabs();
        initPhotoPreview();
        initChat();
        initConfirm();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
