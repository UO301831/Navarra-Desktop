// Pide noticias de Navarra a un servicio web y las muestra
class Noticias {

    constructor(busqueda) {
        this.busqueda = busqueda;
        this.url = "https://api.thenewsapi.com/v1/news/all";
        this.apiKey = "9ycDTSQOjnyyfkaGbWsO0x5DjE6ZqNJ29PNEMAKe";
    }

    // Pide las noticias al servicio web
    buscar() {
        $.ajax({
            url: this.url,
            method: "GET",
            dataType: "json",
            data: {
                api_token: this.apiKey,
                search: this.busqueda,
                language: "es"
            }
        })
        .done(this.procesarNoticias.bind(this))
        .fail(this.mostrarError.bind(this));
    }

    // Muestra en la página cada noticia recibida
    procesarNoticias(json) {
        var seccion = $("main > section").last();
        seccion.empty();
        seccion.append("<h2>Noticias recientes sobre Navarra</h2>");

        if (!json.data || json.data.length === 0) {
            seccion.append("<p>No se han encontrado noticias sobre Navarra.</p>");
            return;
        }

        for (var i = 0; i < json.data.length; i++) {
            var noticia = json.data[i];
            var articulo = $("<article></article>");
            articulo.append("<h3>" + noticia.title + "</h3>");
            articulo.append("<p>" + (noticia.description || "Sin descripción disponible.") + "</p>");
            articulo.append("<p><a href='" + noticia.url + "'>Leer noticia completa</a></p>");
            articulo.append("<p>Fuente: " + noticia.source + "</p>");
            seccion.append(articulo);
        }
    }

    // Avisa si las noticias no se han podido cargar
    mostrarError() {
        var seccion = $("main > section").last();
        seccion.empty();
        seccion.append("<h2>Noticias recientes sobre Navarra</h2>");
        seccion.append("<p>No se han podido cargar las noticias en este momento.</p>");
    }
}
