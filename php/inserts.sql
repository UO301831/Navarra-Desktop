-- inserts.sql - Datos iniciales para la central de reservas
USE UO301831_DB;

INSERT INTO tipo_recurso (nombre) VALUES
 ('Museo'),
 ('Ruta'),
 ('Restaurante'),
 ('Hotel'),
 ('Actividad');

INSERT INTO localidad (nombre) VALUES
 ('Pamplona'),
 ('Olite'),
 ('Javier'),
 ('Estella-Lizarra'),
 ('Ochagavía'),
 ('Zugarramurdi');

INSERT INTO recurso (nombre, id_tipo, id_localidad, plazas, fecha_inicio, fecha_fin, precio, descripcion) VALUES
 ('Museo de Navarra', 1, 1, 50, '2026-07-15 09:30:00', '2026-07-15 14:00:00', 2.00,
  'Antiguo Hospital de Nuestra Señora de la Misericordia. Colección que abarca de la Prehistoria al siglo XXI: mosaicos romanos, pinturas góticas, la Mano de Irulegi y un retrato del Marqués de San Adrián de Goya.'),
 ('Museo de la Catedral de Pamplona', 1, 1, 25, '2026-07-16 10:30:00', '2026-07-16 19:00:00', 5.00,
  'Recorrido por el arte sacro y el patrimonio de la Catedral de Santa María la Real. Incluye visita guiada en español y posibilidad de subir a la campana María.'),
 ('Museo del Carlismo', 1, 4, 30, '2026-07-18 10:00:00', '2026-07-18 19:00:00', 2.00,
  'Ubicado en Estella-Lizarra, en el antiguo palacio del Gobernador. Refleja la historia del carlismo en Navarra y España durante los siglos XIX y XX como proceso político y social.'),
 ('Palacio Real de Olite', 5, 2, 100, '2026-07-20 10:00:00', '2026-07-20 20:00:00', 4.40,
  'Castillo gótico del siglo XV mandado construir por Carlos III el Noble y Leonor de Trastámara. Uno de los conjuntos palaciegos medievales mejor conservados de Europa, con seis torres y murallas visitables.'),
 ('Castillo de Javier', 5, 3, 80, '2026-07-22 10:00:00', '2026-07-22 19:00:00', 4.30,
  'Fortaleza medieval de finales del siglo X, lugar de nacimiento de San Francisco Javier en 1506, patrón de Navarra. Incluye torres, mazmorras, puente levadizo y un museo con recorrido de dioramas sobre la vida del santo.'),
 ('Visita Bodegas Marco Real con cata', 5, 2, 20, '2026-07-23 11:00:00', '2026-07-23 12:30:00', 18.00,
  'Visita guiada de 90 minutos por las bodegas de la capital del vino de Navarra. Incluye Sala de los Aromas con más de 40 tipos de aromas del vino y cata de 3 vinos acompañados del aceite Belasco.'),
 ('Ruta Nacedero del Urederra', 2, 1, 500, '2026-07-25 08:00:00', '2026-07-25 18:00:00', 3.00,
  'Sendero en el Parque Natural de Urbasa-Andía hasta uno de los nacederos más espectaculares de España, con aguas turquesa, cascadas y pozas. Acceso limitado por ley a 500 personas por día con reserva previa obligatoria.'),
 ('Visita guiada Cueva y Museo de las Brujas', 5, 6, 40, '2026-07-26 11:00:00', '2026-07-26 14:00:00', 4.50,
  'Recorrido por la cueva de Zugarramurdi y el museo que explica el proceso inquisitorial de 1610 contra las brujas de la zona. Pueblo fronterizo con Francia de apenas 300 habitantes.'),
 ('Restaurante El Burladero', 3, 1, 100, '2026-08-01 13:30:00', '2026-08-01 16:00:00', 35.00,
  'Restaurante de cocina navarra tradicional frente a la Plaza de Toros de Pamplona. Verduras de temporada, carnes de pastos cercanos y pescados del Cantábrico. Comedor con capacidad para 100 comensales.'),
 ('Gran Hotel La Perla', 4, 1, 44, '2026-08-05 14:00:00', '2026-08-06 12:00:00', 180.00,
  'Hotel histórico de cinco estrellas en plena Plaza del Castillo de Pamplona. Habitaciones donde se alojaron Hemingway, Chaplin u Orson Welles. Precio por noche y habitación doble estándar.');
