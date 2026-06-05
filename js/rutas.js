// Pega aquí tu API key de Mapbox
const TOKEN_MAPBOX = "pk.eyJ1IjoidW8zMDE4MzEiLCJhIjoiY21xMTdvcTY0MDhzbDJ0czk1dThlbjhpaCJ9.Zaz7F76acdg7kG-8-JEjPQ";

// Carga rutas.xml y crea cada ruta
class Rutas {

    constructor(urlXml) {
        this.urlXml = urlXml;
    }

    // Carga el archivo rutas.xml
    cargar() {
        $.ajax({ url: this.urlXml, method: "GET", dataType: "xml" })
            .done(this.procesar.bind(this))
            .fail(this.mostrarError.bind(this));
    }

    // Por cada ruta del XML crea su contenido, su mapa y su altimetría
    procesar(xml) {
        const contenedor = $("main");

        $(xml).find("ruta").each((indice, elemento) => {
            const ruta = new Ruta($(elemento));
            contenedor.append(ruta.construir());

            new MapaRuta(ruta.urlPlanimetria(), ruta.divMapa()).cargar();
            new Altimetria(ruta.urlAltimetria(), ruta.figuraSvg()).cargar();
        });
    }

    // Avisa si no se ha podido cargar el XML de rutas
    mostrarError() {
        $("main").append("<p>No se ha podido cargar la información de las rutas.</p>");
    }
}


// Construye el marcado de una ruta
class Ruta {

    constructor($ruta) {
        this.$ruta = $ruta;
        this.$divMapa = null;
        this.$figuraSvg = null;
    }

    // Devuelve el texto de un dato de la ruta
    leer(etiqueta) {
        return this.$ruta.children(etiqueta).first().text().trim();
    }

    // Monta todo el bloque HTML de la ruta
    construir() {
        const articulo = $("<article></article>");
        articulo.append("<h2>" + this.leer("nombre") + "</h2>");

        articulo.append(this.construirDatosGenerales());
        articulo.append(this.construirInicio());
        articulo.append(this.construirReferencias());
        articulo.append(this.construirHitos());

        articulo.append("<h3>Planimetría</h3>");
        this.$divMapa = $("<div></div>");
        articulo.append(this.$divMapa);

        articulo.append("<h3>Altimetría</h3>");
        this.$figuraSvg = $("<figure></figure>");
        articulo.append(this.$figuraSvg);

        return articulo;
    }

    // Datos generales de la ruta (tipo, transporte, duración...)
    construirDatosGenerales() {
        const seccion = $("<section></section>");
        seccion.append("<h3>Información general</h3>");
        seccion.append("<p>" + this.leer("descripcion") + "</p>");

        const lista = $("<dl></dl>");
        this.anadirDato(lista, "Tipo", this.leer("tipo"));
        this.anadirDato(lista, "Medio de transporte", this.leer("transporte"));
        this.anadirDato(lista, "Fecha de inicio", this.leer("fechaInicio"));
        this.anadirDato(lista, "Hora de inicio", this.leer("horaInicio"));
        this.anadirDato(lista, "Duración", this.leer("duracion"));
        this.anadirDato(lista, "Agencia", this.leer("agencia"));
        this.anadirDato(lista, "Personas adecuadas", this.leer("personas"));
        this.anadirDato(lista, "Recomendación", this.leer("recomendacion") + " / 10");
        seccion.append(lista);
        return seccion;
    }

    // Lugar, dirección y coordenadas de inicio de la ruta
    construirInicio() {
        const $inicio = this.$ruta.children("inicio").first();
        const seccion = $("<section></section>");
        seccion.append("<h3>Inicio de la ruta</h3>");

        const lista = $("<dl></dl>");
        this.anadirDato(lista, "Lugar", $inicio.children("lugar").text().trim());
        this.anadirDato(lista, "Dirección", $inicio.children("direccion").text().trim());
        this.anadirCoordenadas(lista, $inicio.children("coordenadas").first());
        seccion.append(lista);
        return seccion;
    }

    // Lista de enlaces de referencia de la ruta
    construirReferencias() {
        const seccion = $("<section></section>");
        seccion.append("<h3>Referencias</h3>");

        const lista = $("<ul></ul>");
        this.$ruta.find("referencia").each((indice, elemento) => {
            const url = $(elemento).text().trim();
            const enlace = $("<a></a>").attr("href", url).text(url);
            lista.append($("<li></li>").append(enlace));
        });
        seccion.append(lista);
        return seccion;
    }

