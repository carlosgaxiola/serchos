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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `clientes` */

insert  into `clientes`(`id`,`nombre`,`domicilio`,`telefono`,`id_usuario`,`f_registro`,`status`) values (1,'juan pablo','Av. de Los Deportes #33\r\nJuan Miguel Carrillo #28','6949562774',1,'2019-03-18',0),(2,'carlos','Av. de Los Deportes #33\r\nJuan Miguel Carrillo #28','1234567890',1,'2019-03-18',1),(3,'rosa angelica','casa de la maestra','0987654332',1,'2019-03-18',1);

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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

/*Data for the table `modulos` */

insert  into `modulos`(`id`,`nombre`,`descripcion`,`ruta`,`fa_icono`,`id_padre`,`f_registro`,`status`) values (1,'Inicio','Modulos de inicio','inicio','fas fa fa-home',0,'2019-03-10',1),(11,'Perfiles','Modulo que almacena los tipos de usuarios (Perfiles) del sistema','administrar/perfiles','fas fa fa-users-cog',0,'2019-03-09',1),(12,'Modulos','Modulo de modulos','administrar/modulos','fas fa fa-database',0,'2019-03-09',1),(13,'Clientes','Agregar, editar, eliminar y visualizar clientes','administrar/clientes','fas fa fa-user',0,'2019-03-09',1),(14,'Servicios','Modulo encargado de los servicios de la lavanderia','administrar/servicios','fas fa fa-tags',0,'2019-03-09',1),(15,'Cortesias','Las cortesias se dan a los clientes por un determinado numero de recibos','administrar/cortesias','fas fa fa-gift',0,'2019-03-09',1),(16,'Contratos','Gestionar la informacion de los contratos de los empleados','administrar/contratos','fas fa fa-file-text-o',0,'2019-03-09',1),(17,'Recibos','Agregar, editar, alternar y visualizar recibos.','administrar/recibos','fas fa fa-files-o',0,'2019-03-09',1),(18,'Usuarios','Agrega, edita, elimina y visualiza usuarios','administrar/usuarios','fas fa fa-users',0,'2019-03-10',1),(19,'Cambiar Contraseña','modulo para cambiar la contraseña del usuario en sesión','administrar/cambiar','fas fa fa-chain',0,'2019-04-09',1);

/*Table structure for table `perfiles` */

DROP TABLE IF EXISTS `perfiles`;

