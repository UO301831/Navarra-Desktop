// Clase Noticias: pide noticias al servicio web TheNewsAPI y las muestra en la sección de noticias.

class Noticias {

    constructor(busqueda) {
        this.busqueda = busqueda;
        this.url = "https://api.thenewsapi.com/v1/news/all";
        this.apiKey = "9ycDTSQOjnyyfkaGbWsO0x5DjE6ZqNJ29PNEMAKe"; 
    }

    // Hace la petición AJAX al servicio web y delega la respuesta al método correspondiente.
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

    // Pinta cada noticia recibida como un article dentro de la sección de noticias.
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

    // Muestra un mensaje de error si la petición al servicio web falla.
    mostrarError() {
        var seccion = $("main > section").last();
        seccion.empty();
        seccion.append("<h2>Noticias recientes sobre Navarra</h2>");
        seccion.append("<p>No se han podido cargar las noticias en este momento.</p>");
    }
}