    // Recorre los hitos de la ruta y los va añadiendo
    construirHitos() {
        const seccion = $("<section></section>");
        seccion.append("<h3>Hitos de la ruta</h3>");

        let numero = 0;
        this.$ruta.find("hito").each((indice, elemento) => {
            numero = numero + 1;
            seccion.append(this.construirHito($(elemento), numero));
        });
        return seccion;
    }

    // Monta el bloque de un hito (nombre, descripción, coordenadas, fotos...)
    construirHito($hito, numero) {
        const articulo = $("<article></article>");

        const nombre = $hito.children("nombre").first().text().trim();
        articulo.append("<h4>" + (nombre !== "" ? nombre : "Punto de paso " + numero) + "</h4>");

        const descripcion = $hito.children("descripcion").first().text().trim();
        if (descripcion !== "") {
            articulo.append("<p>" + descripcion + "</p>");
        }

        const lista = $("<dl></dl>");
        this.anadirCoordenadas(lista, $hito.children("coordenadas").first());

        const $distancia = $hito.children("distancia").first();
        if ($distancia.length > 0) {
            const unidades = $distancia.attr("unidades") || "";
            this.anadirDato(lista, "Distancia desde el hito anterior", $distancia.text().trim() + " " + unidades);
        }
        articulo.append(lista);

        articulo.append(this.construirFotos($hito, nombre));
        articulo.append(this.construirVideos($hito, nombre));
        return articulo;
    }

    // Crea las fotos del hito
    construirFotos($hito, nombre) {
        let figuras = $();
        $hito.find("foto").each((indice, elemento) => {
            const fuente = $(elemento).text().trim();
            const imagen = $("<img />")
                .attr("src", fuente)
                .attr("alt", "Fotografía de " + (nombre !== "" ? nombre : "un hito de la ruta") + " (" + (indice + 1) + ")");
            figuras = figuras.add($("<figure></figure>").append(imagen));
        });
        return figuras;
    }

    // Crea los vídeos del hito (si tiene)
    construirVideos($hito, nombre) {
        let figuras = $();
        $hito.find("video").each((indice, elemento) => {
            const fuente = $("<source />").attr("src", $(elemento).text().trim());
            const video = $("<video controls preload='metadata'></video>")
                .append(fuente)
                .append("Tu navegador no soporta el elemento de vídeo.");
            const figura = $("<figure></figure>")
                .append(video)
                .append("<figcaption>Vídeo de " + (nombre !== "" ? nombre : "un hito de la ruta") + "</figcaption>");
            figuras = figuras.add(figura);
        });
        return figuras;
    }

    // Añade un par de dato/valor a una lista
    anadirDato(lista, termino, valor) {
        if (valor === undefined || valor === null || valor === "") {
            return;
        }
        lista.append("<dt>" + termino + "</dt>");
        lista.append("<dd>" + valor + "</dd>");
    }

    // Añade las coordenadas (latitud, longitud y altitud) a una lista
    anadirCoordenadas(lista, $coordenadas) {
        if ($coordenadas.length === 0) {
            return;
        }
        const longitud = $coordenadas.children("longitud").text().trim();
        const latitud = $coordenadas.children("latitud").text().trim();
        const $altitud = $coordenadas.children("altitud").first();
        const unidad = $altitud.attr("unidad") || "";

        const hemisferioLat = parseFloat(latitud) >= 0 ? "N" : "S";
        const hemisferioLon = parseFloat(longitud) >= 0 ? "E" : "O";

        const sublista = $("<ul></ul>");
        sublista.append("<li>Latitud: " + latitud.replace("-", "") + "° " + hemisferioLat + "</li>");
        sublista.append("<li>Longitud: " + longitud.replace("-", "") + "° " + hemisferioLon + "</li>");
        sublista.append("<li>Altitud: " + $altitud.text().trim() + " " + unidad + "</li>");

        lista.append("<dt>Coordenadas</dt>");
        lista.append($("<dd></dd>").append(sublista));
    }

    urlPlanimetria() {
        return this.leer("planimetria");
    }

    urlAltimetria() {
        return this.leer("altimetria");
    }

    divMapa() {
        return this.$divMapa.get(0);
    }

    figuraSvg() {
        return this.$figuraSvg.get(0);
    }
}


// Lee el KML y lo dibuja sobre un mapa de Mapbox
class MapaRuta {

