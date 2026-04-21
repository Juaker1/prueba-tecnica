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
