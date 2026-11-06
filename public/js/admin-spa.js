/**
 * FOOTWORK Admin SPA Navigation Engine
 * Single Page Application architecture for Laravel Admin Back-Office
 * 
 * Features:
 * - Seamless SPA transitions without full-page reloads
 * - Persistent layout (Sidebar, Navbar, User Header remain untouched)
 * - Dynamic sidebar active state indicator syncing
 * - Progress bar feedback (proportional, sleek, non-blocking)
 * - Full History API support (Push, Pop, Back, Forward, Direct URLs)
 * - Safe Form Submissions (GET filters, POST/PUT/DELETE CRUD with files)
 * - Automatic script execution & Alpine.js tree re-initialization
 * - Safe exclusion of file downloads (Excel, PDF), auth, and external links
 */

(function () {
    'use strict';

    // Prevent multiple initializations
    if (window.AdminSPA) {
        return;
    }

    const AdminSPA = {
        isNavigating: false,
        abortController: null,
        progressTimer: null,
        progressBar: null,
        progressContainer: null,

        /**
         * Initialize the SPA Engine
         */
        init() {
            this.setupProgressBar();
            this.bindEvents();
            this.syncInitialState();
            console.log('[AdminSPA] Engine initialized successfully.');
        },

        /**
         * Create and inject the sleek top progress bar
         */
        setupProgressBar() {
            let container = document.getElementById('admin-spa-progress');
            if (!container) {
                container = document.createElement('div');
                container.id = 'admin-spa-progress';
                container.style.cssText = 'position:fixed;top:0;left:0;right:0;height:2.5px;z-index:99999;pointer-events-none;opacity:0;transition:opacity 200ms ease;';
                
                const bar = document.createElement('div');
                bar.id = 'admin-spa-progress-bar';
                bar.style.cssText = 'height:100%;width:0%;background-color:#0f172a;transition:width 300ms cubic-bezier(0.4, 0, 0.2, 1);box-shadow:0 0 10px rgba(15,23,42,0.5);';
                
                container.appendChild(bar);
                document.body.appendChild(container);
            }
            this.progressContainer = container;
            this.progressBar = container.querySelector('#admin-spa-progress-bar') || container.firstElementChild;
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
         * Check if a link or URL should be excluded from SPA navigation
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

            // Must belong to admin area
            if (!url.pathname.startsWith('/admin')) return true;

            const pathname = url.pathname.toLowerCase();

            // Auth routes
            if (pathname === '/admin/login' || pathname.includes('/logout')) return true;

            // File download endpoints
            if (
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

            return false;
        },

        /**
         * Update sidebar navigation active highlight
         */
        updateSidebarActiveLink(urlString) {
            try {
                const url = new URL(urlString, window.location.origin);
                const pathname = url.pathname.toLowerCase();

                let activeRoute = 'dashboard';
                if (pathname.includes('/admin/sellers/report')) {
                    activeRoute = 'sellers.report';
                } else if (pathname.includes('/admin/sellers')) {
                    activeRoute = 'sellers';
                } else if (pathname.includes('/admin/orders')) {
                    activeRoute = 'orders';
                } else if (pathname.includes('/admin/products')) {
                    activeRoute = 'products';
                } else if (pathname.includes('/admin/brands')) {
                    activeRoute = 'brands';
                } else if (pathname.includes('/admin/categories')) {
                    activeRoute = 'categories';
                } else if (pathname.includes('/admin/users')) {
                    activeRoute = 'users';
                } else if (pathname.includes('/admin/coupons')) {
                    activeRoute = 'coupons';
                } else if (pathname.includes('/admin/content/banners')) {
                    activeRoute = 'content.banners';
                } else if (pathname.includes('/admin/content/features')) {
                    activeRoute = 'content.features';
                } else if (pathname.includes('/admin/content/customer-services')) {
                    activeRoute = 'content.customer-services';
                } else if (pathname.includes('/admin/content/banks')) {
                    activeRoute = 'content.banks';
                } else if (pathname.includes('/admin/content/socials')) {
                    activeRoute = 'content.socials';
                } else if (pathname.includes('/admin/content/handles')) {
                    activeRoute = 'content.handles';
                } else if (pathname.includes('/admin/dashboard') || pathname === '/admin') {
                    activeRoute = 'dashboard';
                }

                const navLinks = document.querySelectorAll('#admin-sidebar-nav a[data-spa-route]');
                navLinks.forEach((link) => {
                    const routeId = link.getAttribute('data-spa-route');
                    const isActive = routeId === activeRoute;

                    if (isActive) {
                        link.classList.add('bg-black', 'text-white', 'font-semibold');
                        link.classList.remove('text-slate-600', 'hover:bg-slate-50', 'hover:text-slate-900');
                    } else {
                        link.classList.remove('bg-black', 'text-white', 'font-semibold');
                        link.classList.add('text-slate-600', 'hover:bg-slate-50', 'hover:text-slate-900');
                    }
                });
            } catch (e) {
                console.warn('[AdminSPA] Error updating sidebar active link:', e);
            }
        },

        /**
         * Execute inline scripts and reinitialize Alpine tree
         */
        executeScriptsAndInitAlpine(container) {
            if (!container) return;

            // 1. Re-run inline scripts inside container
            const scripts = container.querySelectorAll('script');
            scripts.forEach((oldScript) => {
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach((attr) => {
                    newScript.setAttribute(attr.name, attr.value);
                });
                newScript.textContent = oldScript.textContent;
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });

            // 2. Re-initialize Alpine on the new content tree
            if (window.Alpine) {
                try {
                    window.Alpine.initTree(container);
                } catch (err) {
                    console.warn('[AdminSPA] Alpine tree re-init warning:', err);
                }
            }
        },

        /**
         * Apply newly fetched HTML content to the DOM
         */
        applyContent(htmlString, targetUrl, options = {}) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(htmlString, 'text/html');

            const newMain = doc.querySelector('#admin-main-content');
            const currentMain = document.querySelector('#admin-main-content');

            if (!newMain || !currentMain) {
                console.warn('[AdminSPA] Missing #admin-main-content. Falling back to native navigation.');
                window.location.href = targetUrl;
                return false;
            }

            // 1. Update document title
            const newTitle = doc.querySelector('title')?.textContent;
            if (newTitle) {
                document.title = newTitle;
            }

            // 2. Update page header title
            const newPageTitle = doc.querySelector('#admin-page-title');
            const currentPageTitle = document.querySelector('#admin-page-title');
            if (newPageTitle && currentPageTitle) {
                currentPageTitle.innerHTML = newPageTitle.innerHTML;
            }

            // 3. Update flash alert container
            const newFlash = doc.querySelector('#admin-flash-container');
            const currentFlash = document.querySelector('#admin-flash-container');
            if (currentFlash) {
                currentFlash.innerHTML = newFlash ? newFlash.innerHTML : '';
            }

            // 4. Update main body content
            currentMain.innerHTML = newMain.innerHTML;

            // 5. Update browser history if requested
            if (options.pushHistory !== false) {
                history.pushState({ url: targetUrl }, '', targetUrl);
            }

            // 6. Update sidebar navigation active links
            this.updateSidebarActiveLink(targetUrl);

            // 7. Auto close mobile sidebar drawer
            window.dispatchEvent(new CustomEvent('close-sidebar'));

            // 8. Execute scripts and init Alpine tree
            this.executeScriptsAndInitAlpine(currentMain);

            // 9. Scroll smoothly to top
            window.scrollTo({ top: 0, behavior: 'instant' });

            // 10. Dispatch page loaded lifecycle event
            window.dispatchEvent(new CustomEvent('admin:spa-navigated', {
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

            // Prevent duplicate navigation to exact same URL if not a force refresh
            if (!options.force && targetUrl.href === window.location.href) {
                return;
            }

            // Abort any ongoing navigation
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
                    window.location.href = '/admin/login';
                    return;
                }

                const finalUrl = response.url || targetUrl.href;

                // If redirected to non-admin page, navigate natively
                if (new URL(finalUrl, window.location.origin).origin !== window.location.origin ||
                    !new URL(finalUrl, window.location.origin).pathname.startsWith('/admin')) {
                    window.location.href = finalUrl;
                    return;
                }

                const html = await response.text();
                this.applyContent(html, finalUrl, options);
            } catch (err) {
                if (err.name === 'AbortError') {
                    // Ignored: new navigation took precedence
                    return;
                }
                console.error('[AdminSPA] Navigation error:', err);
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
                form.submit();
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
                        'Accept': 'text/html, application/xhtml+xml, */*'
                    }
                });

                // Check for auth redirect
                if (response.redirected && (response.url.includes('/login') || response.url.includes('/admin/login'))) {
                    window.location.href = response.url;
                    return;
                }

                if (response.status === 401 || response.status === 419) {
                    window.location.href = '/admin/login';
                    return;
                }

                const finalUrl = response.url || window.location.href;

                // If redirected outside admin area
                if (!new URL(finalUrl, window.location.origin).pathname.startsWith('/admin')) {
                    window.location.href = finalUrl;
                    return;
                }

                const html = await response.text();
                this.applyContent(html, finalUrl, { pushHistory: true });
            } catch (err) {
                if (err.name === 'AbortError') return;
                console.error('[AdminSPA] Form submission error:', err);
                form.submit();
            } finally {
                this.finishProgress();
                this.isNavigating = false;
            }
        },

        /**
         * Bind document delegated click and submit event listeners
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

                // If default was already prevented by an inline onsubmit (e.g. confirm cancel)
                if (e.defaultPrevented) return;

                const actionUrl = new URL(form.action || window.location.href, window.location.origin);
                if (this.isExcluded(actionUrl, form)) {
                    return;
                }

                e.preventDefault();
                this.submitForm(form, e.submitter);
            });

            // History popstate listener (Back / Forward buttons)
            window.addEventListener('popstate', (e) => {
                this.navigate(window.location.href, { pushHistory: false });
            });
        },

        /**
         * Sync initial state on first page load
         */
        syncInitialState() {
            this.updateSidebarActiveLink(window.location.href);
            if (!history.state || !history.state.url) {
                history.replaceState({ url: window.location.href }, '', window.location.href);
            }
        }
    };

    // Auto-initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => AdminSPA.init());
    } else {
        AdminSPA.init();
    }

    // Expose to window for external control
    window.AdminSPA = AdminSPA;
})();

