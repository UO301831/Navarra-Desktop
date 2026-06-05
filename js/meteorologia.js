// Muestra el tiempo actual y la previsión de 7 días de una ciudad
class Meteorologia {

    constructor(latitud, longitud, ciudad) {
        this.latitud = latitud;
        this.longitud = longitud;
        this.ciudad = ciudad;
        this.urlBase = "https://api.open-meteo.com/v1/forecast";
    }

    // Pide al servicio web el tiempo de ahora mismo
    buscarTiempoActual() {
        $.ajax({
            url: this.urlBase,
            method: "GET",
            dataType: "json",
            data: {
                latitude: this.latitud,
                longitude: this.longitud,
                current_weather: true,
                timezone: "Europe/Madrid"
            }
        })
        .done(this.pintarTiempoActual.bind(this))
        .fail(this.mostrarErrorActual.bind(this));
    }

    // Pide al servicio web la previsión de los próximos días
    buscarPrevision() {
        $.ajax({
            url: this.urlBase,
            method: "GET",
            dataType: "json",
            data: {
                latitude: this.latitud,
                longitude: this.longitud,
                daily: "temperature_2m_max,temperature_2m_min,weathercode,sunrise,sunset,precipitation_sum,windspeed_10m_max",
                timezone: "Europe/Madrid",
                forecast_days: 8
            }
        })
        .done(this.pintarPrevision.bind(this))
        .fail(this.mostrarErrorPrevision.bind(this));
    }

    // Muestra el tiempo actual de la ciudad
    pintarTiempoActual(json) {
        var seccion = $("main > section:nth-of-type(2)");
        seccion.empty();
        seccion.append("<h2>Tiempo actual en " + this.ciudad + "</h2>");

        var tiempo = json.current_weather;
        var articulo = $("<article></article>");
        articulo.append("<h3>" + tiempo.time.replace("T", " ") + "</h3>");
        articulo.append("<p>Condiciones: " + this.descripcionTiempo(tiempo.weathercode) + "</p>");
        articulo.append("<p>Temperatura: " + tiempo.temperature + " °C</p>");
        articulo.append("<p>Viento: " + tiempo.windspeed + " km/h (dirección " + tiempo.winddirection + "°)</p>");
        seccion.append(articulo);
    }

    // Muestra la previsión día a día
    pintarPrevision(json) {
        var seccion = $("main > section:nth-of-type(3)");
        seccion.empty();
        seccion.append("<h2>Previsión para los próximos 7 días</h2>");

        // Empezamos en 1 para saltar el día de hoy, que ya se muestra arriba
        var dias = json.daily;
        for (var i = 1; i < dias.time.length; i++) {
            var articulo = $("<article></article>");
            articulo.append("<h3>" + dias.time[i] + "</h3>");
            articulo.append("<p>Condiciones: " + this.descripcionTiempo(dias.weathercode[i]) + "</p>");
            articulo.append("<p>Temperatura máxima: " + dias.temperature_2m_max[i] + " °C</p>");
            articulo.append("<p>Temperatura mínima: " + dias.temperature_2m_min[i] + " °C</p>");
            articulo.append("<p>Precipitación total: " + dias.precipitation_sum[i] + " mm</p>");
            articulo.append("<p>Viento máximo: " + dias.windspeed_10m_max[i] + " km/h</p>");
            articulo.append("<p>Salida del sol: " + dias.sunrise[i].split("T")[1] + "</p>");
            articulo.append("<p>Puesta del sol: " + dias.sunset[i].split("T")[1] + "</p>");
            seccion.append(articulo);
        }
    }

    // Avisa si falla la carga del tiempo actual
    mostrarErrorActual() {
        var seccion = $("main > section:nth-of-type(2)");
        seccion.empty();
        seccion.append("<h2>Tiempo actual</h2>");
        seccion.append("<p>No se ha podido cargar la información meteorológica.</p>");
    }

    // Avisa si falla la carga de la previsión
    mostrarErrorPrevision() {
        var seccion = $("main > section:nth-of-type(3)");
        seccion.empty();
        seccion.append("<h2>Previsión para los próximos 7 días</h2>");
        seccion.append("<p>No se ha podido cargar la previsión meteorológica.</p>");
    }

    // Convierte el código del tiempo en una palabra (Despejado, Lluvia...)
    descripcionTiempo(codigo) {
        if (codigo === 0) return "Despejado";
        if (codigo <= 3) return "Nublado";
        if (codigo <= 48) return "Niebla";
        if (codigo <= 67) return "Lluvia";
        if (codigo <= 77) return "Nieve";
        if (codigo <= 82) return "Chubascos";
        return "Tormenta";
    }
}
