/* public/frontend/pages/county.js */
(function () {
    "use strict";

    // const geoId  = parseInt(document.getElementById('geo')?.value || "0", 10) || 0;
    // const gridURL = window.COUNTY_GRID_URL;       // you already use for product grid reloads
    // const kidsURL = window.GEO_CHILDREN_URL;      // new
    const routeBase = (window.COUNTY_SHOW_ROUTE || "/county/"); // /county/{id}/{slug}
    const geoId   = window.CURRENT_GEO_ID || 0;
    const gridURL = window.COUNTY_GRID_URL;
    const kidsURL = window.GEO_CHILDREN_URL;

    // ---- render children as pills ----
    function renderChildren(list) {
        const host = document.getElementById('child-geos');
        if (!host) return;

        if (!Array.isArray(list) || !list.length) {
            host.innerHTML = ""; // hide/empty if none
            return;
        }

        const wrap = document.createElement('div');
        wrap.className = "d-flex flex-wrap gap-2";

        list.forEach(item => {
            const a = document.createElement('a');
            a.className = "badge bg-light text-dark border";
            a.href = routeBase + encodeURIComponent(item.id) + "/" + encodeURIComponent(item.slug);
            a.textContent = item.name;
            wrap.appendChild(a);
        });

        host.innerHTML = `
      <div class="mb-2 fw-semibold">${window.trans_children_of ?? 'Sub-locations'}</div>
    `;
        host.appendChild(wrap);
    }

    // ---- fetch children for current geo ----
    async function loadChildren(parentId){
        const res = await fetch(`${kidsURL}?parent_id=${encodeURIComponent(parentId)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if(!res.ok) return [];
        return await res.json();
    }
    document.addEventListener('DOMContentLoaded', async () => {
        if (!geoId) return;
        const kids = await loadChildren(geoId);
        const cont = document.getElementById('child-geos');
        if (!cont) return;

        if (!kids.length) { cont.innerHTML = ''; return; }

        // simple pill list (linking to /county/{id}/{slug})
        cont.innerHTML = `
    <div class="d-flex flex-wrap gap-2 align-items-center">
      <strong>{{ __('Sub-locations') }}:</strong>
      ${kids.map(k => {
            const url = `${window.COUNTY_SHOW_ROUTE}${k.id}/${encodeURIComponent(k.slug || k.name.toLowerCase().replace(/[^a-z0-9]+/g,'-'))}`;
            return `<a class="badge bg-light text-dark border" href="${url}">${k.name}${k.count !== undefined ? ` (${k.count})` : ''}</a>`;
        }).join('')}
    </div>
  `;
    });
})();
