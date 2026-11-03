{{-- Akselerator Navigasi Instan & Prefetching Modern --}}
<script type="speculationrules">
{
  "prefetch": [
    {
      "source": "document",
      "where": {
        "and": [
          { "href_matches": "/*" },
          { "not": { "href_matches": "/*logout*" } },
          { "not": { "href_matches": "/*export*" } },
          { "not": { "href_matches": "/*download*" } },
          { "not": { "href_matches": "/*hapus*" } },
          { "not": { "href_matches": "/*delete*" } }
        ]
      },
      "eagerness": "moderate"
    }
  ]
}
</script>
<script>
(function () {
    if (typeof window === 'undefined') return;
    if (window.HTMLScriptElement && HTMLScriptElement.supports && HTMLScriptElement.supports('speculationrules')) return;
    if (navigator.connection && navigator.connection.saveData) return;

    var prefetched = new Set();

    function prefetch(url) {
        if (!url || prefetched.has(url)) return;
        prefetched.add(url);
        var link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = url;
        link.as = 'document';
        document.head.appendChild(link);
    }

    function resolveValidUrl(target) {
        var a = target && target.closest ? target.closest('a') : null;
        if (!a || (a.target && a.target !== '_self') || a.hasAttribute('download') || a.hasAttribute('data-no-prefetch')) return null;

        var href = a.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return null;

        try {
            var u = new URL(a.href, window.location.href);
            if (u.origin !== window.location.origin) return null;
            if (u.pathname === window.location.pathname && u.search === window.location.search) return null;

            var path = u.pathname.toLowerCase();
            if (path.includes('logout') || path.includes('export') || path.includes('download') || path.includes('hapus') || path.includes('delete')) {
                return null;
            }
            return u.href;
        } catch (_) {
            return null;
        }
    }

    var hoverTimer = null;
    document.addEventListener('pointerenter', function (e) {
        var url = resolveValidUrl(e.target);
        if (!url) return;
        hoverTimer = setTimeout(function () {
            prefetch(url);
        }, 65);
    }, { capture: true, passive: true });

    document.addEventListener('pointerout', function () {
        if (hoverTimer) {
            clearTimeout(hoverTimer);
            hoverTimer = null;
        }
    }, { capture: true, passive: true });

    document.addEventListener('touchstart', function (e) {
        var url = resolveValidUrl(e.target);
        if (url) {
            prefetch(url);
        }
    }, { capture: true, passive: true });
})();
</script>
