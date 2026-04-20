// ── Botón limpiar filtros ────────────────────────────────────────────────────
// Habilita o deshabilita el botón X según si hay filtros activos en el formulario.

(function () {
    const form       = document.getElementById('filtroForm');
    const btnLimpiar = document.getElementById('btnLimpiar');

    if (!form || !btnLimpiar) return;

    function actualizarBotonLimpiar() {
        const busqueda = document.getElementById('busqueda')?.value.trim() ?? '';
        const estado   = document.getElementById('estado')?.value           ?? '';
        const tipo     = document.getElementById('tipo')?.value             ?? '';

        const hayFiltros = busqueda !== '' || estado !== '' || tipo !== '';

        btnLimpiar.classList.toggle('disabled', !hayFiltros);
        btnLimpiar.setAttribute('aria-disabled', String(!hayFiltros));
        btnLimpiar.setAttribute('tabindex', hayFiltros ? '0' : '-1');
    }

    form.querySelectorAll('input, select').forEach(function (el) {
        el.addEventListener('input', actualizarBotonLimpiar);
        el.addEventListener('change', actualizarBotonLimpiar);
    });
}());
