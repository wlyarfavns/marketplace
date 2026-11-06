/**
 * FOOTWORK Storefront SPA Navigation Engine
 * Single Page Application architecture for FOOTWORK Customer-Facing Storefront
 * 
 * Features:
 * - Seamless SPA transitions without full-page reloads across all customer pages
 * - Persistent layout (Top Bar, Main Header, Search, Cart Drawer, WhatsApp, Footer)
 * - Dynamic active nav tab indicator syncing (Beranda, Ready Stock, PO, Semua Koleksi)
 * - Sleek, non-blocking top progress bar (#0A0A0A, 2.5px) matching FOOTWORK aesthetic
 * - Full History API support (Push, Pop, Browser Back, Forward, Direct URLs, Refresh)
 * - Safe Form Submissions (GET search & filters, POST checkout, profile update, proof upload)
 * - Automatic HTMLFormElement.prototype.submit interception for onchange="this.form.submit()"
 * - Automatic script execution & Alpine.js tree re-initialization (product detail & checkout)
 * - Slide-over cart drawer synchronization & auto-close on navigation
 * - Complete isolation from Admin SPA (routes to /admin/* are excluded and handled natively)
 */

(function () {
    'use strict';

    // Prevent multiple initializations
    if (window.FrontSPA) {
        return;
    }

    const FrontSPA = {
        isNavigating: false,
        abortController: null,
        progressTimer: null,
        progressBar: null,
        progressContainer: null,

        /**
         * Initialize the Storefront SPA Engine
         */
        init() {
            this.setupProgressBar();
            this.setupFormSubmitOverride();
            this.bindEvents();
            this.syncInitialState();
            console.log('[FrontSPA] Engine initialized successfully.');
        },

        /**
         * Create and inject the sleek top progress bar if not present
         */
        setupProgressBar() {
            let container = document.getElementById('front-spa-progress');
            if (!container) {
                container = document.createElement('div');
                container.id = 'front-spa-progress';
                container.style.cssText = 'position:fixed;top:0;left:0;right:0;height:2.5px;z-index:99999;pointer-events-none;opacity:0;transition:opacity 200ms ease;';
                
                const bar = document.createElement('div');
                bar.id = 'front-spa-progress-bar';
                bar.style.cssText = 'height:100%;width:0%;background-color:#0A0A0A;transition:width 300ms cubic-bezier(0.4, 0, 0.2, 1);box-shadow:0 0 10px rgba(10,10,10,0.5);';
                
                container.appendChild(bar);
                document.body.appendChild(container);
            }
            this.progressContainer = container;
            this.progressBar = container.querySelector('#front-spa-progress-bar') || container.firstElementChild;
        },

        startProgress() {
            if (!this.progressBar || !this.progressContainer) return;
            clearInterval(this.progressTimer);
            this.progressContainer.style.opacity = '1';
            this.progressBar.style.transition = 'width 300ms cubic-bezier(0.4, 0, 0.2, 1)';
            this.progressBar.style.width = '25%';

            let current = 25;
            this.progressTimer = setInterval(() => {
                if (current < 85) {
                    current += Math.random() * 15;
                    this.progressBar.style.width = Math.min(current, 85) + '%';
                }
            }, 250);
        },

        finishProgress() {
            if (!this.progressBar || !this.progressContainer) return;
            clearInterval(this.progressTimer);
            this.progressBar.style.width = '100%';

            setTimeout(() => {
                this.progressContainer.style.opacity = '0';
                setTimeout(() => {
                    this.progressBar.style.transition = 'none';
                    this.progressBar.style.width = '0%';
                }, 200);
            }, 180);
        },

        /**
         * Safely override HTMLFormElement.prototype.submit so onchange="this.form.submit()"
         * goes through FrontSPA without triggering a full page reload.
         */
        setupFormSubmitOverride() {
            const self = this;
            const originalSubmit = HTMLFormElement.prototype.submit;
            this._originalSubmit = originalSubmit;

            HTMLFormElement.prototype.submit = function () {
                const actionUrl = new URL(this.action || window.location.href, window.location.origin);
                if (!self.isExcluded(actionUrl, this)) {
                    self.submitForm(this);
                } else {
                    originalSubmit.call(this);
                }
            };
        },

        /**
         * Check if a link, form, or URL should be excluded from Storefront SPA navigation
         */
        isExcluded(url, targetElement) {
            if (!url) return true;

            // Element attribute checks
            if (targetElement) {
                if (targetElement.hasAttribute('download')) return true;
                if (targetElement.getAttribute('target') === '_blank') return true;
                if (targetElement.hasAttribute('data-no-spa') || targetElement.closest('[data-no-spa]')) return true;
            }

            // Origin check: must be internal
            if (url.origin !== window.location.origin) return true;

            const pathname = url.pathname.toLowerCase();

            // Admin area routes are handled natively or by AdminSPA
            if (pathname.startsWith('/admin')) return true;

            // Safe auth logout: logout should reset all session state
            if (pathname === '/logout' || pathname.includes('/logout')) return true;

            // File downloads & raw assets
            if (
                pathname.includes('/storage/') ||
                pathname.includes('/images/') ||
                pathname.includes('export-excel') ||
                pathname.includes('invoice-pdf') ||
                pathname.includes('/export') ||
                pathname.endsWith('.pdf') ||
                pathname.endsWith('.xlsx') ||
                pathname.endsWith('.csv') ||
                pathname.endsWith('.zip')
            ) {
                return true;
            }

            // In-page hash anchors
            if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) {
                return true;
            }

            return false;
        },

        /**
         * Update header navigation active tab indicator
         */
        updateActiveNav(urlString) {
            try {
                const url = new URL(urlString, window.location.origin);
                const pathname = url.pathname.toLowerCase();
                const typeParam = url.searchParams.get('type');

                let activeRoute = null;

                if (pathname === '/' || pathname === '') {
                    activeRoute = 'home';
                } else if (pathname === '/katalog') {
                    if (typeParam === 'ready_stock') {
                        activeRoute = 'ready_stock';
                    } else if (typeParam === 'po') {
                        activeRoute = 'po';
                    } else {
                        activeRoute = 'catalog';
                    }
                }

                const navLinks = document.querySelectorAll('#front-nav-links a[data-front-route]');
                navLinks.forEach((link) => {
                    const routeId = link.getAttribute('data-front-route');
                    const isActive = routeId === activeRoute;

                    if (isActive) {
                        link.classList.add('text-black', 'border-b-2', 'border-neutral-950', 'pb-1', 'font-bold');
                        link.classList.remove('text-gray-600');
                    } else {
                        link.classList.remove('text-black', 'border-b-2', 'border-neutral-950', 'pb-1', 'font-bold');
                        link.classList.add('text-gray-600');
                    }
                });

                // Sync header search input value if on /katalog
                const headerSearchInput = document.getElementById('front-header-search-input');
                if (headerSearchInput) {
                    if (pathname === '/katalog') {
                        headerSearchInput.value = url.searchParams.get('q') || '';
                    } else {
                        // Keep or leave clean
                    }
                }
            } catch (e) {
                console.warn('[FrontSPA] Error updating active nav:', e);
            }
        },

        /**
         * Close cart slide-over drawer if currently open
         */
        closeDrawerIfOpen() {
            try {
                if (window.Alpine) {
                    const bodyEl = document.querySelector('body');
                    if (bodyEl) {
                        const drawer = window.Alpine.$data(bodyEl);
                        if (drawer && typeof drawer.closeDrawer === 'function' && drawer.isOpen) {
                            drawer.closeDrawer();
                        }
                    }
                }
            } catch (e) {
                console.warn('[FrontSPA] Drawer close warning:', e);
            }
        },

        /**
         * Execute inline scripts and reinitialize Alpine tree on newly swapped content
         */
        executeScriptsAndInitAlpine(container) {
            if (!container) return;

            // 1. Extract and re-run all <script> tags inside container in global scope
            const scripts = container.querySelectorAll('script');
            scripts.forEach((oldScript) => {
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach((attr) => {
                    newScript.setAttribute(attr.name, attr.value);
                });
                newScript.textContent = oldScript.textContent;
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });

            // 2. Re-register any newly defined Alpine component objects if needed
            if (window.Alpine) {
                try {
                    if (typeof window.productDetailState === 'function' && window.Alpine.data) {
                        try {
                            window.Alpine.data('productDetailState', window.productDetailState);
                        } catch (e) {
                            // Ignored if already registered
                        }
                    }
                    if (typeof window.checkoutState === 'function' && window.Alpine.data) {
                        try {
                            window.Alpine.data('checkoutState', window.checkoutState);
                        } catch (e) {
                            // Ignored if already registered
                        }
                    }

                    // Re-initialize Alpine tree on the new content container
                    window.Alpine.initTree(container);
                } catch (err) {
                    console.warn('[FrontSPA] Alpine tree re-init warning:', err);
                }
            }
        },

        /**
         * Apply newly fetched HTML content to the DOM
         */
        applyContent(htmlString, targetUrl, options = {}) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(htmlString, 'text/html');

            const newMain = doc.querySelector('#front-main-content');
            const currentMain = document.querySelector('#front-main-content');

            if (!newMain || !currentMain) {
                console.warn('[FrontSPA] Missing #front-main-content container. Falling back to native navigation.');
                window.location.href = targetUrl;
                return false;
            }

            // 1. Update document title
            const newTitle = doc.querySelector('title')?.textContent;
            if (newTitle) {
                document.title = newTitle;
            }

            // 2. Update flash alert container
            const newFlash = doc.querySelector('#front-flash-container');
            const currentFlash = document.querySelector('#front-flash-container');
            if (currentFlash) {
                currentFlash.innerHTML = newFlash ? newFlash.innerHTML : '';
            }

            // 3. Update top bar auth section if present
            const newUserBar = doc.querySelector('#front-user-bar');
            const currentUserBar = document.querySelector('#front-user-bar');
            if (newUserBar && currentUserBar) {
                currentUserBar.innerHTML = newUserBar.innerHTML;
            }

            // 4. Update main body content
            currentMain.innerHTML = newMain.innerHTML;

            // 5. Update browser history if requested
            if (options.pushHistory !== false) {
                history.pushState({ url: targetUrl }, '', targetUrl);
            }

            // 6. Update active nav indicator
            this.updateActiveNav(targetUrl);

            // 7. Auto close slide-over cart drawer on page transitions
            this.closeDrawerIfOpen();

            // 8. Execute scripts and init Alpine tree
            this.executeScriptsAndInitAlpine(currentMain);

            // 9. Scroll smoothly to top
            window.scrollTo({ top: 0, behavior: 'instant' });

            // 10. Dispatch custom lifecycle event
            window.dispatchEvent(new CustomEvent('front:spa-navigated', {
                detail: { url: targetUrl }
            }));

            return true;
        },

        /**
         * Perform SPA navigation to target URL
         */
        async navigate(targetUrlString, options = {}) {
            const targetUrl = new URL(targetUrlString, window.location.origin);

            // Check if excluded from SPA
            if (this.isExcluded(targetUrl)) {
                window.location.href = targetUrl.href;
                return;
            }

            // Prevent duplicate navigation to exact same URL if not forced
            if (!options.force && targetUrl.href === window.location.href) {
                return;
            }

            // Abort ongoing navigation
            if (this.abortController) {
                this.abortController.abort();
            }
            this.abortController = new AbortController();

            this.isNavigating = true;
            this.startProgress();

            try {
                const response = await fetch(targetUrl.href, {
                    method: 'GET',
                    signal: this.abortController.signal,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-SPA-Request': 'true',
                        'Accept': 'text/html, application/xhtml+xml, */*'
                    }
                });

                // Check for auth redirect
                if (response.redirected && (response.url.includes('/login') || response.url.includes('/admin/login'))) {
                    window.location.href = response.url;
                    return;
                }

                if (response.status === 401 || response.status === 419) {
                    window.location.href = '/login';
                    return;
                }

                const finalUrl = response.url || targetUrl.href;

                // If redirected to admin area or external, navigate natively
                if (new URL(finalUrl, window.location.origin).origin !== window.location.origin ||
                    new URL(finalUrl, window.location.origin).pathname.startsWith('/admin')) {
                    window.location.href = finalUrl;
                    return;
                }

                const html = await response.text();
                this.applyContent(html, finalUrl, options);
            } catch (err) {
                if (err.name === 'AbortError') {
                    // New navigation took precedence
                    return;
                }
                console.error('[FrontSPA] Navigation error:', err);
                // Fallback to native navigation on failure
                window.location.href = targetUrl.href;
            } finally {
                this.finishProgress();
                this.isNavigating = false;
            }
        },

        /**
         * Submit form asynchronously via SPA
         */
        async submitForm(form, submitterButton) {
            const formAction = form.getAttribute('action') || window.location.href;
            const actionUrl = new URL(formAction, window.location.origin);

            if (this.isExcluded(actionUrl, form)) {
                if (this._originalSubmit) {
                    this._originalSubmit.call(form);
                } else {
                    form.submit();
                }
                return;
            }

            const method = (form.getAttribute('method') || 'POST').toUpperCase();

            // GET form: convert to query params and navigate
            if (method === 'GET') {
                const formData = new FormData(form);
                const params = new URLSearchParams();
                for (const [key, value] of formData.entries()) {
                    if (value !== '') {
                        params.append(key, value);
                    }
                }
                const queryString = params.toString();
                const targetUrl = actionUrl.pathname + (queryString ? '?' + queryString : '');
                this.navigate(targetUrl);
                return;
            }

            // POST / PUT / DELETE form
            if (this.abortController) {
                this.abortController.abort();
            }
            this.abortController = new AbortController();

            this.isNavigating = true;
            this.startProgress();

            const formData = new FormData(form);
            if (submitterButton && submitterButton.name) {
                formData.append(submitterButton.name, submitterButton.value || '');
            }

            try {
                const response = await fetch(actionUrl.href, {
                    method: 'POST',
                    body: formData,
                    signal: this.abortController.signal,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-SPA-Request': 'true',
                        'Accept': 'text/html, application/xhtml+xml, application/json, */*'
                    }
                });

                // Check for auth redirect
                if (response.redirected && (response.url.includes('/login') || response.url.includes('/admin/login'))) {
                    window.location.href = response.url;
                    return;
                }

                if (response.status === 401 || response.status === 419) {
                    window.location.href = '/login';
                    return;
                }

                const contentType = response.headers.get('content-type') || '';

                // Handle JSON response (e.g. cart updates)
                if (contentType.includes('application/json')) {
                    const jsonData = await response.json();
                    if (jsonData.cart_count !== undefined) {
                        window.dispatchEvent(new CustomEvent('front:cart-sync', { detail: jsonData }));
                    }
                    // Refresh current page view seamlessly
                    this.navigate(window.location.href, { force: true, pushHistory: false });
                    return;
                }

                const finalUrl = response.url || window.location.href;

                // If redirected to admin area or external
                if (new URL(finalUrl, window.location.origin).origin !== window.location.origin ||
                    new URL(finalUrl, window.location.origin).pathname.startsWith('/admin')) {
                    window.location.href = finalUrl;
                    return;
                }

                const html = await response.text();
                this.applyContent(html, finalUrl, { pushHistory: true });
            } catch (err) {
                if (err.name === 'AbortError') return;
                console.error('[FrontSPA] Form submission error:', err);
                if (this._originalSubmit) {
                    this._originalSubmit.call(form);
                } else {
                    form.submit();
                }
            } finally {
                this.finishProgress();
                this.isNavigating = false;
            }
        },

        /**
         * Bind document delegated click, submit, and history events
         */
        bindEvents() {
            // Click listener for links
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (!link || !link.href) return;

                // Ignore modifier keys
                if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.button !== 0) {
                    return;
                }

                // Ignore pure hash anchors on same page
                const hrefAttr = link.getAttribute('href');
                if (hrefAttr && hrefAttr.startsWith('#')) {
                    return;
                }

                const url = new URL(link.href, window.location.origin);

                if (this.isExcluded(url, link)) {
                    return;
                }

                e.preventDefault();
                this.navigate(url.href);
            });

            // Submit listener for forms
            document.addEventListener('submit', (e) => {
                const form = e.target;
                if (!form || form.tagName !== 'FORM') return;

                // If default was already prevented by an inline onsubmit (e.g. confirmation cancel)
                if (e.defaultPrevented) return;

                const actionUrl = new URL(form.action || window.location.href, window.location.origin);
                if (this.isExcluded(actionUrl, form)) {
                    return;
                }

                e.preventDefault();
                this.submitForm(form, e.submitter);
            });

            // History popstate listener (Browser Back & Forward)
            window.addEventListener('popstate', () => {
                this.navigate(window.location.href, { pushHistory: false });
            });
        },

        /**
         * Sync initial state on first page load
         */
        syncInitialState() {
            this.updateActiveNav(window.location.href);
            if (!history.state || !history.state.url) {
                history.replaceState({ url: window.location.href }, '', window.location.href);
            }
        }
    };

    // Auto-initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => FrontSPA.init());
    } else {
        FrontSPA.init();
    }

    // Expose to window for external scripting or debugging
    window.FrontSPA = FrontSPA;
})();

