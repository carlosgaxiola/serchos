/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.5.5-10.1.10-MariaDB : Database - lavanderia
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`lavanderia` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `lavanderia`;

/*Table structure for table `clientes` */

DROP TABLE IF EXISTS `clientes`;

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del registro',
  `nombre` varchar(45) DEFAULT NULL COMMENT 'Nombre del cliente',
  `domicilio` varchar(100) DEFAULT NULL COMMENT 'Domicilio del cliente',
  `telefono` varchar(15) DEFAULT NULL COMMENT 'Telefono del cliente',
  `id_usuario` int(11) NOT NULL COMMENT 'Usuario que hizo el registro',
  `f_registro` date DEFAULT NULL COMMENT 'Fecha de registro',
  `status` int(11) DEFAULT NULL COMMENT 'Estado del registro (0 Inactivo, 1 Activo)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `clientes` */

/*Table structure for table `contratos` */

DROP TABLE IF EXISTS `contratos`;

CREATE TABLE `contratos` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del registro',
  `texto` longtext COMMENT 'Contenido del contrato',
  `f_contrato` date DEFAULT NULL COMMENT 'Fecha de contratacion',
  `id_usuario` int(11) NOT NULL COMMENT 'Usuario que hizo el registro',
  `f_registro` date DEFAULT NULL COMMENT 'Fecha de registro',
  `status` int(11) DEFAULT NULL COMMENT 'Estado del registro (0 Inactivo, 1 Activo)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `contratos` */

/*Table structure for table `cortesias` */

DROP TABLE IF EXISTS `cortesias`;

CREATE TABLE `cortesias` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del registro',
  `id_cliente` int(11) NOT NULL COMMENT 'Identificador del cliente para el cual es la cortesia',
  `id_servicio` int(11) NOT NULL COMMENT 'Identificador del servicio dado en la cortesia',
  `f_inicio` date DEFAULT NULL COMMENT 'Fecha de inicio del periodo de la cortesia',
  `periodo` int(11) DEFAULT NULL COMMENT 'Año del periodo de la cortesia (Ej. 2019)',
  `id_usuario` int(11) NOT NULL COMMENT 'Usuario que hizo el registro',
  `f_registro` date DEFAULT NULL COMMENT 'Fecha de registro',
  `status` int(11) DEFAULT NULL COMMENT 'Estado del registro (0 Inactivo, 1 Activo)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `cortesias` */

/*Table structure for table `detalle_cortesia` */

DROP TABLE IF EXISTS `detalle_cortesia`;

CREATE TABLE `detalle_cortesia` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del registro',
  `id_cortesia` int(11) NOT NULL COMMENT 'Identificador de la cortesia',
  `numero` int(11) DEFAULT NULL COMMENT 'Contador del la cortesia',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `detalle_cortesia` */

/*Table structure for table `detalle_recibos` */

DROP TABLE IF EXISTS `detalle_recibos`;

CREATE TABLE `detalle_recibos` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del registro',
  `id_servicio` int(11) NOT NULL COMMENT 'Identificador de los servicios incluidos en un recibo',
  `id_recibo` int(11) NOT NULL COMMENT 'Identificador del recibo del detalle',
  `cantidad` int(11) DEFAULT NULL COMMENT 'cantidad del servicio',
  `costo` float DEFAULT NULL COMMENT 'Costo del servicio al momento de la emision del recibo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `detalle_recibos` */

/*Table structure for table `modulos` */

DROP TABLE IF EXISTS `modulos`;

CREATE TABLE `modulos` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del registro',
  `nombre` varchar(45) DEFAULT NULL COMMENT 'Nombre del modulo',
  `descripcion` tinytext COMMENT 'Descripcion del modulo',
  `ruta` tinytext COMMENT 'Url del modulo dentro del sistema',
  `fa_icono` varchar(45) DEFAULT NULL COMMENT 'Clase FontAwesome del icono del modulo',
  `id_padre` int(11) DEFAULT NULL COMMENT 'Identificador del padre del modulo, puede ser null',
  `f_registro` date DEFAULT NULL COMMENT 'Fecha de registro',
  `status` int(11) DEFAULT NULL COMMENT 'Estado del modulo (0 Inactivo, 1 Activo)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `modulos` */

/*Table structure for table `perfiles` */

DROP TABLE IF EXISTS `perfiles`;

CREATE TABLE `perfiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del registro',
  `nombre` varchar(45) DEFAULT NULL COMMENT 'Nombre del perfil',
  `descripcion` tinytext COMMENT 'Descripcion de lo que hace el perfil',
  `status` int(11) DEFAULT NULL COMMENT 'Estado del registro (0 Inactivo, 1 Activo)',
  `f_registro` date DEFAULT NULL COMMENT 'Fecha de registro',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `perfiles` */

/*Table structure for table `perfiles_modulos` */

DROP TABLE IF EXISTS `perfiles_modulos`;

CREATE TABLE `perfiles_modulos` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del registro',
  `id_perfil` int(11) NOT NULL COMMENT 'Idenficador del perfil asociado con el modulo',
  `id_modulo` int(11) NOT NULL COMMENT 'Identificador del modulo asociado con el perfil',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `perfiles_modulos` */

/*Table structure for table `recibos` */

DROP TABLE IF EXISTS `recibos`;

CREATE TABLE `recibos` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del registro',
  `f_recibo` date DEFAULT NULL COMMENT 'Fecha de emision del recibo',
  `id_cliente` int(11) NOT NULL COMMENT 'Identificador del cliente al que se le emitio este recibo',
  `total` float DEFAULT NULL COMMENT 'Monto total del recibo',
  `observaciones` text COMMENT 'Notas o comentarios extra respecto al recibo',
  `f_registro` date DEFAULT NULL COMMENT 'Fecha de registro',
  `id_usuario` int(11) NOT NULL COMMENT 'Usuario que hizo el registro',
  `status` int(11) DEFAULT NULL COMMENT 'Estado del registro (0 Inactivo, 1 Activo)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `recibos` */

/*Table structure for table `servicios` */

DROP TABLE IF EXISTS `servicios`;

CREATE TABLE `servicios` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del registro',
  `nombre` varchar(45) DEFAULT NULL COMMENT 'Nombre del servicio',
  `costo` float DEFAULT NULL COMMENT 'Costo del servicio actualmente',
  `id_usuario` int(11) NOT NULL COMMENT 'Usuario que hizo el registro',
  `f_registro` date DEFAULT NULL COMMENT 'Fecha de registro',
  `status` int(11) DEFAULT NULL COMMENT 'Estado del registro (0 Inactivo, 1 Activo)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `servicios` */

/*Table structure for table `usuarios` */

DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del registro',
  `nombre` varchar(45) DEFAULT NULL COMMENT 'Nombre del usuario, con este inicia sesion',
  `contraseña` varchar(100) DEFAULT NULL COMMENT 'Contraseña para iniciar sesion',
  `id_perfil` int(11) NOT NULL COMMENT 'Perfil que el usuario representa',
  `f_registro` date DEFAULT NULL COMMENT 'Fecha de registro',
  `id_usuario` int(11) NOT NULL COMMENT 'Usuario que hizo el registro',
  `status` int(11) DEFAULT NULL COMMENT 'Estado del registro (0 Inactiv, 1 Activo)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `usuarios` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
