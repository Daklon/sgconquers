
-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 08-08-2013 a las 08:45:43
-- Versión del servidor: 5.1.69
-- Versión de PHP: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `u112611173_sgcon`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cons_pend`
--

CREATE TABLE IF NOT EXISTS `cons_pend` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `cancelado` tinyint(1) NOT NULL,
  `planeta` int(5) NOT NULL,
  `jugador` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `horafinalizar` int(20) NOT NULL,
  `cantidad` int(6) NOT NULL,
  `unidad` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `tipo` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `existencias`
--

CREATE TABLE IF NOT EXISTS `existencias` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `planetaactual` int(5) NOT NULL,
  `dueño` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `ucav` int(6) NOT NULL,
  `ingenieros` int(6) NOT NULL,
  `comando_sg` int(6) NOT NULL,
  `oficial_sg` int(6) NOT NULL,
  `x-301` int(6) NOT NULL,
  `x-302` int(6) NOT NULL,
  `bc-303` int(6) NOT NULL,
  `bc-304` int(6) NOT NULL,
  `guarnicion_defensiva` int(6) NOT NULL,
  `lanzacohetes` int(6) NOT NULL,
  `cohete_balistico` int(6) NOT NULL,
  `sonda_asgard` int(6) NOT NULL,
  `supersoldado` int(6) NOT NULL,
  `nave_de_asalto` int(6) NOT NULL,
  `beliksner` int(6) NOT NULL,
  `jackson` int(6) NOT NULL,
  `oneill` int(6) NOT NULL,
  `martillo_de_defensa` int(6) NOT NULL,
  `satelite_asgard` int(6) NOT NULL,
  `comando_exploracion` int(6) NOT NULL,
  `zapadores` int(6) NOT NULL,
  `comando_atlantis` int(6) NOT NULL,
  `marine` int(6) NOT NULL,
  `f-302` int(6) NOT NULL,
  `jumper` int(6) NOT NULL,
  `bc304` int(6) NOT NULL,
  `aurora` int(6) NOT NULL,
  `comando_de_defensa` int(6) NOT NULL,
  `canon_rail` int(6) NOT NULL,
  `satelite_antiguo` int(6) NOT NULL,
  `sonda_escaner` int(6) NOT NULL,
  `guerrero_wraith` int(6) NOT NULL,
  `oficial_wraith` int(6) NOT NULL,
  `dardo` int(6) NOT NULL,
  `explorador` int(6) NOT NULL,
  `crucero_wraith` int(6) NOT NULL,
  `nave_colmena` int(6) NOT NULL,
  `iratus` int(6) NOT NULL,
  `droide_de_reconocimiento` int(6) NOT NULL,
  `guerreros_jaffa` int(6) NOT NULL,
  `guardia_personal` int(6) NOT NULL,
  `guerrero_kull` int(6) NOT NULL,
  `planeador_de_la_muerte` int(6) NOT NULL,
  `aguja_afilada` int(6) NOT NULL,
  `alkesh` int(6) NOT NULL,
  `hatak` int(6) NOT NULL,
  `palacio_nodriza` int(6) NOT NULL,
  `canon_pesado` int(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `existencias_defensas`
--

CREATE TABLE IF NOT EXISTS `existencias_defensas` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `planetaactual` int(5) NOT NULL,
  `dueño` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `guarnicion_defensiva` int(6) DEFAULT NULL,
  `lanzacohetes` int(6) DEFAULT NULL,
  `cohete_balistico` int(6) DEFAULT NULL,
  `martillo_de_defensa` int(6) DEFAULT NULL,
  `satelite_asgard` int(6) DEFAULT NULL,
  `comando_de_defensa` int(6) DEFAULT NULL,
  `canon_rail` int(6) DEFAULT NULL,
  `satelite_antiguo` int(6) DEFAULT NULL,
  `canon_pesado` int(6) DEFAULT NULL,
  `torreta` int(6) DEFAULT NULL,
  `satelite_goauld` int(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `existencias_naves`
--

CREATE TABLE IF NOT EXISTS `existencias_naves` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `planetaactual` int(5) NOT NULL,
  `dueño` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `x-301` int(6) DEFAULT NULL,
  `x-302` int(6) DEFAULT NULL,
  `bc-303` int(6) DEFAULT NULL,
  `bc-304` int(6) DEFAULT NULL,
  `nave_de_asalto` int(6) DEFAULT NULL,
  `beliksner` int(6) DEFAULT NULL,
  `jackson` int(6) DEFAULT NULL,
  `oneill` int(6) DEFAULT NULL,
  `f-302` int(6) DEFAULT NULL,
  `jumper` int(6) DEFAULT NULL,
  `bc304` int(6) DEFAULT NULL,
  `aurora` int(6) DEFAULT NULL,
  `dardo` int(6) DEFAULT NULL,
  `explorador` int(6) DEFAULT NULL,
  `crucero_wraith` int(6) DEFAULT NULL,
  `nave_colmena` int(6) DEFAULT NULL,
  `planeador_de_la_muerte` int(6) DEFAULT NULL,
  `aguja_afilada` int(6) DEFAULT NULL,
  `alkesh` int(6) DEFAULT NULL,
  `hatak` int(6) DEFAULT NULL,
  `palacio_nodriza` int(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `existencias_tropas`
--

CREATE TABLE IF NOT EXISTS `existencias_tropas` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `planetaactual` int(5) NOT NULL,
  `dueño` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `ucav` int(6) DEFAULT NULL,
  `ingenieros` int(6) DEFAULT NULL,
  `comando_sg` int(6) DEFAULT NULL,
  `oficial_sg` int(6) DEFAULT NULL,
  `sonda_asgard` int(6) DEFAULT NULL,
  `supersoldado` int(6) DEFAULT NULL,
  `comando_exploracion` int(6) DEFAULT NULL,
  `zapadores` int(6) DEFAULT NULL,
  `comando_atlantis` int(6) DEFAULT NULL,
  `marine` int(6) DEFAULT NULL,
  `sonda_escaner` int(6) DEFAULT NULL,
  `guerrero_wraith` int(6) DEFAULT NULL,
  `oficial_wraith` int(6) DEFAULT NULL,
  `droide_de_reconocimiento` int(6) DEFAULT NULL,
  `guerreros_jaffa` int(6) DEFAULT NULL,
  `guardia_personal` int(6) DEFAULT NULL,
  `guerrero_kull` int(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `investigaciones`
--

CREATE TABLE IF NOT EXISTS `investigaciones` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `numero` int(2) NOT NULL,
  `raza` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `tipo` int(1) unsigned NOT NULL,
  `nombre` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8_unicode_ci NOT NULL,
  `recurso1` int(5) NOT NULL,
  `recurso2` int(5) NOT NULL,
  `tiempo` int(5) NOT NULL,
  `efecrecurso1` int(2) DEFAULT NULL,
  `efecrecurso2` int(2) DEFAULT NULL,
  `efecenergia` int(2) DEFAULT NULL,
  `efecataquesoldado` int(2) DEFAULT NULL,
  `efecresistenciasoldado` int(2) DEFAULT NULL,
  `efecescudossoldado` int(2) DEFAULT NULL,
  `efeccargasoldado` int(2) DEFAULT NULL,
  `efecataquenave` int(2) DEFAULT NULL,
  `efecresistencianave` int(2) DEFAULT NULL,
  `efecescudosnave` int(2) DEFAULT NULL,
  `efeccarganave` int(2) DEFAULT NULL,
  `efecvelocidadnave` int(2) DEFAULT NULL,
  `efeclimitemisiones` int(1) DEFAULT NULL,
  `efecataquedefensa` int(2) DEFAULT NULL,
  `efecresistenciadefensa` int(2) DEFAULT NULL,
  `efecescudosdefensa` int(2) DEFAULT NULL,
  `efeclimitetropas` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=62 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inves_pend`
