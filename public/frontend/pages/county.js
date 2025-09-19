/* public/frontend/pages/county.js */
(function () {
    "use strict";

    // Globals provided by the Blade view
    const routeBase = (window.COUNTY_SHOW_ROUTE || "/county/");   // /county/{id}/{slug}
    const geoId     = window.CURRENT_GEO_ID || 0;
    const kidsURL   = window.GEO_CHILDREN_URL;

    function slugify(txt){
        return String(txt || "")
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/^-+|-+$/g, "");
    }

    async function loadChildren(parentId){
        try{
            const res = await fetch(
                `${kidsURL}?parent_id=${encodeURIComponent(parentId)}`,
                { headers: { "X-Requested-With": "XMLHttpRequest" } }
            );
            if (!res.ok) return [];
            return await res.json();
        } catch(e){
            return [];
        }
    }

    document.addEventListener("DOMContentLoaded", async () => {
        if (!geoId) return;

        const cont = document.getElementById("child-geos");
        if (!cont) return;

        const kids = await loadChildren(geoId);
        if (!Array.isArray(kids) || !kids.length) {
            cont.innerHTML = "";
            return;
        }

        const label = (window.trans_children || "Sub-locations") + ":";

        cont.innerHTML = `
      <div class="d-flex flex-wrap gap-2 align-items-center">
        <strong>${label}</strong>
        ${kids.map(k => {
            const s = k.slug || slugify(k.name);
            const url = `${routeBase}${encodeURIComponent(k.id)}/${encodeURIComponent(s)}`;
            const suffix = (k.count !== undefined && k.count !== null) ? ` (${k.count})` : "";
            return `<a class="badge bg-light text-dark border" href="${url}">${k.name}${suffix}</a>`;
        }).join("")}
      </div>
    `;
    });
})();
