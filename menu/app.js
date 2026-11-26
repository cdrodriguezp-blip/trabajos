(() => {
    const NS = 'http://www.w3.org/2000/svg';
    const form = document.querySelector('form');
    const salida = document.querySelector('.resultado');

    const crear = (tag, atributos = {}, texto = null, ns = null) => {
        const e = ns ? document.createElementNS(ns, tag) : document.createElement(tag);
        for (const [clave, valor] of Object.entries(atributos)) e.setAttribute(clave, valor);
        if (texto !== null) e.textContent = texto;
        return e;
    };
    const svg = (tag, atributos, texto) => crear(tag, atributos, texto, NS);
    const recortar = (texto, max) => {
        texto = String(texto);
        return texto.length > max ? texto.slice(0, max - 1) + '…' : texto;
    };

    function dibujarArbol(raiz) {
        const R = 24, DX = 64, DY = 84, M = 36;
        const nodos = [], aristas = [];
        let columna = 0, profundidad = 0;

        const colocar = (n, nivel) => {
            if (!n) return null;
            const izq = colocar(n.izq, nivel + 1);
            const p = { valor: String(n.valor), col: columna++, nivel, hoja: !n.izq && !n.der };
            const der = colocar(n.der, nivel + 1);
            nodos.push(p);
            for (const hijo of [izq, der]) if (hijo) aristas.push([p, hijo]);
            profundidad = Math.max(profundidad, nivel);
            return p;
        };
        colocar(raiz, 0);

        const x = (p) => M + p.col * DX;
        const y = (p) => M + p.nivel * DY;
        const ancho = 2 * M + (columna - 1) * DX;
        const alto = 2 * M + profundidad * DY;

        const lienzo = svg('svg', { viewBox: `0 0 ${ancho} ${alto}`, width: ancho, height: alto, role: 'img', 'aria-label': 'Árbol binario reconstruido' });
        for (const [padre, hijo] of aristas) {
            lienzo.append(svg('line', { class: 'arista', x1: x(padre), y1: y(padre), x2: x(hijo), y2: y(hijo) }));
        }
        for (const p of nodos) {
            const clase = p.nivel === 0 ? 'nodo raiz' : p.hoja ? 'nodo hoja' : 'nodo';
            const g = svg('g', { class: clase });
            const tamano = p.valor.length <= 2 ? 17 : p.valor.length <= 3 ? 13 : 10;
            g.append(
                svg('title', {}, p.valor),
                svg('circle', { cx: x(p), cy: y(p), r: R }),
                svg('text', { x: x(p), y: y(p), 'font-size': tamano }, recortar(p.valor, 5))
            );
            lienzo.append(g);
        }

        const leyenda = crear('div', { class: 'leyenda' });
        leyenda.append(crear('span', { class: 'l-raiz' }, 'Raíz'), crear('span', { class: 'l-hoja' }, 'Hoja'));
        const contenedor = crear('div', { class: 'grafico' });
        contenedor.append(lienzo, leyenda);
        return contenedor;
    }

    function dibujarVenn({ soloA, ambos, soloB }) {
        const A = { cx: 240, cy: 180, r: 130 };
        const B = { cx: 400, cy: 180, r: 130 };
        const lienzo = svg('svg', { viewBox: '0 30 640 300', role: 'img', 'aria-label': 'Diagrama de Venn de los conjuntos A y B' });

        const resalte = svg('g');
        const circulos = [svg('circle', { ...A, class: 'venn-circulo' }), svg('circle', { ...B, class: 'venn-circulo' })];
        const nombres = [svg('text', { x: 120, y: 62, class: 'venn-nombre' }, 'A'), svg('text', { x: 520, y: 62, class: 'venn-nombre' }, 'B')];

        const colocar = (lista, cx, cy, columnas, filas, anchoColumna) => {
            const g = svg('g');
            const capacidad = columnas * filas;
            const vistos = lista.length > capacidad
                ? [...lista.slice(0, capacidad - 1), `+${lista.length - capacidad + 1} más`]
                : lista;
            const usadas = Math.min(columnas, Math.ceil(vistos.length / filas));
            const filasUsadas = Math.ceil(vistos.length / usadas);
            vistos.forEach((texto, i) => {
                const col = Math.floor(i / filasUsadas), fila = i % filasUsadas;
                g.append(svg('text', {
                    class: 'venn-item',
                    x: cx + (col - (usadas - 1) / 2) * anchoColumna,
                    y: cy + (fila - (filasUsadas - 1) / 2) * 23,
                }, recortar(texto, columnas > 1 ? 6 : 8)));
            });
            return g;
        };

        lienzo.append(resalte, ...circulos, ...nombres,
            colocar(soloA, 188, 180, 2, 7, 66),
            colocar(ambos, 320, 180, 1, 8, 0),
            colocar(soloB, 452, 180, 2, 7, 66));

        const resaltar = (modo) => {
            resalte.replaceChildren();
            const figura = (c, extra = {}) => svg('circle', { ...c, class: 'venn-resalte', ...extra });
            if (modo === 'union') {
                resalte.append(figura(A), figura(B));
            } else if (modo === 'interseccion') {
                const recorte = svg('clipPath', { id: 'venn-recorte' });
                recorte.append(svg('circle', A));
                resalte.append(recorte, figura(B, { 'clip-path': 'url(#venn-recorte)' }));
            } else {
                const [dentro, fuera] = modo === 'a-b' ? [A, B] : [B, A];
                const mascara = svg('mask', { id: 'venn-mascara' });
                mascara.append(svg('circle', { ...dentro, fill: '#fff' }), svg('circle', { ...fuera, fill: '#000' }));
                resalte.append(mascara, figura(dentro, { mask: 'url(#venn-mascara)' }));
            }
        };

        const contenedor = crear('div', { class: 'grafico' });
        contenedor.append(lienzo, crear('p', { class: 'nota' }, 'Toca una operación de la lista para resaltarla en el diagrama.'));
        return { contenedor, resaltar };
    }

    function mostrar(datos) {
        salida.replaceChildren();
        if (datos.error) {
            salida.append(crear('p', { class: 'error', role: 'alert' }, datos.error));
            return;
        }

        const lista = crear('div', { class: 'lineas' });
        for (const [etiqueta, valor, region] of datos.lineas) {
            const fila = crear(region ? 'button' : 'div', { class: 'linea' });
            if (region) {
                fila.setAttribute('type', 'button');
                fila.dataset.region = region;
            }
            fila.append(crear('span', { class: 'etiqueta' }, etiqueta), crear('span', { class: 'valor' }, valor));
            lista.append(fila);
        }
        salida.append(lista);

        const grafico = datos.grafico;
        if (grafico?.tipo === 'arbol') salida.append(dibujarArbol(grafico.raiz));
        if (grafico?.tipo === 'venn') {
            const venn = dibujarVenn(grafico);
            salida.append(venn.contenedor);
            const botones = lista.querySelectorAll('button.linea');
            const activar = (region) => {
                venn.resaltar(region);
                botones.forEach((b) => b.classList.toggle('activa', b.dataset.region === region));
            };
            botones.forEach((b) => b.addEventListener('click', () => activar(b.dataset.region)));
            activar('union');
        }
        if (datos.nota) salida.append(crear('p', { class: 'nota' }, datos.nota));
    }

    const listaHistorial = document.getElementById('historial');
    const CLAVE = 'calculadora-historial';
    const leerHistorial = () => {
        try { return JSON.parse(localStorage.getItem(CLAVE)) || []; } catch { return []; }
    };
    const guardarHistorial = (items) => {
        try { localStorage.setItem(CLAVE, JSON.stringify(items.slice(0, 30))); } catch {  }
    };
    const pintarHistorial = (items) => {
        if (!listaHistorial) return;
        listaHistorial.replaceChildren(...(items.length
            ? items.map((texto) => crear('li', {}, texto))
            : [crear('li', {}, 'Aún no hay operaciones.')]));
    };

    if (listaHistorial) {
        pintarHistorial(leerHistorial());
        document.getElementById('borrar-historial').addEventListener('click', () => {
            guardarHistorial([]);
            pintarHistorial([]);
        });
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        let datos;
        try {
            const respuesta = await fetch('../api.php', { method: 'POST', body: new FormData(form) });
            datos = await respuesta.json();
        } catch {
            datos = { error: 'No se pudo leer la respuesta del servidor. Abre el proyecto con PHP (por ejemplo: php -S localhost:8000 dentro de la carpeta) y no haciendo doble clic en el archivo.' };
        }
        mostrar(datos);
        if (datos.resumen) {
            const items = [datos.resumen, ...leerHistorial()];
            guardarHistorial(items);
            pintarHistorial(items);
        }
    });
})();
