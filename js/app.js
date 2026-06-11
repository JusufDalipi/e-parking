document.addEventListener('DOMContentLoaded', function () {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('overlay');
    if (sidebar && overlay) {
        var closeNav = function () {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        };

        // Close sidebar when clicking on links
        sidebar.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', closeNav);
        });
    }

    document.querySelectorAll('.js-validate').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            var valid = true;

            form.querySelectorAll('[required]').forEach(function (field) {
                if (!field.value.trim()) {
                    valid = false;
                    field.classList.add('field-error');
                } else {
                    field.classList.remove('field-error');
                }
            });

            var emailInput = form.querySelector('input[type="email"]');
            if (emailInput && emailInput.value.trim() !== '') {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailInput.value.trim())) {
                    valid = false;
                    emailInput.classList.add('field-error');
                }
            }

            if (!valid) {
                event.preventDefault();
                alert('Ju lutem plotesoni fushat e detyrueshme me vlere valide.');
            }
        });
    });

    document.querySelectorAll('.js-delete').forEach(function (link) {
        link.addEventListener('click', function (event) {
            if (!confirm('A jeni i sigurt qe doni te fshini kete rekord?')) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('.js-table-filter').forEach(function (input) {
        input.addEventListener('input', function () {
            var term = input.value.trim().toLowerCase();
            var tableId = input.dataset.target;
            var table = document.getElementById(tableId);
            if (!table) {
                return;
            }

            var items = table.querySelectorAll('tbody tr, .slot-item');
            items.forEach(function (row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(term) > -1 ? '' : 'none';
            });
        });
    });
});

// --- SPA Interceptor Logic ---
function loadPage(url, options = {}) {
    const token = sessionStorage.getItem('jwt');
    const headers = options.headers || {};
    if (token) {
        headers['Authorization'] = 'Bearer ' + token;
    }
    headers['X-Requested-With'] = 'XMLHttpRequest';

    return fetch(url, { ...options, headers })
        .then(res => {
            if (res.status === 401) {
                sessionStorage.removeItem('jwt');
                window.location.replace('/Websherbimeprojekti/login.php');
                throw new Error('Unauthorized');
            }
            if (res.status === 403) {
                alert('Qasje e ndaluar!');
                throw new Error('Forbidden');
            }
            return res.text();
        })
        .then(html => {
            if (html) {
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');
                document.title = doc.title;
                document.body.innerHTML = doc.body.innerHTML;

                Array.from(document.body.querySelectorAll('script')).forEach(oldScript => {
                    if (oldScript.src && oldScript.src.includes('app.js')) return; // Prevent duplicating app.js

                    const newScript = document.createElement('script');
                    Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                    newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                    if (oldScript.parentNode) {
                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    }
                });

                document.dispatchEvent(new Event('DOMContentLoaded'));
                window.scrollTo(0, 0);
            }
        });
}

document.addEventListener('click', function (e) {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('overlay');

    // Toggle sidebar via the hamburger icon (event delegation)
    if (e.target.closest('#nav-toggle')) {
        if (sidebar) sidebar.classList.toggle('active');
        if (overlay) overlay.classList.toggle('active');
        return;
    }

    // Close sidebar via the ✕ button (event delegation — works after SPA re-render)
    if (e.target.closest('#sidebar-close')) {
        if (sidebar) sidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        return;
    }

    // Close sidebar via overlay click
    if (e.target === overlay || e.target.closest('#overlay')) {
        if (sidebar) sidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        return;
    }

    const link = e.target.closest('a');
    if (link && link.href && link.origin === window.location.origin) {
        if (link.target === '_blank') return;

        // Always close sidebar when any link is clicked
        if (sidebar) sidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('active');

        if (link.hasAttribute('data-no-spa')) return; // navigate directly, no SPA

        const url = new URL(link.href);

        if (url.pathname.endsWith('.php') && !url.pathname.includes('login.php')) {
            e.preventDefault();
            history.pushState({}, '', url.href);
            loadPage(url.href);
        }
    }
});

window.addEventListener('popstate', function () {
    loadPage(window.location.href);
});

document.addEventListener('submit', function (e) {
    const form = e.target;
    if (form.id === 'loginForm') {
        e.preventDefault();
        const formData = new FormData(form);
        fetch(form.action || '/Websherbimeprojekti/php/login_process.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(res => res.json()).then(data => {
            if (data.status === 'success') {
                sessionStorage.setItem('jwt', data.token);
                window.location.href = '/Websherbimeprojekti/index.php';
            } else {
                alert(data.error || 'Gabim gjate identifikimit');
            }
        }).catch(err => {
            alert('Gabim gjate identifikimit');
        });
        return;
    }

    if (form.id === 'registerForm') {
        e.preventDefault();
        const formData = new FormData(form);
        fetch(form.action || '/Websherbimeprojekti/php/register_process.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(res => res.json()).then(data => {
            if (data.status === 'success') {
                window.location.href = '/Websherbimeprojekti/login.php';
            } else {
                alert(data.error || 'Gabim gjatë regjistrimit');
            }
        }).catch(err => {
            alert('Gabim gjatë regjistrimit');
        });
        return;
    }

    // Allow js-validate to run first. If the form isn't prevented, we intercept it.
    if (!e.defaultPrevented && form.method && form.method.toLowerCase() === 'post') {
        e.preventDefault();
        const formData = new FormData(form);
        loadPage(form.action, { method: 'POST', body: formData }).then(() => {
            history.pushState({}, '', form.action);
        });
    }
});
