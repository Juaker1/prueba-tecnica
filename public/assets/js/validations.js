// ── Botón limpiar filtros ────────────────────────────────────────────────────
// Habilita o deshabilita el botón X según si hay filtros activos en el formulario.

(function () {
    const form       = document.getElementById('filtroForm');
    const btnLimpiar = document.getElementById('btnLimpiar');

    if (!form || !btnLimpiar) return;

    function actualizarBotonLimpiar() {
        const busqueda = form.querySelector('[name="busqueda"]')?.value.trim() ?? '';
        const estado   = form.querySelector('[name="estado"]')?.value           ?? '';
        const tipo     = form.querySelector('[name="tipo"]')?.value             ?? '';

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

// ── Validación formulario login ──────────────────────────────────────────────

(function () {
    const form = document.getElementById('loginForm');
    if (!form) return;

    function marcarInvalido(input) {
        input.classList.add('is-invalid');
    }

    function limpiarInvalido(input) {
        input.classList.remove('is-invalid');
    }

    function esEmailValido(valor) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor.trim());
    }

    form.querySelectorAll('input').forEach(function (input) {
        input.addEventListener('input', function () {
            limpiarInvalido(input);
        });
    });

    form.addEventListener('submit', function (e) {
        let valido = true;

        const email    = form.querySelector('#email');
        const password = form.querySelector('#password');

        if (!email.value.trim() || !esEmailValido(email.value)) {
            marcarInvalido(email);
            valido = false;
        }

        if (!password.value) {
            marcarInvalido(password);
            valido = false;
        }

        if (!valido) {
            e.preventDefault();
        }
    });
}());

// ── Validación formulario nueva solicitud ────────────────────────────────────

(function () {
    const form = document.getElementById('createForm');
    if (!form) return;

    function marcarInvalido(input) {
        input.classList.add('is-invalid');
    }

    function limpiarInvalido(input) {
        input.classList.remove('is-invalid');
    }

    function esEmailValido(valor) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor.trim());
    }

    form.querySelectorAll('input, select, textarea').forEach(function (el) {
        el.addEventListener('input', function () {
            limpiarInvalido(el);
        });
        el.addEventListener('change', function () {
            limpiarInvalido(el);
        });
    });

    form.addEventListener('submit', function (e) {
        let valido = true;

        const nombre      = form.querySelector('#nombre_solicitante');
        const correo      = form.querySelector('#correo');
        const tipo        = form.querySelector('#tipo');
        const descripcion = form.querySelector('#descripcion');

        if (!nombre.value.trim()) {
            marcarInvalido(nombre);
            valido = false;
        }

        if (!correo.value.trim() || !esEmailValido(correo.value)) {
            marcarInvalido(correo);
            valido = false;
        }

        if (!tipo.value) {
            marcarInvalido(tipo);
            valido = false;
        }

        if (!descripcion.value.trim()) {
            marcarInvalido(descripcion);
            valido = false;
        }

        if (!valido) {
            e.preventDefault();
        }
    });
}());

// ── Filtrado en tiempo real del listado ──────────────────────────────────────

(function () {
    const form          = document.getElementById('filtroForm');
    const tbody         = document.querySelector('#tablaListado tbody');
    const sinResultados = document.getElementById('sinResultados');
    const tablaCard     = document.getElementById('tablaCard');
    const contador      = document.getElementById('contadorResultados');

    if (!form || !tbody) return;

    const inputBusqueda = form.querySelector('[name="busqueda"]');
    const selectEstado  = form.querySelector('[name="estado"]');
    const selectTipo    = form.querySelector('[name="tipo"]');

    let debounceTimer = null;

    function aplicarFiltros() {
        const busqueda = inputBusqueda ? inputBusqueda.value.trim().toLowerCase() : '';
        const estado   = selectEstado  ? selectEstado.value  : '';
        const tipo     = selectTipo    ? selectTipo.value    : '';

        let visibles = 0;

        tbody.querySelectorAll('tr').forEach(function (fila) {
            const coincideBusqueda = !busqueda ||
                fila.dataset.nombre.includes(busqueda) ||
                (fila.dataset.correo && fila.dataset.correo.includes(busqueda));

            const coincideEstado = !estado || fila.dataset.estado === estado;
            const coincideTipo   = !tipo   || fila.dataset.tipo   === tipo;

            const mostrar = coincideBusqueda && coincideEstado && coincideTipo;
            fila.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        const sinDatos = visibles === 0;

        if (sinResultados) sinResultados.style.display = sinDatos ? ''     : 'none';
        if (tablaCard)     tablaCard.style.display     = sinDatos ? 'none' : '';
        if (contador)      contador.style.display      = sinDatos ? 'none' : '';
    }

    if (selectEstado) {
        selectEstado.addEventListener('change', aplicarFiltros);
    }

    if (selectTipo) {
        selectTipo.addEventListener('change', aplicarFiltros);
    }

    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(aplicarFiltros, 300);
        });
    }
}());
