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
                current: "temperature_2m,apparent_temperature,relative_humidity_2m,precipitation,weather_code,cloud_cover,surface_pressure,wind_speed_10m,wind_direction_10m,wind_gusts_10m,is_day",
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
                daily: "weather_code,temperature_2m_max,temperature_2m_min,apparent_temperature_max,apparent_temperature_min,precipitation_sum,precipitation_probability_max,wind_speed_10m_max,wind_gusts_10m_max,uv_index_max,sunrise,sunset",
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

        var tiempo = json.current;
        var articulo = $("<article></article>");
        articulo.append("<h3>" + this.formatearFecha(tiempo.time) + " " + tiempo.time.split("T")[1] + "</h3>");
        articulo.append("<p>Condiciones: " + this.descripcionTiempo(tiempo.weather_code) + "</p>");
        articulo.append("<p>Momento del día: " + (tiempo.is_day === 1 ? "Día" : "Noche") + "</p>");
        articulo.append("<p>Temperatura: " + tiempo.temperature_2m + " °C</p>");
        articulo.append("<p>Sensación térmica: " + tiempo.apparent_temperature + " °C</p>");
        articulo.append("<p>Humedad relativa: " + tiempo.relative_humidity_2m + " %</p>");
        articulo.append("<p>Precipitación: " + tiempo.precipitation + " mm</p>");
        articulo.append("<p>Nubosidad: " + tiempo.cloud_cover + " %</p>");
        articulo.append("<p>Presión atmosférica: " + tiempo.surface_pressure + " hPa</p>");
        articulo.append("<p>Viento: " + tiempo.wind_speed_10m + " km/h (" + this.direccionViento(tiempo.wind_direction_10m) + ", " + tiempo.wind_direction_10m + "°)</p>");
        articulo.append("<p>Rachas de viento: " + tiempo.wind_gusts_10m + " km/h</p>");
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
            articulo.append("<h3>" + this.formatearFecha(dias.time[i]) + "</h3>");
            articulo.append("<p>Condiciones: " + this.descripcionTiempo(dias.weather_code[i]) + "</p>");
            articulo.append("<p>Temperatura máxima: " + dias.temperature_2m_max[i] + " °C</p>");
            articulo.append("<p>Temperatura mínima: " + dias.temperature_2m_min[i] + " °C</p>");
            articulo.append("<p>Sensación térmica máxima: " + dias.apparent_temperature_max[i] + " °C</p>");
            articulo.append("<p>Sensación térmica mínima: " + dias.apparent_temperature_min[i] + " °C</p>");
            articulo.append("<p>Precipitación total: " + dias.precipitation_sum[i] + " mm</p>");
            articulo.append("<p>Probabilidad de precipitación: " + dias.precipitation_probability_max[i] + " %</p>");
            articulo.append("<p>Viento máximo: " + dias.wind_speed_10m_max[i] + " km/h</p>");
            articulo.append("<p>Rachas máximas: " + dias.wind_gusts_10m_max[i] + " km/h</p>");
            articulo.append("<p>Índice UV máximo: " + dias.uv_index_max[i] + "</p>");
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

    // Convierte una fecha ISO (YYYY-MM-DD...) al formato d/m/año
    formatearFecha(iso) {
        var partes = iso.split("T")[0].split("-");
        return partes[2] + "/" + partes[1] + "/" + partes[0];
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

    // Convierte los grados del viento en un punto cardinal (N, NE, E...)
    direccionViento(grados) {
        var puntos = ["N", "NE", "E", "SE", "S", "SO", "O", "NO"];
        return puntos[Math.round(grados / 45) % 8];
    }
}
