(() => {
    "use strict";

    const catalogo = document.querySelector("#catalogo-peliculas");
    if (!catalogo) return;

    const peliculas = Array.from(catalogo.querySelectorAll("[data-pelicula]"));
    const busqueda = document.querySelector("#busqueda-peliculas");
    const orden = document.querySelector("#orden-peliculas");
    const botonFiltroFavoritas = document.querySelector("#filtro-favoritas");
    const contador = document.querySelector("#contador-peliculas");
    let estadoVacio = document.querySelector("#estado-vacio");
    let mostrarSoloFavoritas = false;

    const claveFavoritas = "cineplus-favoritas";

    function leerFavoritas() {
        try {
            const guardadas = JSON.parse(localStorage.getItem(claveFavoritas) || "[]");
            return new Set(Array.isArray(guardadas) ? guardadas.map(String) : []);
        } catch {
            return new Set();
        }
    }

    const favoritas = leerFavoritas();

    function guardarFavoritas() {
        try {
            localStorage.setItem(claveFavoritas, JSON.stringify(Array.from(favoritas)));
        } catch {
            // Los botones siguen funcionando durante esta visita aunque el navegador
            // tenga desactivado el almacenamiento local.
        }
    }

    function normalizar(texto) {
        return String(texto || "")
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toLocaleLowerCase("es");
    }

    function mostrarNotificacion(mensaje, tipo) {
        const alerta = document.createElement("div");
        alerta.className = `alert alert-${tipo} alert-dismissible fade show position-fixed top-0 end-0 m-3 shadow`;
        alerta.style.zIndex = "1080";
        alerta.setAttribute("role", tipo === "success" ? "status" : "alert");
        alerta.setAttribute("aria-live", tipo === "success" ? "polite" : "assertive");

        const texto = document.createElement("span");
        texto.textContent = mensaje;

        const cerrar = document.createElement("button");
        cerrar.type = "button";
        cerrar.className = "btn-close";
        cerrar.setAttribute("aria-label", "Cerrar aviso");

        let temporizador;
        const quitarAlerta = () => {
            window.clearTimeout(temporizador);
            alerta.classList.remove("show");
            window.setTimeout(() => alerta.remove(), 200);
        };

        cerrar.addEventListener("click", quitarAlerta);
        alerta.append(texto, cerrar);
        document.body.appendChild(alerta);
        temporizador = window.setTimeout(quitarAlerta, 5000);
    }

    function mostrarMensajeDeOperacion() {
        const url = new URL(window.location.href);
        const estado = url.searchParams.get("mensaje");
        const mensajes = {
            guardada: { texto: "Película guardada correctamente.", tipo: "success" },
            eliminada: { texto: "Película eliminada correctamente.", tipo: "success" },
            error_guardar: { texto: "No se pudieron guardar los cambios.", tipo: "danger" },
            error_eliminar: { texto: "No se pudo eliminar la película.", tipo: "danger" }
        };

        if (!mensajes[estado]) return;

        mostrarNotificacion(mensajes[estado].texto, mensajes[estado].tipo);
        url.searchParams.delete("mensaje");
        window.history.replaceState({}, "", url);
    }

    function actualizarBotonesFavoritos() {
        peliculas.forEach((pelicula) => {
            const boton = pelicula.querySelector(".boton-favorita");
            if (!boton) return;

            const esFavorita = favoritas.has(pelicula.dataset.id);
            boton.textContent = esFavorita ? "★" : "☆";
            boton.title = esFavorita ? "Quitar de favoritas" : "Agregar a favoritas";
            boton.setAttribute("aria-pressed", String(esFavorita));
            boton.setAttribute(
                "aria-label",
                `${esFavorita ? "Quitar" : "Agregar"} ${pelicula.dataset.titulo} ${esFavorita ? "de" : "a"} favoritas`
            );
        });
    }

    function crearEstadoVacio() {
        const columna = document.createElement("div");
        columna.id = "estado-vacio";
        columna.className = "col-12";
        columna.innerHTML = `
            <div class="sin-resultados">
                <h3 class="h5">No hay películas que coincidan</h3>
                <p class="text-secondary mb-0">Prueba con otra búsqueda o cambia los filtros.</p>
            </div>
        `;
        catalogo.appendChild(columna);
        return columna;
    }

    function actualizarCatalogo() {
        const palabras = normalizar(busqueda ? busqueda.value.trim() : "")
            .split(/\s+/)
            .filter(Boolean);
        const criterio = orden ? orden.value : "recientes";

        const ordenadas = [...peliculas].sort((a, b) => {
            if (criterio === "titulo") {
                return a.dataset.titulo.localeCompare(b.dataset.titulo, "es", { sensitivity: "base" });
            }
            if (criterio === "calificacion") {
                return Number(b.dataset.calificacion) - Number(a.dataset.calificacion);
            }
            if (criterio === "anio") {
                return Number(b.dataset.anio) - Number(a.dataset.anio);
            }
            return Number(a.dataset.posicion) - Number(b.dataset.posicion);
        });

        ordenadas.forEach((pelicula) => catalogo.appendChild(pelicula));

        let cantidadVisible = 0;
        peliculas.forEach((pelicula) => {
            const textoPelicula = normalizar([
                pelicula.dataset.titulo,
                pelicula.dataset.genero,
                pelicula.dataset.director,
                pelicula.dataset.anio
            ].join(" "));
            const coincideBusqueda = palabras.every((palabra) => textoPelicula.includes(palabra));
            const coincideFavoritas = !mostrarSoloFavoritas || favoritas.has(pelicula.dataset.id);
            const visible = coincideBusqueda && coincideFavoritas;

            pelicula.classList.toggle("d-none", !visible);
            if (visible) cantidadVisible += 1;
        });

        if (contador) {
            contador.textContent = `Mostrando ${cantidadVisible} de ${peliculas.length} película(s)`;
        }

        if (!estadoVacio && cantidadVisible === 0) {
            estadoVacio = crearEstadoVacio();
        }
        if (estadoVacio) {
            estadoVacio.classList.toggle("d-none", cantidadVisible !== 0);
        }
    }

    peliculas.forEach((pelicula, posicion) => {
        pelicula.dataset.posicion = String(posicion);
    });

    catalogo.addEventListener("click", (evento) => {
        const boton = evento.target.closest(".boton-favorita");
        if (!boton) return;

        const pelicula = boton.closest("[data-pelicula]");
        if (!pelicula) return;

        if (favoritas.has(pelicula.dataset.id)) {
            favoritas.delete(pelicula.dataset.id);
        } else {
            favoritas.add(pelicula.dataset.id);
        }

        guardarFavoritas();
        actualizarBotonesFavoritos();
        actualizarCatalogo();
    });

    catalogo.addEventListener("submit", (evento) => {
        const formulario = evento.target.closest("[data-confirmar-eliminacion]");
        if (formulario && !window.confirm("¿Seguro que deseas eliminar esta película?")) {
            evento.preventDefault();
        }
    });

    if (busqueda) busqueda.addEventListener("input", actualizarCatalogo);
    if (orden) orden.addEventListener("change", actualizarCatalogo);

    if (botonFiltroFavoritas) {
        botonFiltroFavoritas.addEventListener("click", () => {
            mostrarSoloFavoritas = !mostrarSoloFavoritas;
            botonFiltroFavoritas.setAttribute("aria-pressed", String(mostrarSoloFavoritas));
            botonFiltroFavoritas.textContent = mostrarSoloFavoritas
                ? "★ Mostrando favoritas"
                : "☆ Solo favoritas";
            actualizarCatalogo();
        });
    }

    mostrarMensajeDeOperacion();
    actualizarBotonesFavoritos();
    actualizarCatalogo();
})();