CREATE TABLE `perfiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del registro',
  `nombre` varchar(45) DEFAULT NULL COMMENT 'Nombre del perfil',
  `descripcion` tinytext COMMENT 'Descripcion de lo que hace el perfil',
  `status` int(11) DEFAULT NULL COMMENT 'Estado del registro (0 Inactivo, 1 Activo)',
  `f_registro` date DEFAULT NULL COMMENT 'Fecha de registro',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `perfiles` */

insert  into `perfiles`(`id`,`nombre`,`descripcion`,`status`,`f_registro`) values (1,'Desarrollador','Tiene permiso para editar modulos importantes para la integridad del sistema',1,'2019-03-09'),(2,'Encargado','Acceso a modulos de servicios, cortesias, clientes, contratos y recibos',1,'2019-03-09'),(3,'Usuario comun','Modulos de clientes, servicios y cortesias',1,'2019-03-09');

/*Table structure for table `perfiles_modulos` */

DROP TABLE IF EXISTS `perfiles_modulos`;

CREATE TABLE `perfiles_modulos` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador del registro',
  `id_perfil` int(11) NOT NULL COMMENT 'Idenficador del perfil asociado con el modulo',
  `id_modulo` int(11) NOT NULL COMMENT 'Identificador del modulo asociado con el perfil',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=latin1;

/*Data for the table `perfiles_modulos` */

insert  into `perfiles_modulos`(`id`,`id_perfil`,`id_modulo`) values (69,1,1),(70,1,11),(71,1,12),(72,1,13),(73,1,14),(74,1,15),(75,1,16),(76,1,17),(77,1,18),(78,1,19),(79,2,13),(80,2,14),(81,2,15),(82,2,16),(83,2,17),(84,2,19),(85,3,13),(86,3,14),(87,3,15),(88,3,17),(89,3,19);

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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

/*Data for the table `servicios` */

insert  into `servicios`(`id`,`nombre`,`costo`,`id_usuario`,`f_registro`,`status`) values (1,'servicio basico',26,1,'2019-03-18',1),(2,'edredon matr',80,1,'2019-03-18',1),(3,'cobertor matr',60,1,'2019-03-18',1),(4,'lavadora grd',50,1,'2019-03-18',1),(5,'lavadora 5kg',22,1,'2019-03-18',1),(6,'secadora ch',35,1,'2019-03-18',1),(7,'secadora gde',80,1,'2019-03-18',1),(8,'jabon',7,1,'2019-03-18',1),(9,'suavizante',7,1,'2019-03-18',1),(10,'pinol',5,1,'2019-03-18',1),(11,'cloro',5,1,'2019-03-18',1),(12,'bolsa',7,1,'2019-03-18',1),(13,'detergente liq',7,1,'2019-03-18',1),(14,'planchazo pza',8,1,'2019-03-18',1),(15,'prueba',10,1,'2019-03-18',1);

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*Data for the table `usuarios` */

insert  into `usuarios`(`id`,`nombre`,`contraseña`,`id_perfil`,`f_registro`,`id_usuario`,`status`) values (1,'admin','7110eda4d09e062aa5e4a390b0a572ac0d2c0220',1,'2019-03-10',1,1),(2,'carlos','8c31b65bdecdc9f18b695d7318186fd1feed690d',3,'2019-03-10',1,1),(3,'encargado','8c31b65bdecdc9f18b695d7318186fd1feed690d',2,'2019-03-10',1,1),(5,'usuario','8c31b65bdecdc9f18b695d7318186fd1feed690d',3,'2019-03-10',1,0),(6,'prueba','8c31b65bdecdc9f18b695d7318186fd1feed690d',1,'2019-03-10',1,0),(7,'prueba2','8c31b65bdecdc9f18b695d7318186fd1feed690d',1,'2019-03-10',1,0);

/*Table structure for table `listar_detalle_recibo` */

DROP TABLE IF EXISTS `listar_detalle_recibo`;

/*!50001 DROP VIEW IF EXISTS `listar_detalle_recibo` */;
/*!50001 DROP TABLE IF EXISTS `listar_detalle_recibo` */;

/*!50001 CREATE TABLE  `listar_detalle_recibo`(
 `id` int(11) ,
 `id_servicio` int(11) ,
 `id_recibo` int(11) ,
 `cantidad` int(11) ,
 `costo` float ,
 `nombre` varchar(45) 
)*/;

/*Table structure for table `listar_recibos` */

DROP TABLE IF EXISTS `listar_recibos`;

/*!50001 DROP VIEW IF EXISTS `listar_recibos` */;
/*!50001 DROP TABLE IF EXISTS `listar_recibos` */;

/*!50001 CREATE TABLE  `listar_recibos`(
 `id` int(11) ,
 `f_recibo` date ,
 `id_cliente` int(11) ,
 `total` float ,
 `observaciones` text ,
 `f_registro` date ,
 `id_usuario` int(11) ,
 `status` int(11) ,
 `cliente` varchar(45) 
)*/;

/*View structure for view listar_detalle_recibo */

/*!50001 DROP TABLE IF EXISTS `listar_detalle_recibo` */;
/*!50001 DROP VIEW IF EXISTS `listar_detalle_recibo` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `listar_detalle_recibo` AS (select `det`.`id` AS `id`,`det`.`id_servicio` AS `id_servicio`,`det`.`id_recibo` AS `id_recibo`,`det`.`cantidad` AS `cantidad`,`det`.`costo` AS `costo`,`ser`.`nombre` AS `nombre` from (`detalle_recibos` `det` join `servicios` `ser` on((`ser`.`id` = `det`.`id_servicio`)))) */;

/*View structure for view listar_recibos */

/*!50001 DROP TABLE IF EXISTS `listar_recibos` */;
/*!50001 DROP VIEW IF EXISTS `listar_recibos` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `listar_recibos` AS (select `re`.`id` AS `id`,`re`.`f_recibo` AS `f_recibo`,`re`.`id_cliente` AS `id_cliente`,`re`.`total` AS `total`,`re`.`observaciones` AS `observaciones`,`re`.`f_registro` AS `f_registro`,`re`.`id_usuario` AS `id_usuario`,`re`.`status` AS `status`,`cli`.`nombre` AS `cliente` from (`recibos` `re` join `clientes` `cli` on((`cli`.`id` = `re`.`id_cliente`)))) */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
