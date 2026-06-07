// Juego de 10 preguntas tipo test sobre la web de Navarra
class Juego {

    constructor() {
        this.preguntas = [
            {
                enunciado: "¿Cuántos días de previsión meteorológica muestra la página de Meteorología?",
                opciones: ["3 días", "5 días", "7 días", "10 días", "14 días"],
                correcta: 2
            },
            {
                enunciado: "¿Qué función ofrece la página de Reservas según la sección de Ayuda?",
                opciones: ["Comprar entradas de fútbol", "Reservar vuelos a Pamplona", "Gestionar reservas de recursos turísticos", "Reservar libros de la biblioteca", "Reservar mesas en restaurantes únicamente"],
                correcta: 2
            },
            {
                enunciado: "¿Cuál de estas recetas con Piquillo de Lodosa aparece en la tabla de gastronomía?",
                opciones: ["Croquetas de pollo y pimientos del Piquillo de Lodosa", "Tortilla de patata con Piquillo", "Pizza de Piquillo", "Empanada de Piquillo", "Hamburguesa de Piquillo"],
                correcta: 0
            },
            {
                enunciado: "¿Qué embutido típico de Navarra es un chorizo de calibre más fino de lo habitual?",
                opciones: ["Fuet", "Chistorra", "Mortadela", "Sobrasada", "Salchichón"],
                correcta: 1
            },
            {
                enunciado: "¿De qué localidad navarra es el famoso pimiento del piquillo con Denominación de Origen?",
                opciones: ["Pamplona", "Olite", "Lodosa", "Tudela", "Estella"],
                correcta: 2
            },
            {
                enunciado: "¿Cuál de estos quesos navarros se menciona en la sección de Gastronomía?",
                opciones: ["Queso manchego", "Queso de Burgos", "Queso de Cabrales", "Queso de Roncal", "Queso gallego"],
                correcta: 3
            },
            {
                enunciado: "¿Qué medio de transporte se utiliza en la ruta de las Bardenas Reales?",
                opciones: ["Bicicleta", "Avión", "Tren", "Barco", "Patines"],
                correcta: 0
            },
            {
                enunciado: "¿Cómo se llama la formación geológica más emblemática de las Bardenas Reales que aparece como hito de la ruta?",
                opciones: ["El Teide", "La Giralda", "El Naranjo de Bulnes", "La Concha", "Castildetierra"],
                correcta: 4
            },
            {
                enunciado: "Según la página de Ayuda, ¿en qué formato se representa la planimetría de cada ruta sobre el mapa?",
                opciones: ["PDF", "KML", "MP3", "DOCX", "ZIP"],
                correcta: 1
            },
            {
                enunciado: "Según la página de Ayuda, ¿qué muestra la página de Inicio?",
                opciones: ["Un mapa del metro", "Una calculadora", "Un carrusel de imágenes y noticias de Navarra", "Un reproductor de música", "Un foro de mensajes"],
                correcta: 2
            }
        ];

        // Recupera el progreso guardado para mantener el estado al cambiar de página
        var estado = JSON.parse(localStorage.getItem("juegoNavarra"));
        this.preguntaActual = estado ? estado.preguntaActual : 0;
        this.aciertos = estado ? estado.aciertos : 0;
    }

    // Guarda el estado actual del juego en el navegador
    guardarEstado() {
        localStorage.setItem("juegoNavarra", JSON.stringify({
            preguntaActual: this.preguntaActual,
            aciertos: this.aciertos
        }));
    }

    // Empieza el juego mostrando la pregunta donde se quedó (o la final si ya terminó)
    iniciar() {
        if (this.preguntaActual < this.preguntas.length) {
            this.mostrarPregunta();
        } else {
            this.mostrarPuntuacion();
        }
    }

    // Muestra la pregunta actual con sus opciones como botones
    mostrarPregunta() {
        var seccion = document.querySelector("main > section:nth-of-type(2)");
        seccion.innerHTML = "";

        var pregunta = this.preguntas[this.preguntaActual];

        var titulo = document.createElement("h3");
        titulo.textContent = "Pregunta " + (this.preguntaActual + 1) + " de " + this.preguntas.length;
        seccion.appendChild(titulo);

        var enunciado = document.createElement("p");
        enunciado.textContent = pregunta.enunciado;
        seccion.appendChild(enunciado);

        var lista = document.createElement("ol");
        for (let i = 0; i < pregunta.opciones.length; i++) {
            var item = document.createElement("li");
            var boton = document.createElement("button");
            boton.textContent = pregunta.opciones[i];
            boton.addEventListener("click", () => this.responder(i));
            item.appendChild(boton);
            lista.appendChild(item);
        }
        seccion.appendChild(lista);
    }

    // Comprueba la respuesta y pasa a la siguiente pregunta
    responder(indice) {
        if (indice === this.preguntas[this.preguntaActual].correcta) {
            this.aciertos = this.aciertos + 1;
        }

        this.preguntaActual = this.preguntaActual + 1;
        this.guardarEstado();

        if (this.preguntaActual < this.preguntas.length) {
            this.mostrarPregunta();
        } else {
            this.mostrarPuntuacion();
        }
    }

    // Muestra la nota final del jugador (0 a 10)
    mostrarPuntuacion() {
        var seccion = document.querySelector("main > section:nth-of-type(2)");
        seccion.innerHTML = "";

        var titulo = document.createElement("h3");
        titulo.textContent = "Resultado final";
        seccion.appendChild(titulo);

        var resumen = document.createElement("p");
        resumen.textContent = "Has acertado " + this.aciertos + " de " + this.preguntas.length + " preguntas.";
        seccion.appendChild(resumen);

        var nota = document.createElement("p");
        nota.textContent = "Tu calificación es: " + this.aciertos + " / 10.";
        seccion.appendChild(nota);

        var reiniciar = document.createElement("button");
        reiniciar.textContent = "Volver a jugar";
        reiniciar.addEventListener("click", () => {
            localStorage.removeItem("juegoNavarra");
            this.preguntaActual = 0;
            this.aciertos = 0;
            this.mostrarPregunta();
        });
        seccion.appendChild(reiniciar);
    }
}