--

CREATE TABLE IF NOT EXISTS `inves_pend` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `cancelado` tinyint(1) NOT NULL DEFAULT '0',
  `jugador` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `investigacion` int(2) NOT NULL,
  `nivelnuevo` int(2) NOT NULL,
  `horafinalizar` int(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=62 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugadores`
--

CREATE TABLE IF NOT EXISTS `jugadores` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `activado` tinyint(1) NOT NULL DEFAULT '0',
  `nick` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(35) COLLATE utf8_unicode_ci NOT NULL,
  `codigoseguridad` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `raza` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `recurso1` int(13) NOT NULL,
  `recurso2` int(13) NOT NULL,
  `inv1` int(2) NOT NULL,
  `inv2` int(2) NOT NULL,
  `inv3` int(2) NOT NULL,
  `inv4` int(2) NOT NULL,
  `inv5` int(2) NOT NULL,
  `inv6` int(2) NOT NULL,
  `inv7` int(2) NOT NULL,
  `inv8` int(2) NOT NULL,
  `inv9` int(2) NOT NULL,
  `inv10` int(2) NOT NULL,
  `inv11` int(2) NOT NULL,
  `inv12` int(2) NOT NULL,
  `inv13` int(2) NOT NULL,
  `planeta1` int(4) NOT NULL,
  `planeta2` int(4) DEFAULT NULL,
  `planeta3` int(4) DEFAULT NULL,
  `planeta4` int(4) DEFAULT NULL,
  `planeta5` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mapa`
--

CREATE TABLE IF NOT EXISTS `mapa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `galaxia` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `sector` int(2) NOT NULL,
  `cuadrante` int(2) NOT NULL,
  `posicion` int(2) NOT NULL,
  `porcentaje` int(3) NOT NULL,
  `nombre` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `dueño` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=242 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE IF NOT EXISTS `mensajes` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `de` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `para` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `hora` int(12) NOT NULL,
  `asunto` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `mensaje` text COLLATE utf8_unicode_ci NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mov_pend`
--

CREATE TABLE IF NOT EXISTS `mov_pend` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `cancelado` tinyint(1) NOT NULL,
  `vuelta` int(9) DEFAULT NULL,
  `planetaactual` int(5) NOT NULL,
  `planetadestino` int(5) NOT NULL,
  `jugador` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `horallegada` int(20) NOT NULL,
  `horavuelta` int(20) DEFAULT NULL,
  `ucav` int(6) NOT NULL,
  `ingenieros` int(6) NOT NULL,
  `comando_sg` int(6) NOT NULL,
  `oficial_sg` int(6) NOT NULL,
  `x-301` int(6) NOT NULL,
  `x-302` int(6) NOT NULL,
  `bc-303` int(6) NOT NULL,
  `bc-304` int(6) NOT NULL,
  `guarnicion_defensiva` int(6) NOT NULL,
  `lanzacohetes` int(6) NOT NULL,
  `cohete_balistico` int(6) NOT NULL,
  `sonda_asgard` int(6) NOT NULL,
  `supersoldado` int(6) NOT NULL,
  `nave_de_asalto` int(6) NOT NULL,
  `beliksner` int(6) NOT NULL,
  `jackson` int(6) NOT NULL,
  `oneill` int(6) NOT NULL,
  `martillo_de_defensa` int(6) NOT NULL,
  `satelite_asgard` int(6) NOT NULL,
  `comando_exploracion` int(6) NOT NULL,
  `zapadores` int(6) NOT NULL,
  `comando_atlantis` int(6) NOT NULL,
  `marine` int(6) NOT NULL,
  `f-302` int(6) NOT NULL,
  `jumper` int(6) NOT NULL,
  `bc304` int(6) NOT NULL,
  `aurora` int(6) NOT NULL,
  `comando_de_defensa` int(6) NOT NULL,
  `canon_rail` int(6) NOT NULL,
  `satelite_antiguo` int(6) NOT NULL,
  `sonda_escaner` int(6) NOT NULL,
  `guerrero_wraith` int(6) NOT NULL,
  `oficial_wraith` int(6) NOT NULL,
  `dardo` int(6) NOT NULL,
  `explorador` int(6) NOT NULL,
  `crucero_wraith` int(6) NOT NULL,
  `nave_colmena` int(6) NOT NULL,
  `iratus` int(6) NOT NULL,
  `droide_de_reconocimiento` int(6) NOT NULL,
  `guerreros_jaffa` int(6) NOT NULL,
  `guardia_personal` int(6) NOT NULL,
  `guerrero_kull` int(6) NOT NULL,
  `planeador_de_la_muerte` int(6) NOT NULL,
  `aguja_afilada` int(6) NOT NULL,
  `alkesh` int(6) NOT NULL,
  `hatak` int(6) NOT NULL,
  `palacio_nodriza` int(6) NOT NULL,
  `canon_pesado` int(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `raza`
--

CREATE TABLE IF NOT EXISTS `raza` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `limitetropas` int(11) NOT NULL,
  `energia` int(5) NOT NULL,
  `recurso1` int(5) NOT NULL,
  `recurso2` int(5) NOT NULL,
  `limiteplanetas` int(1) NOT NULL,
  `stargateintergalactico` tinyint(1) NOT NULL,
  `viajesintergalacticos` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades`
--

CREATE TABLE IF NOT EXISTS `unidades` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8_unicode_ci NOT NULL,
  `raza` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `recurso1` int(4) NOT NULL,
  `recurso2` int(4) NOT NULL,
  `recurso3` int(4) NOT NULL,
  `tiempo` int(6) NOT NULL,
  `inv1` int(2) NOT NULL,
  `inv2` int(2) NOT NULL,
  `inv3` int(2) NOT NULL,
  `inv4` int(2) NOT NULL,
  `inv5` int(2) NOT NULL,
  `inv6` int(2) NOT NULL,
  `inv7` int(2) NOT NULL,
  `inv8` int(2) NOT NULL,
  `inv9` int(2) NOT NULL,
  `inv10` int(2) NOT NULL,
  `inv11` int(2) NOT NULL,
  `inv12` int(2) NOT NULL,
  `inv13` int(2) NOT NULL,
  `ataque` int(6) NOT NULL,
  `defensa` int(6) NOT NULL,
  `escudo` int(6) NOT NULL,
  `velocidad` int(3) NOT NULL,
  `cazas` int(3) NOT NULL,
  `carga` int(5) NOT NULL,
  `autodestruccion` tinyint(1) NOT NULL,
  `atraviesa_escudos` tinyint(1) NOT NULL,
  `camuflaje` tinyint(1) NOT NULL,
  `atraviesa_stargate` tinyint(1) NOT NULL,
  `tipo` varchar(17) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=51 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