    constructor(urlKml, divMapa) {
        this.urlKml = urlKml;
        this.divMapa = divMapa;
    }

    // Carga el archivo KML de la ruta
    cargar() {
        $.ajax({ url: this.urlKml, method: "GET", dataType: "xml" })
            .done(this.dibujar.bind(this))
            .fail(() => {
                $(this.divMapa).text("No se ha podido cargar la planimetría de la ruta.");
            });
    }

    // Saca del KML la línea de la ruta y los puntos con nombre
    dibujar(kml) {
        let linea = [];
        const marcadores = [];

        $(kml).find("Placemark").each((indice, elemento) => {
            const $placemark = $(elemento);
            const $coordsLinea = $placemark.find("LineString coordinates").first();

            if ($coordsLinea.length > 0) {
                linea = this.parsear($coordsLinea.text());
            } else {
                const nombre = $placemark.children("name").first().text().trim();
                const puntos = this.parsear($placemark.find("Point coordinates").first().text());
                if (nombre !== "" && puntos.length > 0) {
                    marcadores.push({ coordenada: puntos[0], nombre: nombre });
                }
            }
        });

        this.crearMapa(linea, marcadores);
    }

    // Convierte el texto de coordenadas del KML en pares longitud/latitud
    parsear(texto) {
        const puntos = [];
        texto.trim().split(/\s+/).forEach((tripleta) => {
            if (tripleta === "") {
                return;
            }
            const valores = tripleta.split(",");
            puntos.push([parseFloat(valores[0]), parseFloat(valores[1])]);
        });
        return puntos;
    }

    // Crea el mapa de Mapbox con la línea y los marcadores de la ruta
    crearMapa(linea, marcadores) {
        mapboxgl.accessToken = TOKEN_MAPBOX;

        const centro = linea.length > 0
            ? linea[0]
            : (marcadores.length > 0 ? marcadores[0].coordenada : [-1.65, 42.81]);

        const mapa = new mapboxgl.Map({
            container: this.divMapa,
            style: "mapbox://styles/mapbox/outdoors-v12",
            center: centro,
            zoom: 12
        });
        mapa.addControl(new mapboxgl.NavigationControl());

        mapa.on("load", () => {
            if (linea.length > 0) {
                mapa.addSource("ruta", {
                    type: "geojson",
                    data: { type: "Feature", geometry: { type: "LineString", coordinates: linea } }
                });
                mapa.addLayer({
                    id: "ruta",
                    type: "line",
                    source: "ruta",
                    layout: { "line-join": "round", "line-cap": "round" },
                    paint: { "line-color": "#8a0e1e", "line-width": 4 }
                });
            }

            marcadores.forEach((marcador) => {
                new mapboxgl.Marker({ element: this.crearPunto() })
                    .setLngLat(marcador.coordenada)
                    .setPopup(new mapboxgl.Popup().setText(marcador.nombre))
                    .addTo(mapa);
            });

            this.encuadrar(mapa, linea.length > 0 ? linea : marcadores.map((m) => m.coordenada));
        });
    }

    // Crea el punto (marcador) que se ve en el mapa
    crearPunto() {
        const punto = document.createElement("div");
        punto.style.width = "14px";
        punto.style.height = "14px";
        punto.style.borderRadius = "50%";
        punto.style.backgroundColor = "#8a0e1e";
        punto.style.border = "2px solid #ffffff";
        return punto;
    }

    // Ajusta el mapa para que se vea toda la ruta
    encuadrar(mapa, puntos) {
        if (puntos.length === 0) {
            return;
        }
        const limites = new mapboxgl.LngLatBounds(puntos[0], puntos[0]);
        puntos.forEach((punto) => limites.extend(punto));
        mapa.fitBounds(limites, { padding: 30, maxZoom: 15 });
    }
}


// Lee el SVG y lo incrusta como gráfico
class Altimetria {

    constructor(urlSvg, figura) {
        this.urlSvg = urlSvg;
        this.figura = figura;
    }

    // Carga el archivo SVG de la ruta
    cargar() {
        $.ajax({ url: this.urlSvg, method: "GET", dataType: "text" })
            .done(this.insertar.bind(this))
            .fail(() => {
                $(this.figura).text("No se ha podido cargar la altimetría de la ruta.");
            });
    }

    // Mete el SVG dentro de la página
    insertar(textoSvg) {
        const documento = $.parseXML(textoSvg);
        this.figura.appendChild(document.importNode(documento.documentElement, true));
    }
}
