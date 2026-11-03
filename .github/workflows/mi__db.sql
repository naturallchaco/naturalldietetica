-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         11.5.2-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para mi__db
CREATE DATABASE IF NOT EXISTS `mi__db` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */;
USE `mi__db`;

-- Volcando estructura para tabla mi__db.carrito
CREATE TABLE IF NOT EXISTS `carrito` (
  `id_carrito` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_carrito`),
  KEY `fk_carrito_usuario` (`id_usuario`),
  KEY `fk_carrito_producto` (`id_producto`),
  CONSTRAINT `fk_carrito_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_carrito_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Volcando datos para la tabla mi__db.carrito: ~0 rows (aproximadamente)

-- Volcando estructura para tabla mi__db.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(50) NOT NULL,
  PRIMARY KEY (`id_categoria`),
  UNIQUE KEY `nombre_categoria` (`nombre_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Volcando datos para la tabla mi__db.categorias: ~8 rows (aproximadamente)
INSERT INTO `categorias` (`id_categoria`, `nombre_categoria`) VALUES
	(1, 'granel'),
	(2, 'bebidas'),
	(3, 'cero_azucares'),
	(4, 'congelados'),
	(5, 'lacteos'),
	(6, 'panificados'),
	(7, 'sin_tacc'),
	(8, 'veganos');

-- Volcando estructura para tabla mi__db.detalle_pedido
CREATE TABLE IF NOT EXISTS `detalle_pedido` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `fk_detalle_pedido` (`id_pedido`),
  KEY `fk_detalle_producto` (`id_producto`),
  CONSTRAINT `fk_detalle_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Volcando datos para la tabla mi__db.detalle_pedido: ~1 rows (aproximadamente)
INSERT INTO `detalle_pedido` (`id_detalle`, `id_pedido`, `id_producto`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
	(1, 13, 24, 2, 720.00, 1440.00),
	(2, 14, 162, 3, 2900.00, 8700.00);

-- Volcando estructura para tabla mi__db.empleados
CREATE TABLE IF NOT EXISTS `empleados` (
  `id_empleado` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) DEFAULT NULL,
  `horario` varchar(255) DEFAULT NULL,
  `descanso` varchar(50) DEFAULT NULL,
  `vacaciones` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_empleado`),
  UNIQUE KEY `Índice 2` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Volcando datos para la tabla mi__db.empleados: 2 rows
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
INSERT INTO `empleados` (`id_empleado`, `email`, `horario`, `descanso`, `vacaciones`) VALUES
	(1, 'fr069864@gmail.com', '12-19', '15-16', 'sin reservar'),
	(2, 'cami@gmail.com', '8-12', '10-11', 'sin reservar');
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;

-- Volcando estructura para tabla mi__db.estados_pedido
CREATE TABLE IF NOT EXISTS `estados_pedido` (
  `id_estado` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_estado` varchar(50) NOT NULL,
  PRIMARY KEY (`id_estado`),
  UNIQUE KEY `nombre_estado` (`nombre_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Volcando datos para la tabla mi__db.estados_pedido: ~6 rows (aproximadamente)
INSERT INTO `estados_pedido` (`id_estado`, `nombre_estado`) VALUES
	(1, 'Pendiente'),
	(2, 'Pago Confirmado'),
	(3, 'En Preparación'),
	(4, 'Enviado'),
	(5, 'Entregado'),
	(6, 'Cancelado');

-- Volcando estructura para tabla mi__db.ofertas
CREATE TABLE IF NOT EXISTS `ofertas` (
  `id_oferta` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(11) NOT NULL,
  `descuento_porcentaje` decimal(5,2) DEFAULT NULL,
  `precio_oferta` decimal(10,2) DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  PRIMARY KEY (`id_oferta`),
  UNIQUE KEY `id_producto` (`id_producto`),
  CONSTRAINT `fk_oferta_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Volcando datos para la tabla mi__db.ofertas: ~0 rows (aproximadamente)

-- Volcando estructura para tabla mi__db.pedidos
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `cliente_nombre` varchar(255) DEFAULT NULL,
  `cliente_email` varchar(255) DEFAULT NULL,
  `cliente_telefono` varchar(50) DEFAULT NULL,
  `id_estado` int(11) NOT NULL DEFAULT 1,
  `fecha_pedido` datetime NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `direccion_envio` varchar(255) NOT NULL,
  PRIMARY KEY (`id_pedido`),
  KEY `fk_pedido_usuario` (`id_usuario`),
  KEY `fk_pedido_estado` (`id_estado`),
  CONSTRAINT `fk_pedido_estado` FOREIGN KEY (`id_estado`) REFERENCES `estados_pedido` (`id_estado`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_pedido_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Volcando datos para la tabla mi__db.pedidos: ~1 rows (aproximadamente)
INSERT INTO `pedidos` (`id_pedido`, `id_usuario`, `cliente_nombre`, `cliente_email`, `cliente_telefono`, `id_estado`, `fecha_pedido`, `total`, `metodo_pago`, `direccion_envio`) VALUES
	(13, NULL, 'cam', 'camila@gmail.com', '72728181', 4, '2025-10-29 11:08:51', 1440.00, 'Efectivo', '123, resistencia,chaco'),
	(14, 2, 'camila', 'cami@gmail.com', '5764', 5, '2025-10-29 15:27:32', 8700.00, 'Efectivo', 'cgvbnnbv, 45yhj');

-- Volcando estructura para tabla mi__db.productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `id_proveedor` int(11) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `es_oferta` tinyint(1) NOT NULL DEFAULT 0,
  `descuento_porcentaje` decimal(5,2) DEFAULT 0.00,
  PRIMARY KEY (`id_producto`),
  KEY `fk_producto_proveedor` (`id_proveedor`),
  KEY `fk_producto_categoria` (`id_categoria`),
  CONSTRAINT `fk_producto_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_producto_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Volcando datos para la tabla mi__db.productos: ~162 rows (aproximadamente)
INSERT INTO `productos` (`id_producto`, `id_proveedor`, `id_categoria`, `nombre`, `descripcion`, `precio_unitario`, `stock`, `imagen_url`, `es_oferta`, `descuento_porcentaje`) VALUES
	(1, 2, 7, 'galletas de arroz', NULL, 250.00, 10, 'galletas_arroz.jpg', 1, 0.00),
	(2, 1, 3, 'Coca zero', NULL, 250.00, 10, 'coca_zero.jpg', 1, 0.00),
	(3, 1, 1, 'Lentejas Rojas Orgánicas (Kg)', NULL, 850.00, 0, 'lentejas_rojas.jpg', 0, 0.00),
	(4, 2, 1, 'Harina de Almendras Fina (Kg)', NULL, 720.00, 5, 'harina_almendras.jpg', 0, 0.00),
	(5, 1, 1, 'Semillas de Chía Premium (250g)', NULL, 720.00, 30, 'semillas_chia.jpg', 0, 0.00),
	(6, 2, 1, 'Mix de Frutos Secos Energético (Kg)', NULL, 610.00, 30, 'mix_frutos_secos.jpg', 0, 0.00),
	(7, 1, 1, 'Avena Instantánea Sin TACC (Kg)', NULL, 850.00, 30, 'avena_sintacc.jpg', 0, 0.00),
	(8, 2, 1, 'Azúcar Mascabo (Kg)', NULL, 720.00, 0, 'azucar_mascabo.jpg', 0, 0.00),
	(9, 1, 1, 'Cacao Amargo Puro (200g)', NULL, 720.00, 30, 'cacao_amargo.jpg', 0, 0.00),
	(10, 2, 1, 'Café de Grano Tostado Natural (Kg)', NULL, 720.00, 30, 'cafe_grano.jpg', 0, 0.00),
	(11, 1, 1, 'Pasas de Uva Rubias (500g)', NULL, 850.00, 5, 'pasas_rubias.jpg', 0, 0.00),
	(12, 2, 1, 'Pimienta Negra en Grano (100g)', NULL, 720.00, 30, 'pimienta_negra.jpg', 0, 0.00),
	(13, 1, 1, 'Arroz Integral Largo Fino (Kg)', NULL, 720.00, 0, 'arroz_integral.jpg', 0, 0.00),
	(14, 2, 1, 'Garbanzos Secos (Kg)', NULL, 720.00, 30, 'garbanzos.jpg', 0, 0.00),
	(15, 1, 1, 'Sal Rosa del Himalaya (500g)', NULL, 850.00, 30, 'sal_rosa.jpg', 0, 0.00),
	(16, 2, 1, 'Harina Integral de Espelta (Kg)', NULL, 610.00, 30, 'harina_espelta.jpg', 0, 0.00),
	(17, 1, 1, 'Almendras Laminadas (250g)', NULL, 720.00, 30, 'almendras_laminadas.jpg', 0, 0.00),
	(18, 2, 1, 'Polvo de Hornear Sin Aluminio (100g)', NULL, 720.00, 0, 'polvo_hornear.jpg', 0, 0.00),
	(19, 1, 1, 'Bayas de Goji Deshidratadas (150g)', NULL, 850.00, 30, 'bayas_goji.jpg', 0, 0.00),
	(20, 2, 1, 'Sésamo Tostado Blanco (150g)', NULL, 250.00, 10, 'sesamo_tostado.jpg', 1, 0.00),
	(21, 1, 1, 'Quinoa Blanca Orgánica (500g)', NULL, 250.00, 10, 'quinoa_blanca.jpg', 1, 0.00),
	(22, 2, 1, 'Nueces Pecan Mitades (Kg)', NULL, 250.00, 10, 'nueces_pecan.jpg', 1, 0.00),
	(23, 2, 2, 'Jugo Prensado Verde Detox (500ml)', NULL, 850.00, 0, 'jugo_detox.jpg', 0, 0.00),
	(24, 1, 2, 'Agua de Coco Natural (1L)', NULL, 720.00, 30, 'agua_coco.jpg', 0, 0.00),
	(25, 2, 2, 'Kombucha Jengibre y Limón (330ml)', NULL, 720.00, 5, 'kombucha_jengibre.jpg', 0, 0.00),
	(26, 1, 2, 'Leche de Almendras Sin Azúcar (1L)', NULL, 610.00, 30, 'leche_almendras.jpg', 0, 0.00),
	(27, 2, 2, 'Té Matcha Japonés Orgánico (50g)', NULL, 850.00, 30, 'te_matcha.jpg', 0, 0.00),
	(28, 1, 2, 'Cerveza Artesanal Sin Alcohol (355ml)', NULL, 720.00, 0, 'cerveza_sin_alcohol.jpg', 0, 0.00),
	(29, 2, 2, 'Smoothie de Frutos Rojos y Chía (300ml)', NULL, 720.00, 30, 'smoothie_rojos.jpg', 0, 0.00),
	(30, 1, 2, 'Concentrado de Jengibre y Cúrcuma (250ml)', NULL, 720.00, 30, 'concentrado_curcuma.jpg', 0, 0.00),
	(31, 2, 2, 'Bebida de Avena Barista (1L)', NULL, 850.00, 30, 'bebida_avena_barista.jpg', 0, 0.00),
	(32, 1, 2, 'Infusión Digestiva de Manzanilla (20u)', NULL, 720.00, 5, 'infusion_manzanilla.jpg', 0, 0.00),
	(33, 2, 2, 'Agua Mineral Alcalina (1.5L)', NULL, 720.00, 0, 'agua_alcalina.jpg', 0, 0.00),
	(34, 1, 2, 'Jugo de Arándanos Puros (500ml)', NULL, 720.00, 30, 'jugo_arandanos.jpg', 0, 0.00),
	(35, 2, 2, 'Refresco de Flor de Hibiscus (1L)', NULL, 850.00, 30, 'refresco_hibiscus.jpg', 0, 0.00),
	(36, 1, 2, 'Chocolate Caliente Vegano (300g)', NULL, 610.00, 30, 'chocolate_vegano.jpg', 0, 0.00),
	(37, 2, 2, 'Bebida de Arroz con Vainilla (1L)', NULL, 720.00, 30, 'bebida_arroz.jpg', 0, 0.00),
	(38, 1, 2, 'Gaseosa de Pomelo Natural (350ml)', NULL, 720.00, 0, 'gaseosa_pomelo.jpg', 0, 0.00),
	(39, 2, 2, 'Té Negro con Cardamomo (Caja)', NULL, 850.00, 5, 'te_cardamomo.jpg', 0, 0.00),
	(40, 1, 2, 'Bebida Energética Guaraná Orgánica (250ml)', NULL, 250.00, 10, 'bebida_guarana.jpg', 1, 0.00),
	(41, 2, 2, 'Extracto de Aloe Vera Bebible (500ml)', NULL, 250.00, 10, 'aloe_vera_bebible.jpg', 1, 0.00),
	(42, 1, 2, 'Sidra de Manzana sin Azúcar (750ml)', NULL, 250.00, 10, 'sidra_manzana.jpg', 1, 0.00),
	(43, 1, 3, 'Mermelada de Frutilla Zero (300g)', NULL, 850.00, 0, 'mermelada_zero.jpg', 0, 0.00),
	(44, 2, 3, 'Golosinas Gummy Bears Zero (100g)', NULL, 720.00, 30, 'gummy_bears_zero.jpg', 0, 0.00),
	(45, 1, 3, 'Chocolatina Negra 85% Cacao Zero', NULL, 720.00, 30, 'chocolate_zero.jpg', 0, 0.00),
	(46, 2, 3, 'Galletas Integrales de Avena Zero (200g)', NULL, 610.00, 5, 'galletas_avena_zero.jpg', 0, 0.00),
	(47, 1, 3, 'Aderezo César Light Zero', NULL, 850.00, 30, 'aderezo_cesar_zero.jpg', 0, 0.00),
	(48, 2, 3, 'Stevia Líquida Concentrada (50ml)', NULL, 720.00, 0, 'stevia_liquida.jpg', 0, 0.00),
	(49, 1, 3, 'Torta Brownie Premix Zero (300g)', NULL, 720.00, 30, 'brownie_mix_zero.jpg', 0, 0.00),
	(50, 2, 3, 'Yogur Griego Natural Zero (150g)', NULL, 720.00, 30, 'yogur_griego_zero.jpg', 0, 0.00),
	(51, 1, 3, 'Helado Vainilla sin Azúcar (1L)', NULL, 850.00, 30, 'helado_vainilla_zero.jpg', 0, 0.00),
	(52, 2, 3, 'Salsa Ketchup Sin Azúcares Añadidos (350g)', NULL, 720.00, 30, 'ketchup_zero.jpg', 0, 0.00),
	(53, 1, 3, 'Barrita Proteica Sabor Chocolate Zero', NULL, 720.00, 0, 'barrita_zero.jpg', 0, 0.00),
	(54, 2, 3, 'Pan Molde Integral Zero (400g)', NULL, 720.00, 30, 'pan_molde_zero.jpg', 0, 0.00),
	(55, 1, 3, 'Miel de Agave Orgánica Light (300g)', NULL, 850.00, 30, 'miel_agave_light.jpg', 0, 0.00),
	(56, 2, 3, 'Caramelos Duros de Menta Zero (100g)', NULL, 610.00, 30, 'caramelos_zero.jpg', 0, 0.00),
	(57, 1, 3, 'Bebida Isotónica Zero Lima (500ml)', NULL, 720.00, 30, 'isotonica_zero.jpg', 0, 0.00),
	(58, 2, 3, 'Gelatina de Frutas Mixtas Zero (4u)', NULL, 720.00, 0, 'gelatina_zero.jpg', 0, 0.00),
	(59, 1, 3, 'Sirope de Chocolate Zero (250ml)', NULL, 850.00, 30, 'sirope_chocolate_zero.jpg', 0, 0.00),
	(60, 2, 3, 'Cheesecake Congelado Zero (porción)', NULL, 250.00, 10, 'cheesecake_zero.jpg', 1, 0.00),
	(61, 1, 3, 'Cereales Crujientes de Maíz Zero (300g)', NULL, 250.00, 10, 'cereales_zero.jpg', 1, 0.00),
	(62, 2, 3, 'Café Frío Espresso Zero (250ml)', NULL, 250.00, 10, 'cafe_frio_zero.jpg', 1, 0.00),
	(63, 2, 4, 'Mix de Vegetales para Wok (500g)', NULL, 850.00, 0, 'mix_wok_congelado.jpg', 0, 0.00),
	(64, 1, 4, 'Hamburguesas de Lentejas Veganas (4u)', NULL, 720.00, 30, 'hamburguesas_lentejas.jpg', 0, 0.00),
	(65, 2, 4, 'Pulpas de Acaí Puras (4x100g)', NULL, 720.00, 30, 'pulpa_acai.jpg', 0, 0.00),
	(66, 1, 4, 'Papas Rústicas Pre-fritas (Kg)', NULL, 610.00, 30, 'papas_rusticas.jpg', 0, 0.00),
	(67, 2, 4, 'Filetes de Salmón Congelados (300g)', NULL, 850.00, 5, 'salmon_congelado.jpg', 0, 0.00),
	(68, 1, 4, 'Espinacas Picadas Congeladas (500g)', NULL, 720.00, 0, 'espinacas_congeladas.jpg', 0, 0.00),
	(69, 2, 4, 'Pizza de Masa Madre Integral Congelada', NULL, 720.00, 30, 'pizza_integral_cong.jpg', 0, 0.00),
	(70, 1, 4, 'Muffins de Arándanos Congelados (6u)', NULL, 720.00, 30, 'muffins_congelados.jpg', 0, 0.00),
	(71, 2, 4, 'Frutillas Enteras Congeladas (500g)', NULL, 850.00, 30, 'frutillas_cong.jpg', 0, 0.00),
	(72, 1, 4, 'Bocaditos de Queso y Espinaca (6u)', NULL, 720.00, 30, 'bocaditos_queso.jpg', 0, 0.00),
	(73, 2, 4, 'Brócoli en Ramilletes Congelado (400g)', NULL, 720.00, 0, 'brocoli_cong.jpg', 0, 0.00),
	(74, 1, 4, 'Empanadas de Humita Veganas (6u)', NULL, 720.00, 5, 'empanadas_humita.jpg', 0, 0.00),
	(75, 2, 4, 'Croquetas de Pollo y Arroz Integral (8u)', NULL, 850.00, 30, 'croquetas_pollo.jpg', 0, 0.00),
	(76, 1, 4, 'Choclo Desgranado Congelado (500g)', NULL, 610.00, 30, 'choclo_cong.jpg', 0, 0.00),
	(77, 2, 4, 'Lasagna de Vegetales Sin TACC Congelada', NULL, 720.00, 30, 'lasagna_sintacc.jpg', 0, 0.00),
	(78, 1, 4, 'Helado de Maracuyá Artesanal (500ml)', NULL, 720.00, 0, 'helado_maracuya.jpg', 0, 0.00),
	(79, 2, 4, 'Waffles Belgas Integrales (4u)', NULL, 850.00, 30, 'waffles_integral.jpg', 0, 0.00),
	(80, 1, 4, 'Albóndigas de Soja Orgánica (300g)', NULL, 250.00, 10, 'albondigas_soja.jpg', 1, 0.00),
	(81, 2, 4, 'Calabaza en Cubos Congelada (500g)', NULL, 250.00, 10, 'calabaza_cubos.jpg', 1, 0.00),
	(82, 1, 4, 'Tortilla de Papa Española Congelada', NULL, 250.00, 10, 'tortilla_espanola.jpg', 1, 0.00),
	(83, 1, 5, 'Leche Entera Ecológica (1L)', NULL, 850.00, 0, 'leche_ecologica.jpg', 0, 0.00),
	(84, 2, 5, 'Yogur Natural Bífidus (500g)', NULL, 720.00, 30, 'yogur_bifidus.jpg', 0, 0.00),
	(85, 1, 5, 'Queso Untable Cremoso Light (250g)', NULL, 720.00, 30, 'queso_untable_light.jpg', 0, 0.00),
	(86, 2, 5, 'Manteca Ghee Clarificada (200g)', NULL, 610.00, 30, 'manteca_ghee.jpg', 0, 0.00),
	(87, 1, 5, 'Kéfir de Leche Probiótico (500ml)', NULL, 850.00, 30, 'kefir_leche.jpg', 0, 0.00),
	(88, 2, 5, 'Ricotta Fresca Sin Sal (300g)', NULL, 720.00, 0, 'ricotta_fresca.jpg', 0, 0.00),
	(89, 1, 5, 'Leche Descremada Deslactosada (1L)', NULL, 720.00, 30, 'leche_deslactosada.jpg', 0, 0.00),
	(90, 2, 5, 'Queso Parmesano Rallado (100g)', NULL, 720.00, 30, 'parmesano_rallado.jpg', 0, 0.00),
	(91, 1, 5, 'Crema de Leche Reducida en Grasa (200ml)', NULL, 850.00, 30, 'crema_reducida.jpg', 0, 0.00),
	(92, 2, 5, 'Postre de Chocolate Proteico (125g)', NULL, 720.00, 30, 'postre_proteico.jpg', 0, 0.00),
	(93, 1, 5, 'Suero de Mantequilla (Buttermilk) (1L)', NULL, 720.00, 0, 'suero_mantequilla.jpg', 0, 0.00),
	(94, 2, 5, 'Queso Duro Estacionado (200g)', NULL, 720.00, 30, 'queso_estacionado.jpg', 0, 0.00),
	(95, 1, 5, 'Yogur Bebible con Frutas (250ml)', NULL, 850.00, 5, 'yogur_bebible.jpg', 0, 0.00),
	(96, 2, 5, 'Leche de Cabra Fresca (500ml)', NULL, 610.00, 30, 'leche_cabra.jpg', 0, 0.00),
	(97, 1, 5, 'Muzzarella Bola Artesanal (200g)', NULL, 720.00, 30, 'muzzarella_bola.jpg', 0, 0.00),
	(98, 2, 5, 'Mantequilla de Campo Sin Sal (250g)', NULL, 720.00, 0, 'mantequilla_campo.jpg', 0, 0.00),
	(99, 1, 5, 'Postre de Arroz con Leche (150g)', NULL, 850.00, 30, 'postre_arroz.jpg', 0, 0.00),
	(100, 2, 5, 'Yogur de Coco (Vegano) (150g)', NULL, 250.00, 10, 'yogur_coco.jpg', 1, 0.00),
	(101, 1, 5, 'Queso Feta en Aceite de Oliva (150g)', NULL, 250.00, 10, 'queso_feta.jpg', 1, 0.00),
	(102, 2, 5, 'Leche Evaporada Light (400g)', NULL, 250.00, 10, 'leche_evaporada.jpg', 1, 0.00),
	(103, 2, 6, 'Pan Integral de Masa Madre (500g)', NULL, 850.00, 0, 'pan_masa_madre.jpg', 0, 0.00),
	(104, 1, 6, 'Bagels de Centeno (4u)', NULL, 720.00, 30, 'bagels_centeno.jpg', 0, 0.00),
	(105, 2, 6, 'Galletas de Agua Integrales (250g)', NULL, 720.00, 30, 'galletas_agua.jpg', 0, 0.00),
	(106, 1, 6, 'Focaccia de Oliva y Romero Fresca', NULL, 610.00, 30, 'focaccia_romero.jpg', 0, 0.00),
	(107, 2, 6, 'Muffins de Salvado y Miel (6u)', NULL, 850.00, 30, 'muffins_salvado.jpg', 0, 0.00),
	(108, 1, 6, 'Pan de Hamburguesa Integral (6u)', NULL, 720.00, 0, 'pan_hamburguesa.jpg', 0, 0.00),
	(109, 2, 6, 'Pitas de Trigo Sarraceno (5u)', NULL, 720.00, 5, 'pitas_sarraceno.jpg', 0, 0.00),
	(110, 1, 6, 'Media Lunas Integrales (6u)', NULL, 720.00, 30, 'media_lunas_int.jpg', 0, 0.00),
	(111, 2, 6, 'Tostadas de Arroz Multigrano (150g)', NULL, 850.00, 30, 'tostadas_multigrano.jpg', 0, 0.00),
	(112, 1, 6, 'Pre-pizza Integral Lista (2u)', NULL, 720.00, 30, 'pre_pizza_integral.jpg', 0, 0.00),
	(113, 2, 6, 'Pan de Pita Tradicional (6u)', NULL, 720.00, 0, 'pan_pita_tradicional.jpg', 0, 0.00),
	(114, 1, 6, 'Grisines de Sésamo y Lino (200g)', NULL, 720.00, 30, 'grisines_sesamo.jpg', 0, 0.00),
	(115, 2, 6, 'Pan de Molde con Semillas (500g)', NULL, 850.00, 30, 'pan_semillas.jpg', 0, 0.00),
	(116, 1, 6, 'Rollos de Canela Congelados (4u)', NULL, 610.00, 5, 'rollos_canela.jpg', 0, 0.00),
	(117, 2, 6, 'Masa de Tarta Pascualina Integral', NULL, 720.00, 30, 'masa_pascualina_int.jpg', 0, 0.00),
	(118, 1, 6, 'Pan Brioche Artesanal (400g)', NULL, 720.00, 0, 'pan_brioche.jpg', 0, 0.00),
	(119, 2, 6, 'Waffles Integrales Listos (6u)', NULL, 850.00, 30, 'waffles_listos.jpg', 0, 0.00),
	(120, 1, 6, 'Biscotes de Ajo y Perejil (150g)', NULL, 250.00, 10, 'biscotes_ajo.jpg', 1, 0.00),
	(121, 2, 6, 'Pan de Cebada y Nueces (500g)', NULL, 250.00, 10, 'pan_cebada_nueces.jpg', 1, 0.00),
	(122, 1, 6, 'Mini Chips de Chocolate Integrales (12u)', NULL, 250.00, 10, 'mini_chips_int.jpg', 1, 0.00),
	(123, 1, 7, 'Pasta Fusilli de Maíz Sin TACC (500g)', NULL, 850.00, 0, 'pasta_fusilli_sintacc.jpg', 0, 0.00),
	(124, 2, 7, 'Premezcla para Pan Sin TACC (500g)', NULL, 720.00, 30, 'premezcla_pan_sintacc.jpg', 0, 0.00),
	(125, 1, 7, 'Ñoquis de Papa Sin TACC Congelados (500g)', NULL, 720.00, 30, 'noquis_sintacc.jpg', 0, 0.00),
	(126, 2, 7, 'Cerveza Lager Sin Gluten (330ml)', NULL, 610.00, 30, 'cerveza_sintacc.jpg', 0, 0.00),
	(127, 1, 7, 'Tostadas de Arroz Clásicas (120g)', NULL, 850.00, 30, 'tostadas_arroz_sintacc.jpg', 0, 0.00),
	(128, 2, 7, 'Harina de Garbanzos (500g)', NULL, 720.00, 0, 'harina_garbanzos_sintacc.jpg', 0, 0.00),
	(129, 1, 7, 'Masa de Pizza Sin TACC (Individual)', NULL, 720.00, 30, 'masa_pizza_sintacc.jpg', 0, 0.00),
	(130, 2, 7, 'Alfajor de Maicena Sin TACC (2u)', NULL, 720.00, 5, 'alfajor_maicena_sintacc.jpg', 0, 0.00),
	(131, 1, 7, 'Galletas de Chispas de Chocolate Sin TACC', NULL, 850.00, 30, 'galletas_chips_sintacc.jpg', 0, 0.00),
	(132, 2, 7, 'Pan de Molde Blanco Sin TACC (400g)', NULL, 720.00, 30, 'pan_blanco_sintacc.jpg', 0, 0.00),
	(133, 1, 7, 'Leche Condensada Sin Lactosa/TACC (300g)', NULL, 720.00, 0, 'leche_condensada_sintacc.jpg', 0, 0.00),
	(134, 2, 7, 'Cereales de Arroz Inflado Sin TACC', NULL, 720.00, 30, 'cereales_arroz_sintacc.jpg', 0, 0.00),
	(135, 1, 7, 'Mix para Brownies Sin TACC (300g)', NULL, 850.00, 30, 'mix_brownies_sintacc.jpg', 0, 0.00),
	(136, 2, 7, 'Salsa de Tomate Lista Sin TACC (400g)', NULL, 610.00, 30, 'salsa_tomate_sintacc.jpg', 0, 0.00),
	(137, 1, 7, 'Bizcochos de Limón Sin TACC (200g)', NULL, 720.00, 5, 'bizcochos_sintacc.jpg', 0, 0.00),
	(138, 2, 7, 'Gomitas de Fruta Puras Sin TACC (100g)', NULL, 720.00, 0, 'gomitas_sintacc.jpg', 0, 0.00),
	(139, 1, 7, 'Harina de Yuca (Mandioca) (Kg)', NULL, 850.00, 30, 'harina_yuca.jpg', 0, 0.00),
	(140, 2, 7, 'Ravioles de Calabaza Sin TACC (300g)', NULL, 250.00, 10, 'ravioles_sintacc.jpg', 1, 0.00),
	(141, 1, 7, 'Tortillas de Maíz para Tacos (10u)', NULL, 250.00, 10, 'tortillas_maiz_sintacc.jpg', 1, 0.00),
	(142, 2, 7, 'Barra de Semillas y Frutas Sin TACC', NULL, 250.00, 10, 'barra_semillas_sintacc.jpg', 1, 0.00),
	(143, 2, 8, 'Mayonesa Vegana de Palta (300g)', NULL, 850.00, 0, 'mayonesa_palta_vegana.jpg', 0, 0.00),
	(144, 1, 8, 'Queso de Cajú Fermentado Sabor Cheddar', NULL, 720.00, 5, 'queso_caju_cheddar.jpg', 0, 0.00),
	(145, 2, 8, 'Tofu Orgánico Extra Firme (350g)', NULL, 720.00, 30, 'tofu_firme.jpg', 0, 0.00),
	(146, 1, 8, 'Mantequilla de Maní Natural (500g)', NULL, 610.00, 30, 'mantequilla_mani_vegana.jpg', 0, 0.00),
	(147, 2, 8, 'Chorizo Vegano de Proteína de Arveja (2u)', NULL, 850.00, 30, 'chorizo_vegano.jpg', 0, 0.00),
	(148, 1, 8, 'Leche de Coco en Lata (400ml)', NULL, 720.00, 0, 'leche_coco_lata.jpg', 0, 0.00),
	(149, 2, 8, 'Albóndigas de Portobello Congeladas (300g)', NULL, 720.00, 30, 'albondigas_portobello.jpg', 0, 0.00),
	(150, 1, 8, 'Gelatina Vegana de Agar-Agar Sabor Frambuesa', NULL, 720.00, 30, 'gelatina_agar.jpg', 0, 0.00),
	(151, 2, 8, 'Snacks de Algas Marinas Tostadas (10g)', NULL, 850.00, 5, 'snacks_algas.jpg', 0, 0.00),
	(152, 1, 8, 'Helado de Chocolate y Banana Vegano (1L)', NULL, 720.00, 30, 'helado_choco_banana.jpg', 0, 0.00),
	(153, 2, 8, 'Crema de Soja para Cocinar (200ml)', NULL, 720.00, 0, 'crema_soja_cocinar.jpg', 0, 0.00),
	(154, 1, 8, 'Sustituto de Huevo en Polvo (200g)', NULL, 720.00, 30, 'sustituto_huevo.jpg', 0, 0.00),
	(155, 2, 8, 'Paté de Lentejas y Pimientos (150g)', NULL, 850.00, 30, 'pate_lentejas.jpg', 0, 0.00),
	(156, 1, 8, 'Barritas Energéticas de Dátiles (6u)', NULL, 610.00, 30, 'barritas_datiles.jpg', 0, 0.00),
	(157, 2, 8, 'Carne Molida Vegana Texturizada (250g)', NULL, 720.00, 30, 'carne_vegana_text.jpg', 0, 0.00),
	(158, 1, 8, 'Yogur de Almendras con Frutos Secos (150g)', NULL, 720.00, 0, 'yogur_almendras_vegan.jpg', 0, 0.00),
	(159, 2, 8, 'Masa de Hojaldre Vegana (Lámina)', NULL, 850.00, 30, 'hojaldre_vegana.jpg', 0, 0.00),
	(160, 1, 8, 'Bacon Vegano Ahumado (100g)', NULL, 250.00, 10, 'bacon_vegano.jpg', 1, 0.00),
	(161, 2, 8, 'Miel de Diente de León (Alternativa) (300g)', NULL, 1450.00, 55, 'miel_diente_leon.jpg', 0, 0.00),
	(162, 1, 8, 'Aceite de Coco Virgen Orgánico (500ml)', NULL, 2900.00, 60, 'aceite_coco_virgen.jpg', 0, 0.00);

-- Volcando estructura para tabla mi__db.proveedores
CREATE TABLE IF NOT EXISTS `proveedores` (
  `id_proveedor` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_proveedor` varchar(100) NOT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_proveedor`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Volcando datos para la tabla mi__db.proveedores: ~2 rows (aproximadamente)
INSERT INTO `proveedores` (`id_proveedor`, `nombre_proveedor`, `contacto`, `telefono`) VALUES
	(1, 'A.S.A', 'fr069864@gmail.com', '3625168853'),
	(2, 'Veggie Foods', 'maria.gomez@veggiefoods.com', '3624168056');

-- Volcando estructura para tabla mi__db.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id_rol` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(50) NOT NULL,
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `nombre_rol` (`nombre_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Volcando datos para la tabla mi__db.roles: ~2 rows (aproximadamente)
INSERT INTO `roles` (`id_rol`, `nombre_rol`) VALUES
	(1, 'Cliente'),
	(2, 'Administrador');

-- Volcando estructura para tabla mi__db.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `id_rol` int(11) NOT NULL DEFAULT 1,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion_principal` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `ultimo_login` datetime DEFAULT NULL,
  `token_recuperacion` varchar(64) DEFAULT NULL,
  `expiracion_token` datetime DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_usuario_rol` (`id_rol`),
  CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Volcando datos para la tabla mi__db.usuarios: ~2 rows (aproximadamente)
INSERT INTO `usuarios` (`id_usuario`, `id_rol`, `nombre`, `apellido`, `email`, `telefono`, `direccion_principal`, `password`, `fecha_registro`, `ultimo_login`, `token_recuperacion`, `expiracion_token`) VALUES
	(1, 1, 'Francisco', 'Ramirez', 'franciscouram1@gmail.com', '3625168853', 'Av. 25 De Mayo 250', '$2y$10$oTSPvuP6Kb1NSLpjH1Xs7Oyh4BlqhXS6dY0/NaDhozbV2t/LAXiH6', '2025-10-17 23:16:31', '2025-10-18 18:19:16', NULL, NULL),
	(2, 2, 'Francisco', 'Ramirez', 'fr069864@gmail.com', '3624168853', 'Av. 25 De Mayo 300', '$2y$10$/2QDuTQEhXSJPM03koLI5erosJo0EqxSvdQerrBON0cyMEQtuwE7m', '2025-10-17 23:35:38', '2025-10-29 15:27:47', NULL, NULL),
	(7, 1, 'cami', 'cami', 'kramer@gmail.com', '12345', '1234567', '$2y$10$vH8m.970jyPJoXD65z9do.n.d9TJrW.1YAwjxL8x3UA2GeckmccCW', '2025-10-20 12:49:49', '2025-10-20 13:11:32', NULL, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
