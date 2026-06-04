import xml.etree.ElementTree as ET
from pathlib import Path

XML_ENTRADA = Path(__file__).parent / "rutas.xml"


class ConstructorKml:
    """Construye un documento KML en memoria y lo escribe en disco."""

    KML_NS = "http://www.opengis.net/kml/2.2"

    def __init__(self, titulo: str):
        self._raiz = ET.Element("kml", xmlns=self.KML_NS)
        self._documento = ET.SubElement(self._raiz, "Document")
        ET.SubElement(self._documento, "name").text = titulo

    def agregar_marcador(self, etiqueta: str, lon: str, lat: str, alt: str = "0") -> None:
        marca = ET.SubElement(self._documento, "Placemark")
        ET.SubElement(marca, "name").text = etiqueta
        punto = ET.SubElement(marca, "Point")
        ET.SubElement(punto, "coordinates").text = f"{lon},{lat},{alt}"

    def agregar_trazado(self, etiqueta: str, lista_coords: list,
                        color: str = "ff1400e6", grosor: str = "4") -> None:
        marca = ET.SubElement(self._documento, "Placemark")
        ET.SubElement(marca, "name").text = etiqueta
        estilo = ET.SubElement(marca, "Style")
        linea = ET.SubElement(estilo, "LineStyle")
        ET.SubElement(linea, "color").text = color
        ET.SubElement(linea, "width").text = grosor
        trayecto = ET.SubElement(marca, "LineString")
        ET.SubElement(trayecto, "tessellate").text = "1"
        ET.SubElement(trayecto, "altitudeMode").text = "relativeToGround"
        ET.SubElement(trayecto, "coordinates").text = "\n".join(lista_coords)

    def guardar(self, destino: Path) -> None:
        arbol = ET.ElementTree(self._raiz)
        try:
            ET.indent(arbol, space="  ")
        except AttributeError:
            pass
        arbol.write(str(destino), encoding="utf-8", xml_declaration=True)


class LectorRutas:
    """Parsea rutas.xml y expone los datos de cada ruta."""

    def __init__(self, ruta_xml: Path):
        if not ruta_xml.exists():
            raise SystemExit(f"No se encontro el archivo: {ruta_xml}")
        self._raiz = ET.parse(str(ruta_xml)).getroot()
        for el in self._raiz.iter(): el.tag = el.tag.split("}")[-1]

    def rutas(self) -> list:
        return [self._extraer_ruta(r) for r in self._raiz.findall("ruta")]

    def _extraer_ruta(self, nodo) -> dict:
        return {
            "nombre":      (nodo.findtext("nombre") or "Ruta sin nombre").strip(),
            "planimetria": (nodo.findtext("planimetria") or "").strip(),
            "inicio":      self._leer_coords(nodo.find("inicio/coordenadas")),
            "hitos":       self._leer_hitos(nodo),
        }

    def _leer_coords(self, nodo) -> dict:
        if nodo is None:
            return {}
        return {
            "lon": (nodo.findtext("longitud") or "").strip(),
            "lat": (nodo.findtext("latitud")  or "").strip(),
            "alt": (nodo.findtext("altitud")  or "0").strip(),
        }

    def _leer_hitos(self, nodo_ruta) -> list:
        resultado = []
        for hito in nodo_ruta.findall("hitos/hito"):
            coords = self._leer_coords(hito.find("coordenadas"))
            if coords.get("lon") and coords.get("lat"):
                resultado.append({
                    "nombre": (hito.findtext("nombre") or "").strip(),
                    **coords,
                })
        return resultado


class GeneradorPlanimetria:
    """Orquesta la lectura del XML y la creacion de un KML por ruta."""

    def __init__(self, ruta_xml: Path):
        self._lector = LectorRutas(ruta_xml)
        self._directorio = ruta_xml.parent

    def generar(self) -> None:
        for ruta in self._lector.rutas():
            if not ruta["planimetria"]:
                print(f"  Sin planimetria definida: {ruta['nombre']}")
                continue
            self._procesar_ruta(ruta)

    def _procesar_ruta(self, ruta: dict) -> None:
        kml = ConstructorKml(ruta["nombre"])

        inicio = ruta["inicio"]
        if inicio.get("lon") and inicio.get("lat"):
            kml.agregar_marcador(
                "Inicio de la ruta",
                inicio["lon"], inicio["lat"], inicio.get("alt", "0")
            )

        lista_coords = self._construir_lista_coords(inicio, ruta["hitos"])

        for hito in ruta["hitos"]:
            if hito["nombre"]:
                kml.agregar_marcador(
                    hito["nombre"],
                    hito["lon"], hito["lat"], hito.get("alt", "0")
                )

        if lista_coords:
            kml.agregar_trazado(ruta["nombre"], lista_coords)

        destino = self._directorio / Path(ruta["planimetria"]).name
        kml.guardar(destino)
        print(f"KML generado: {destino}")

    def _construir_lista_coords(self, inicio: dict, hitos: list) -> list:
        coords = []
        if inicio.get("lon") and inicio.get("lat"):
            coords.append(f"{inicio['lon']},{inicio['lat']},{inicio.get('alt','0')}")
        for hito in hitos:
            coords.append(f"{hito['lon']},{hito['lat']},{hito.get('alt','0')}")
        return coords


if __name__ == "__main__":
    GeneradorPlanimetria(XML_ENTRADA).generar()
