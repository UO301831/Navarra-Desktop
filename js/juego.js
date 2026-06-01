// Clase Juego: juego de 10 preguntas tipo test sobre Navarra Desktop.

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
                enunciado: "[Pregunta 4]",
                opciones: [
                    "[Opción A]",
                    "[Opción B]",
                    "[Opción C]",
                    "[Opción D]",
                    "[Opción E]"
                ],
                correcta: 0
            },
            {
                enunciado: "[Pregunta 5]",
                opciones: [
                    "[Opción A]",
                    "[Opción B]",
                    "[Opción C]",
                    "[Opción D]",
                    "[Opción E]"
                ],
                correcta: 0
            },
            {
                enunciado: "[Pregunta 6]",
                opciones: [
                    "[Opción A]",
                    "[Opción B]",
                    "[Opción C]",
                    "[Opción D]",
                    "[Opción E]"
                ],
                correcta: 0
            },
            {
                enunciado: "[Pregunta 7]",
                opciones: [
                    "[Opción A]",
                    "[Opción B]",
                    "[Opción C]",
                    "[Opción D]",
                    "[Opción E]"
                ],
                correcta: 0
            },
            {
                enunciado: "[Pregunta 8]",
                opciones: [
                    "[Opción A]",
                    "[Opción B]",
                    "[Opción C]",
                    "[Opción D]",
                    "[Opción E]"
                ],
                correcta: 0
            },
            {
                enunciado: "[Pregunta 9]",
                opciones: [
                    "[Opción A]",
                    "[Opción B]",
                    "[Opción C]",
                    "[Opción D]",
                    "[Opción E]"
                ],
                correcta: 0
            },
            {
                enunciado: "[Pregunta 10]",
                opciones: [
                    "[Opción A]",
                    "[Opción B]",
                    "[Opción C]",
                    "[Opción D]",
                    "[Opción E]"
                ],
                correcta: 0
            }
        ];

        this.preguntaActual = 0;
        this.aciertos = 0;
    }

    // Lanza el juego mostrando la primera pregunta.
    iniciar() {
        this.mostrarPregunta();
    }

    // Pinta en la sección la pregunta actual con sus 5 opciones como botones.
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

    // Comprueba la respuesta, suma acierto si toca y avanza a la siguiente pregunta.
    responder(indice) {
        if (indice === this.preguntas[this.preguntaActual].correcta) {
            this.aciertos = this.aciertos + 1;
        }

        this.preguntaActual = this.preguntaActual + 1;

        if (this.preguntaActual < this.preguntas.length) {
            this.mostrarPregunta();
        } else {
            this.mostrarPuntuacion();
        }
    }

    // Pinta la puntuación final del jugador (0 a 10).
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
    }
}
