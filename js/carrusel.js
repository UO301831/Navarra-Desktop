// Carrusel que va mostrando las fotos de Navarra una a una
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
            "Imagen de Elizondo, capital del Valle de Baztán",
            "Imagen del Palacio Real de Olite, joya del gótico civil navarro",
            "Imagen del mapa de situación de Navarra dentro de España",
            "Imagen del Puente románico de Puente la Reina sobre el río Arga",
            "Imagen de la tradición popular navarra (Carnavales de Ituren y Zubieta)"
        ];

        this.actual = 0;
        this.segundos = 3000;
    }

    // Muestra la primera foto y va cambiándola cada cierto tiempo
    iniciar() {
        this.mostrarFoto();
        setInterval(this.cambiarFoto.bind(this), this.segundos);
    }

    // Pone la foto actual en la imagen de la página
    mostrarFoto() {
        var rutaFoto = this.fotos[this.actual];
        var textoFoto = this.descripciones[this.actual];
        var imagen = $("figure > img");

        imagen.attr("src", rutaFoto);
        imagen.attr("alt", textoFoto);
    }

    // Pasa a la siguiente foto y vuelve al principio al llegar al final
    cambiarFoto() {
        this.actual = this.actual + 1;
        if (this.actual >= this.fotos.length) {
            this.actual = 0;
        }
        this.mostrarFoto();
    }
}
