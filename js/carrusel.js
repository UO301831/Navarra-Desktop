// Clase Carrusel: muestra una a una las fotos locales de Navarra cada cierto tiempo.

class Carrusel {

    constructor() {
        this.fotos = [
            "multimedia/imagenes/01_carrusel_elizondo.jpg",
            "multimedia/imagenes/02_carrusel_olite.jpg",
            "multimedia/imagenes/03_carrusel_situacion_navarra.jpg",
            "multimedia/imagenes/04_carrusel_puente_reina.jpg",
            "multimedia/imagenes/05_carrusel_tradicion_navarra.jpg"
        ];

        this.descripciones = [
            "Elizondo, capital del Valle de Baztán",
            "Palacio Real de Olite, joya del gótico civil navarro",
            "Mapa de situación de Navarra dentro de España",
            "Puente románico de Puente la Reina sobre el río Arga",
            "Tradición popular navarra (Carnavales de Ituren y Zubieta)"
        ];

        this.actual = 0;
        this.segundos = 3000;
    }

    // Arranca el carrusel: muestra la primera foto y programa el cambio automático.
    iniciar() {
        this.mostrarFoto();
        setInterval(this.cambiarFoto.bind(this), this.segundos);
    }

    // Pinta la foto actual en el HTML (src, alt y pie de imagen).
    mostrarFoto() {
        var rutaFoto = this.fotos[this.actual];
        var textoFoto = this.descripciones[this.actual];

        $("figure > img").attr("src", rutaFoto);
        $("figure > img").attr("alt", textoFoto);
    }

    // Avanza al siguiente índice; vuelve al 0 al pasar del último.
    cambiarFoto() {
        this.actual = this.actual + 1;
        if (this.actual >= this.fotos.length) {
            this.actual = 0;
        }
        this.mostrarFoto();
    }
}
