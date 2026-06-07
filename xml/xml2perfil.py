import xml.etree.ElementTree as ET
from pathlib import Path
import math

XML_PATH = Path(__file__).parent / "rutas.xml"


def calcular_intervalo(total, divisiones):
    """Devuelve un intervalo redondeado (1, 2, 2.5 o 5 x 10^n) para situar
    aproximadamente el numero de divisiones indicado en un eje."""
    if total <= 0:
        return 1.0
    aproximado = total / divisiones
    base = 10 ** math.floor(math.log10(aproximado))
    for multiplo in (1, 2, 2.5, 5):
        if base * multiplo >= aproximado:
            return base * multiplo
    return base * 10


class Svg(object):

    def __init__(self, width=800, height=400, viewBox=None, bg=None):
        ET.register_namespace("", "http://www.w3.org/2000/svg")
        self.raiz = ET.Element(
            "svg",
            {
                "xmlns": "http://www.w3.org/2000/svg",
                "width": str(width),
                "height": str(height),
                "viewBox": viewBox or f"0 0 {width} {height}",
            },
        )
        if bg:
            self.addRect(0, 0, "100%", "100%", fill=bg, strokeWidth=0, stroke="none")

    def addRect(self, x, y, width, height, fill="none", strokeWidth=1, stroke="#000"):
        ET.SubElement(
            self.raiz, "rect",
            x=str(x), y=str(y), width=str(width), height=str(height),
            fill=str(fill), stroke=str(stroke),
            **{"stroke-width": str(strokeWidth)}
        )

    def addLine(self, x1, y1, x2, y2, stroke="#000", strokeWidth=1, dash=None):
        attrs = {
            "x1": str(x1), "y1": str(y1),
            "x2": str(x2), "y2": str(y2),
            "stroke": str(stroke),
            "stroke-width": str(strokeWidth),
        }
        if dash:
            attrs["stroke-dasharray"] = dash
        ET.SubElement(self.raiz, "line", **attrs)

    def addPolyline(self, points, stroke="#000", strokeWidth=1, fill="none"):
        ET.SubElement(
            self.raiz, "polyline",
            points=str(points), fill=str(fill), stroke=str(stroke),
            **{"stroke-width": str(strokeWidth)}
        )

    def addPolygon(self, points, fill="#ccc", opacity=1.0, stroke="none"):
        ET.SubElement(
            self.raiz, "polygon",
            points=str(points), fill=str(fill),
            **{"fill-opacity": str(opacity), "stroke": str(stroke)}
        )

    def addCircle(self, cx, cy, r, fill="none", strokeWidth=1, stroke="#000"):
        ET.SubElement(
            self.raiz, "circle",
            cx=str(cx), cy=str(cy), r=str(r),
            fill=str(fill), stroke=str(stroke),
            **{"stroke-width": str(strokeWidth)}
        )

    def addText(self, texto, x, y, fontFamily="Verdana", fontSize=12, extra_style="", transform=""):
        attrs = {
            "x": str(x), "y": str(y),
            "font-family": fontFamily,
            "font-size": str(fontSize),
        }
        if extra_style:
            attrs["style"] = extra_style
        if transform:
            attrs["transform"] = transform
        t = ET.SubElement(self.raiz, "text", **attrs)
        t.text = str(texto)

    def escribir(self, nombreArchivoSVG):
        arbol = ET.ElementTree(self.raiz)
        try:
            ET.indent(arbol)
        except Exception:
            pass
        arbol.write(nombreArchivoSVG, encoding="utf-8", xml_declaration=True)


def cargar_perfil(ruta_el):
    nombre = (ruta_el.findtext("nombre") or "Ruta").strip()
    perfil = []
    dist_acum = 0.0

    for hito in ruta_el.findall("hitos/hito"):
        alt_txt = hito.findtext("coordenadas/altitud") or "0"
        dist_el = hito.find("distancia")
        try:
            alt = float(alt_txt.strip())
        except ValueError:
            alt = 0.0
        dist = 0.0
        if dist_el is not None:
            try:
                dist = float((dist_el.text or "0").strip())
            except ValueError:
                dist = 0.0
            if dist_el.get("unidades", "m") == "km":
                dist *= 1000.0
        dist_acum += dist
        nombre_hito = (hito.findtext("nombre") or "").strip()
        perfil.append((dist_acum, alt, nombre_hito))

    return nombre, perfil


