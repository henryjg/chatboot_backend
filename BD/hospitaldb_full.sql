
#
# Structure for table "categoria_pregunta"
#

DROP TABLE IF EXISTS `categoria_pregunta`;
CREATE TABLE `categoria_pregunta` (
  `cp_id` int(11) NOT NULL AUTO_INCREMENT,
  `cp_nombre` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`cp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Structure for table "categorias"
#

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Structure for table "citas"
#

DROP TABLE IF EXISTS `citas`;
CREATE TABLE `citas` (
  `citas_id` int(11) NOT NULL AUTO_INCREMENT,
  `citas_fecha` varchar(75) DEFAULT NULL,
  `citas_dni` varchar(8) DEFAULT NULL,
  `citas_nombre` varchar(255) DEFAULT NULL,
  `cita_celular` varchar(50) DEFAULT NULL,
  `citas_procedencia` varchar(255) DEFAULT NULL,
  `citas_descripcion` varchar(255) DEFAULT NULL,
  `citas_precio` varchar(50) DEFAULT NULL,
  `citas_estado` varchar(50) DEFAULT NULL,
  `citas_consultorio` varchar(255) DEFAULT NULL,
  `cita_preciogeneral` varchar(50) DEFAULT NULL,
  `cita_preciofinal` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`citas_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Structure for table "empresa"
#

DROP TABLE IF EXISTS `empresa`;
CREATE TABLE `empresa` (
  `emp_id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_nombrecorto` varchar(45) DEFAULT NULL,
  `emp_razonsocial` varchar(45) DEFAULT NULL,
  `emp_ruc` varchar(11) DEFAULT NULL,
  `emp_gerente` varchar(95) DEFAULT NULL,
  `emp_titulopagina` varchar(45) DEFAULT NULL,
  `emp_slogan` varchar(45) DEFAULT NULL,
  `emp_nosotros` longtext DEFAULT NULL,
  `emp_mision` longtext DEFAULT NULL,
  `emp_vision` longtext DEFAULT NULL,
  `emp_valores` longtext DEFAULT NULL,
  `emp_politicasprivacidad` longtext DEFAULT NULL,
  `emp_celular` varchar(45) DEFAULT NULL,
  `emp_celular2` varchar(255) DEFAULT NULL,
  `emp_direccion` varchar(625) DEFAULT NULL,
  `emp_email` varchar(255) DEFAULT NULL,
  `emp_contacto` varchar(45) DEFAULT NULL,
  `emp_metatag` varchar(245) DEFAULT NULL,
  `emp_facebook` varchar(255) DEFAULT NULL,
  `emp_instragram` varchar(255) DEFAULT NULL,
  `emp_youtube` varchar(255) DEFAULT NULL,
  `emp_pixel` varchar(255) DEFAULT NULL,
  `emp_imagendestacadaUrl` varchar(255) DEFAULT NULL,
  `emp_terminos` varchar(255) DEFAULT NULL,
  `emp_logo` varchar(255) DEFAULT NULL,
  `emp_portada` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Structure for table "formcontacto"
#

DROP TABLE IF EXISTS `formcontacto`;
CREATE TABLE `formcontacto` (
  `contacto_id` int(11) NOT NULL AUTO_INCREMENT,
  `contacto_nombre` varchar(45) DEFAULT NULL,
  `contacto_apellidos` varchar(255) DEFAULT NULL,
  `contacto_asunto` varchar(255) DEFAULT NULL,
  `contacto_correo` varchar(45) DEFAULT NULL,
  `contacto_celular` varchar(45) DEFAULT NULL,
  `contacto_mensaje` varchar(255) DEFAULT NULL,
  `contacto_fecharegistro` datetime DEFAULT NULL,
  `contacto_estado` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`contacto_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Structure for table "horarios"
#

DROP TABLE IF EXISTS `horarios`;
CREATE TABLE `horarios` (
  `hora_id` int(11) NOT NULL AUTO_INCREMENT,
  `hora_fechainicio` time DEFAULT NULL,
  `hora_fechafin` time DEFAULT NULL,
  PRIMARY KEY (`hora_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Structure for table "horadiacita"
#

DROP TABLE IF EXISTS `horadiacita`;
CREATE TABLE `horadiacita` (
  `hdc_id` int(11) NOT NULL AUTO_INCREMENT,
  `hdc_horarioId` int(11) DEFAULT 0,
  `hdc_citaId` int(11) DEFAULT 0,
  PRIMARY KEY (`hdc_id`),
  KEY `horarioId` (`hdc_horarioId`),
  KEY `citaId` (`hdc_citaId`),
  CONSTRAINT `citaId` FOREIGN KEY (`hdc_citaId`) REFERENCES `citas` (`citas_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `horarioId` FOREIGN KEY (`hdc_horarioId`) REFERENCES `horarios` (`hora_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Structure for table "mensajes"
#

DROP TABLE IF EXISTS `mensajes`;
CREATE TABLE `mensajes` (
  `mensaje_id` int(11) NOT NULL AUTO_INCREMENT,
  `mensaje_mensaje` varchar(445) DEFAULT NULL,
  `mensaje_accion` varchar(45) DEFAULT NULL,
  `mensaje_fechaaccion` date DEFAULT NULL,
  `mensaje_fecharegistro` datetime DEFAULT NULL,
  `mensaje_asesorId` int(11) DEFAULT NULL,
  `mensaje_clienteId` int(11) DEFAULT NULL,
  `mensaje_estado` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`mensaje_id`),
  KEY `mensaje_clienteId` (`mensaje_clienteId`),
  CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`mensaje_clienteId`) REFERENCES `cliente` (`cliente_id`)
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Structure for table "noticias"
#

DROP TABLE IF EXISTS `noticias`;
CREATE TABLE `noticias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `subtitulo` varchar(255) DEFAULT NULL,
  `url_ImagenDestacada` varchar(255) DEFAULT NULL,
  `curpohtml_box1` text DEFAULT NULL,
  `url_Imagen2` varchar(255) DEFAULT NULL,
  `curpohtml_box2` text DEFAULT NULL,
  `url_Imagen3` varchar(255) DEFAULT NULL,
  `url_video` varchar(255) DEFAULT NULL,
  `seoMetatag` varchar(255) DEFAULT NULL,
  `seoDescripcion` text DEFAULT NULL,
  `fechaRegistro` datetime DEFAULT current_timestamp(),
  `estado` varchar(15) DEFAULT NULL,
  `noticia_foto` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_noticias_foto_empresa` (`noticia_foto`),
  CONSTRAINT `FK_noticias_foto_empresa` FOREIGN KEY (`noticia_foto`) REFERENCES `foto_empresa` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Structure for table "preguntas_frecuentes"
#

DROP TABLE IF EXISTS `preguntas_frecuentes`;
CREATE TABLE `preguntas_frecuentes` (
  `pf_if` int(11) NOT NULL AUTO_INCREMENT,
  `pf_descripcion` varchar(255) DEFAULT NULL,
  `pf_respuesta` varchar(255) DEFAULT NULL,
  `pf_infoAdicional` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pf_if`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Structure for table "servicio"
#

DROP TABLE IF EXISTS `servicio`;
CREATE TABLE `servicio` (
  `servicio_id` int(11) NOT NULL AUTO_INCREMENT,
  `servicio_categoria` int(11) NOT NULL DEFAULT 0,
  `servicio_nombre` varchar(255) NOT NULL DEFAULT '0',
  `servicio_descripcion` varchar(255) NOT NULL DEFAULT '0',
  `servicio_beneficios` varchar(255) NOT NULL DEFAULT '0',
  `servicio_precio` varchar(255) NOT NULL DEFAULT '0',
  `servicio_facilidades` varchar(255) NOT NULL DEFAULT '0',
  `servicio_video1` varchar(255) NOT NULL DEFAULT '0',
  `servicio_video2` varchar(255) NOT NULL DEFAULT '0',
  `servicio_info_adicional` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`servicio_id`),
  KEY `FK_servicio_categorias` (`servicio_categoria`),
  CONSTRAINT `FK_servicio_categorias` FOREIGN KEY (`servicio_categoria`) REFERENCES `categorias` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Structure for table "foto_servicio"
#

DROP TABLE IF EXISTS `foto_servicio`;
CREATE TABLE `foto_servicio` (
  `foto_id` int(11) NOT NULL AUTO_INCREMENT,
  `foto_url` varchar(255) DEFAULT NULL,
  `foto_nombre` varchar(255) DEFAULT NULL,
  `foto_orden` varchar(15) DEFAULT NULL,
  `foto_servicio` int(11) DEFAULT NULL,
  PRIMARY KEY (`foto_id`),
  KEY `foto_servicio` (`foto_servicio`),
  CONSTRAINT `foto_servicio` FOREIGN KEY (`foto_servicio`) REFERENCES `servicio` (`servicio_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#
# Structure for table "trabajador"
#

DROP TABLE IF EXISTS `trabajador`;
CREATE TABLE `trabajador` (
  `tra_id` int(11) NOT NULL AUTO_INCREMENT,
  `tra_dni` varchar(8) DEFAULT NULL,
  `tra_nombre` varchar(150) DEFAULT NULL,
  `tra_apellido` varchar(255) DEFAULT NULL,
  `tra_email` varchar(150) DEFAULT NULL,
  `tra_cargo` varchar(155) DEFAULT NULL,
  `tra_supervisor_id` varchar(50) DEFAULT NULL,
  `tra_telefono` varchar(45) DEFAULT NULL,
  `tra_celular` varchar(45) DEFAULT NULL,
  `tra_fotourl` varchar(255) NOT NULL,
  `tra_eslider` tinyint(4) DEFAULT NULL,
  `tra_liderId` int(11) DEFAULT NULL,
  `tra_rol` varchar(45) DEFAULT NULL,
  `tra_user` varchar(45) DEFAULT NULL,
  `tra_pass` varchar(255) DEFAULT NULL,
  `tra_estado` varchar(255) DEFAULT NULL,
  `tra_empresaId` int(11) NOT NULL,
  `tra_fechareg` datetime DEFAULT NULL,
  `tra_fnacimiento` date DEFAULT NULL,
  PRIMARY KEY (`tra_id`),
  KEY `fk_trabajador_empresa1_idx` (`tra_empresaId`),
  CONSTRAINT `fk_trabajador_empresa1` FOREIGN KEY (`tra_empresaId`) REFERENCES `empresa` (`emp_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

#
# Structure for table "ubdepartamento"
#

DROP TABLE IF EXISTS `ubdepartamento`;
CREATE TABLE `ubdepartamento` (
  `idDepa` int(5) NOT NULL DEFAULT 0,
  `departamento` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idDepa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

#
# Structure for table "ubprovincia"
#

DROP TABLE IF EXISTS `ubprovincia`;
CREATE TABLE `ubprovincia` (
  `idProv` int(5) NOT NULL DEFAULT 0,
  `provincia` varchar(50) DEFAULT NULL,
  `idDepa` int(5) DEFAULT NULL,
  PRIMARY KEY (`idProv`),
  KEY `fk_provincia_departamento` (`idDepa`),
  CONSTRAINT `fk_provincia_departamento` FOREIGN KEY (`idDepa`) REFERENCES `ubdepartamento` (`idDepa`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

#
# Structure for table "ubdistrito"
#

DROP TABLE IF EXISTS `ubdistrito`;
CREATE TABLE `ubdistrito` (
  `idDist` int(5) NOT NULL DEFAULT 0,
  `distrito` varchar(50) DEFAULT NULL,
  `idProv` int(5) DEFAULT NULL,
  PRIMARY KEY (`idDist`),
  KEY `fk_provincia_distrito` (`idProv`),
  CONSTRAINT `fk_provincia_distrito` FOREIGN KEY (`idProv`) REFERENCES `ubprovincia` (`idProv`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
