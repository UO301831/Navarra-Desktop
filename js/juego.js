// Juego de 10 preguntas tipo test sobre la web de Navarra
class Juego {

    constructor() {
        this.preguntas = [
            {
                enunciado: "¿Sobre qué provincia de España trata este sitio web?",
                opciones: ["Madrid", "Sevilla", "Asturias", "Navarra", "Valencia"],
                correcta: 3
            },
            {
                enunciado: "¿Qué capital de provincia se cita en la página de Meteorología?",
                opciones: ["Tudela", "Pamplona", "Lodosa", "Estella", "Olite"],
                correcta: 1
            },
            {
                enunciado: "¿Cuántas preguntas tiene este juego?",
                opciones: ["5", "7", "8", "12", "10"],
                correcta: 4
            },
            {
                enunciado: "¿Cuántos días de previsión meteorológica muestra la página de Meteorología?",
                opciones: ["8 días", "3 días", "5 días", "7 días", "14 días"],
                correcta: 0
            },
            {
                enunciado: "¿Qué embutido típico de Navarra aparece como imagen en la página de Gastronomía?",
                opciones: ["El fuet", "La sobrasada", "La chistorra", "La mortadela", "El salchichón"],
                correcta: 2
            },
            {
                enunciado: "¿De qué localidad navarra es el famoso pimiento del piquillo que aparece en Gastronomía?",
                opciones: ["Pamplona", "Tudela", "Roncal", "Estella", "Lodosa"],
                correcta: 4
            },
            {
                enunciado: "¿Qué queso navarro se menciona en la página de Gastronomía?",
                opciones: ["Queso manchego", "Queso de Roncal", "Queso de Burgos", "Queso de Cabrales", "Queso gallego"],
                correcta: 1
            },
            {
                enunciado: "¿Qué verdura típica de Navarra aparece en las recetas de la página de Gastronomía?",
                opciones: ["La zanahoria", "La lechuga", "El tomate", "El espárrago", "La cebolla"],
                correcta: 3
            },
            {
                enunciado: "¿Cuál de estas rutas turísticas aparece en la sección de Rutas?",
                opciones: ["Selva Negra", "Bosque de Sherwood", "Selva de Irati - Cascada del Cubo", "Parque del Retiro", "Selva del Amazonas"],
                correcta: 2
            },
            {
                enunciado: "¿Para qué sirve la página de Reservas del sitio web según la sección de Ayuda?",
                opciones: ["Comprar entradas de fútbol", "Reservar vuelos a Pamplona", "Reservar libros de la biblioteca", "Pedir comida a domicilio", "Reservar recursos turísticos de Navarra"],
                correcta: 4
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