def generar_altimetria_svg(nombre, perfil, out_path):
    W, H = 1000, 380
    ML, MR, MT, MB = 70, 30, 30, 50
    cw, ch = W - ML - MR, H - MT - MB

    xs = [x for x, _, _ in perfil]
    ys = [y for _, y, _ in perfil]
    x_min, x_max = 0.0, max(xs)
    y_min, y_max = min(ys), max(ys)
    if abs(y_max - y_min) < 1e-6:
        y_min -= 1
        y_max += 1

    def sx(x): return ML + (x - x_min) / (x_max - x_min) * cw
    def sy(y): return MT + ch - (y - y_min) / (y_max - y_min) * ch
    pts = [(sx(x), sy(y)) for x, y, _ in perfil]

    base_y = sy(y_min)
    poly = [(sx(x_min), base_y)] + pts + [(sx(x_max), base_y)]

    svg = Svg(W, H, bg="#ffffff")

    svg.addText(f"Altimetria — {nombre}", W / 2, 18,
                fontSize=16, extra_style="text-anchor: middle;")

    svg.addLine(ML, MT + ch, ML + cw, MT + ch, stroke="#333", strokeWidth=1.5)
    svg.addLine(ML, MT, ML, MT + ch, stroke="#333", strokeWidth=1.5)

    paso_x = calcular_intervalo(x_max, 20)
    n_x = int(math.floor(x_max / paso_x))
    for i in range(n_x + 1):
        m = i * paso_x
        x = sx(m)
        svg.addLine(x, MT, x, MT + ch, stroke="#e6e6e6", strokeWidth=1)
        svg.addText(f"{m / 1000:.1f}", x, MT + ch + 18, fontSize=11,
                    extra_style="text-anchor: middle; fill: #444;")
    svg.addText("Distancia (km)", ML + cw / 2, H - 12,
                fontSize=12, extra_style="text-anchor: middle;")

    rango = y_max - y_min
    if rango <= 25:
        paso_y = 5
    elif rango <= 60:
        paso_y = 10
    elif rango <= 120:
        paso_y = 20
    elif rango <= 250:
        paso_y = 50
    else:
        paso_y = 100
    y_tick = math.floor(y_min / paso_y) * paso_y
    while y_tick <= y_max + 1e-9:
        y = sy(y_tick)
        svg.addLine(ML, y, ML + cw, y, stroke="#efefef", strokeWidth=1)
        svg.addText(f"{y_tick:.0f}", ML - 8, y + 4, fontSize=11,
                    extra_style="text-anchor: end; fill: #444;")
        y_tick += paso_y
    svg.addText("Altitud (m)", 16, MT + ch / 2, fontSize=12,
                extra_style="text-anchor: middle;",
                transform=f"rotate(-90,16,{MT + ch / 2})")

    # Poli-linea cerrada: perfil + base, repitiendo el primer punto para cerrarla
    poly_cerrada = poly + [poly[0]]
    svg.addPolyline(" ".join(f"{x:.2f},{y:.2f}" for x, y in poly_cerrada),
                    stroke="none", strokeWidth=0, fill="#cfe8ff")
    svg.addPolyline(" ".join(f"{x:.2f},{y:.2f}" for x, y in pts),
                    stroke="#0066ff", strokeWidth=2.5, fill="none")

    # Circulo gris pequeño en todos los hitos (anonimos y nombrados)
    for px, py in pts:
        svg.addCircle(f"{px:.2f}", f"{py:.2f}", 3,
                      fill="#888888", stroke="none", strokeWidth=0)

    # Circulo azul mayor y etiqueta para los hitos con nombre
    label_idx = 0
    for i, (_, _, nombre_hito) in enumerate(perfil):
        if not nombre_hito:
            continue
        px, py = pts[i]
        svg.addCircle(f"{px:.2f}", f"{py:.2f}", 5,
                      fill="#ffffff", stroke="#0044cc", strokeWidth=1.5)
        if label_idx % 2 == 0:
            # Etiqueta horizontal: a la derecha y ligeramente arriba del punto
            svg.addText(nombre_hito, f"{px + 6:.1f}", f"{py - 6:.1f}",
                        fontSize=9, extra_style="fill: #222;")
        else:
            # Etiqueta vertical: rotada -90° alrededor del punto
            svg.addText(nombre_hito, f"{px:.1f}", f"{py - 6:.1f}",
                        fontSize=9, extra_style="fill: #222;",
                        transform=f"rotate(-90,{px:.1f},{py:.1f})")
        label_idx += 1

    svg.addText(f"Longitud: {x_max / 1000:.3f} km", W - MR, 36,
                fontSize=12, extra_style="text-anchor: end;")
    svg.addText(f"Altitud: {y_min:.0f}–{y_max:.0f} m", W - MR, 54,
                fontSize=12, extra_style="text-anchor: end;")

    svg.escribir(str(out_path))
    return out_path


def main():
    if not XML_PATH.exists():
        raise SystemExit(f"No existe el XML:\n{XML_PATH}")

    root = ET.parse(str(XML_PATH)).getroot()
    for el in root.iter(): el.tag = el.tag.split("}")[-1]

    for ruta_el in root.findall("ruta"):
        nombre, perfil = cargar_perfil(ruta_el)
        alt_rel = (ruta_el.findtext("altimetria") or "altimetria.svg").strip()
        out_path = Path(__file__).parent / Path(alt_rel).name
        out = generar_altimetria_svg(nombre, perfil, out_path)
        print(f"SVG creado en:\n{out}")


if __name__ == "__main__":
    main()
