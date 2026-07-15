
drop database if exists ministerio_trabajo;
create database ministerio_trabajo;
use ministerio_trabajo;



-- =========================================================================
-- SCRIPT GENERAL DE LA BASE DE DATOS: PORTAL DE EMPLEO INTERMEDIADO
-- MINISTERIO DE TRABAJO Y FOMENTO DE EMPLEO - GUINEA ECUATORIAL
-- =========================================================================

-- 1. TABLA: USUARIOS (Centraliza credenciales, verificación de correo y roles institucionales)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_expediente VARCHAR(15) NOT NULL UNIQUE,   -- Nueva columna para el expediente único (EG-XXXXX)
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    correo_electronico VARCHAR(150) NOT NULL UNIQUE, 
    documento_identidad VARCHAR(30) NOT NULL UNIQUE, 
    password VARCHAR(255) NOT NULL,                  
    
    rol ENUM('buscador', 'empleador', 'ministerio', 'administrador') DEFAULT 'buscador',
    
    correo_verificado TINYINT(1) DEFAULT 0,          
    token_verificacion VARCHAR(100) NULL,            
    
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO `usuarios` (`id`, `numero_expediente`, `nombre`, `apellidos`, `nombre_usuario`, `correo_electronico`, `documento_identidad`, `password`, `rol`, `correo_verificado`, `token_verificacion`, `fecha_registro`) VALUES
(1, 'EG-12449', 'salvador', 'METE BIJERI', 'Mh123', 'salvadormete2@gmail.com', '000897645', '$2y$10$W0A5bbpCxnQDg8yOldPTluW8Dwg7WM4RyqApbjTlfzL81lhSD0xnO', 'buscador', 1, '84b698263584569dee585f43f39ab9a2addc36b79d3fe85b5e79e208f180b3a9', '2026-07-11 15:03:55'),
(2, 'EG-49094', 'minerva', 'PABITA', 'mnerva12', 'minerva@prueba.com', '763432', '$2y$10$4eQoQ20sUCn1uMz1QObMO.Ym3ad6fjlQxcHcYyzkN8yXp1QBQT9ji', 'administrador', 1, 'de76b750ac0f052dffe1b9da7879d939bede604f77415a4940697f03ae46d545', '2026-07-12 06:38:23'),
(3, 'EG-32225', 'salvador', 'METE BIJERI', 'Marketing', 'mpprogramacion22@gmail.com', '234321', '$2y$10$4PXHiZ5MeawIJJP.hrXBeOmyvKaUjapCrDQwkWkcb2TGt8owm3utm', 'empleador', 1, '0a825a549a9ccde52687a306d3aff8290445c3e239d0d171308020fc7ce79127', '2026-07-12 14:11:30');


-- 2. TABLA: BUSCADORES_EMPLEO (Perfil específico del ciudadano desempleado)
-- 2. TABLA: BUSCADORES_EMPLEO (Perfil específico con control de estado laboral)
CREATE TABLE buscadores_empleo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    estado_civil VARCHAR(30) NOT NULL,
    foto_carnet VARCHAR(255) NOT NULL,
    provincia VARCHAR(50) NOT NULL,
    distrito VARCHAR(50) NOT NULL,
    ciudad_municipio VARCHAR(50) NOT NULL,
    -- NUEVA COLUMNA DE FACULTAD PARA EL MINISTERIO:
    estado_laboral ENUM('desempleado', 'contratado', 'suspendido') DEFAULT 'desempleado',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

INSERT INTO `buscadores_empleo` (`id`, `usuario_id`, `telefono`, `estado_civil`, `foto_carnet`, `provincia`, `distrito`, `ciudad_municipio`, `estado_laboral`) VALUES
(2, 1, '+240222478702', 'soltero', 'uploads/fotos/foto_1_1783861891.png', 'bioko_norte', 'malabo', 'malabo', 'desempleado');


-- 3. TABLA: DOCUMENTOS (Expediente digital de archivos del desempleado)
CREATE TABLE documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    copia_dip VARCHAR(255) NOT NULL,                 -- Ruta del archivo DIP escaneado (Obligatorio)
    cv VARCHAR(255) NOT NULL,                        -- Ruta del archivo Currículum (Obligatorio)
    titulos VARCHAR(255) NULL,                       -- Opcional
    otros_documentos VARCHAR(255) NULL,              -- Opcional
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

INSERT INTO `documentos` (`id`, `usuario_id`, `copia_dip`, `cv`, `titulos`, `otros_documentos`, `fecha_subida`) VALUES
(1, 1, 'uploads/documentos/copia_dip_1_1783861891.pdf', 'uploads/documentos/cv_1_1783861891.pdf', 'uploads/documentos/titulos_1_1783861891.pdf', 'uploads/documentos/otros_documentos_1_1783861891.pdf', '2026-07-12 13:11:31');



-- 4. TABLA: EXPERIENCIA_LABORAL (Historial dinámico de empleos pasados)
CREATE TABLE experiencia_laboral (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    empresa VARCHAR(150) NOT NULL,
    puesto VARCHAR(100) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NULL,                             -- Puede ser NULL si sigue trabajando ahí
    descripcion TEXT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

INSERT INTO `experiencia_laboral` (`id`, `usuario_id`, `empresa`, `puesto`, `fecha_inicio`, `fecha_fin`, `descripcion`) VALUES
(1, 1, 'CISCO', 'Tecnico de Redes', '2024-01-01', '2026-07-01', '- monitoreo de redes\r\n- analisis y diseño de redes.');


-- 5. TABLA: EMPLEADORES (Perfil corporativo de empresas)
CREATE TABLE empleadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre_empresa VARCHAR(150) NOT NULL,
    rnc_ruc VARCHAR(50) UNIQUE NULL,                 -- Registro oficial de la empresa si aplica
    sector_industrial VARCHAR(100) NOT NULL,         -- Ej: Telecomunicaciones, Petróleo, Comercio
    telefono_corporativo VARCHAR(20) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    sitio_web VARCHAR(150) NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

INSERT INTO `empleadores` (`id`, `usuario_id`, `nombre_empresa`, `rnc_ruc`, `sector_industrial`, `telefono_corporativo`, `direccion`, `sitio_web`) VALUES
(1, 3, 'MP MARKETING AND SOLUTIONS', 'RNC-67432', 'Comercio y Servicios', '222221444', 'Perez, detras de la feria', 'https://mpmarketing.net');


-- 6. TABLA: OFERTAS_EMPLEO (Vacantes publicadas por las empresas)
CREATE TABLE ofertas_empleo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empleador_id INT NOT NULL,                       -- Quién publica la oferta
    titulo_puesto VARCHAR(150) NOT NULL,            -- Ej: Contable, Conductor de camión
    descripcion TEXT NOT NULL,
    requisitos TEXT NOT NULL,
    provincia VARCHAR(50) NOT NULL,
    salario_ofrecido DECIMAL(10,2) NULL,             -- Opcional
    estado ENUM('abierta', 'cerrada') DEFAULT 'abierta',
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (empleador_id) REFERENCES empleadores(id) ON DELETE CASCADE
);

INSERT INTO `ofertas_empleo` (`id`, `empleador_id`, `titulo_puesto`, `descripcion`, `requisitos`, `provincia`, `salario_ofrecido`, `estado`, `fecha_publicacion`) VALUES
(1, 1, 'Infraestructuras IT', 'Un puesto de Infraestructura de TI (IT Infrastructure) se encarga del diseño, implementación y mantenimiento de la base tecnológica de una empresa. El rol garantiza la disponibilidad, seguridad y el rendimiento continuo de los servidores, redes, sistemas de almacenamiento y plataformas en la nube.', 'Requisitos del PerfilFormación Académica: Licenciatura o Grado universitario en Ingeniería en Sistemas, Informática o Ciencias de la Computación.Experiencia: Por lo general, se solicitan entre 2 y 5 años de experiencia comprobable en administración de redes, soporte de sistemas o ingeniería de infraestructura.Conocimientos Técnicos:Dominio de sistemas operativos Windows y Linux.Protocolos de red (TCP/IP, DNS, DHCP, Active Directory).Tecnologías de virtualización y plataformas en la nube.Herramientas de monitoreo y mitigación de riesgos.Habilidades Personales: Capacidad analítica para la resolución de problemas complejos, gestión del tiempo y fuertes habilidades de comunicación.Certificaciones (Valoradas como un plus):Redes: Cisco CCNASistemas: Microsoft Certified (MCSE/Azure)Metodología: ITIL Foundation', 'Bioko Norte', 465000.00, 'abierta', '2026-07-12 17:18:09');


-- 7. TABLA: NOTIFICACIONES_INTERMEDIACION (Control regulador de alertas para el Ministerio)
CREATE TABLE notificaciones_intermediacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    origen ENUM('empleador', 'buscador') NOT NULL,  -- Quién inició la acción de contacto
    buscador_id INT NOT NULL,                        -- El desempleado implicado
    oferta_id INT NULL,                              -- La oferta vinculada (NULL si es búsqueda directa de perfil)
    empleador_id INT NOT NULL,                       -- La empresa implicada
    mensaje_motivo TEXT NULL,                        -- Notas aclaratorias para el administrador estatal
    estado_ministerio ENUM('pendiente', 'en_revision', 'aprobado', 'rechazado','contratado') DEFAULT 'pendiente',
    codigo_seguimiento VARCHAR(20) NOT NULL UNIQUE,  -- Código identificador (Ej: MITRAD-2026-X9)
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (buscador_id) REFERENCES buscadores_empleo(id) ON DELETE CASCADE,
    FOREIGN KEY (empleador_id) REFERENCES empleadores(id) ON DELETE CASCADE,
    FOREIGN KEY (oferta_id) REFERENCES ofertas_empleo(id) ON DELETE CASCADE
);

-- 8. TABLA: FAVORITOS (Ofertas guardadas temporalmente por los desempleados)
CREATE TABLE favoritos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    buscador_id INT NOT NULL,
    oferta_id INT NOT NULL,
    fecha_guardado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(buscador_id, oferta_id),                  -- Bloquea duplicados de una misma oferta por usuario
    FOREIGN KEY (buscador_id) REFERENCES buscadores_empleo(id) ON DELETE CASCADE,
    FOREIGN KEY (oferta_id) REFERENCES ofertas_empleo(id) ON DELETE CASCADE
);

-- =========================================================================
-- 1. NUEVA TABLA: ENTIDADES_FORMADORAS
-- =========================================================================
CREATE TABLE entidades_formadoras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_entidad VARCHAR(20) NOT NULL UNIQUE,          -- Ej: ENT-INEM-01, ENT-UNGE-02
    nombre_entidad VARCHAR(150) NOT NULL,                -- Ej: Instituto Nacional de Empleo (INEM)
    siglas VARCHAR(20) NULL,                             -- Ej: INEM, UNGE, AAUCA
    tipo_entidad ENUM('publica', 'privada', 'ong', 'internacional') DEFAULT 'publica',
    responsable_contacto VARCHAR(150) NULL,              -- Nombre de la persona o director de contacto
    telefono VARCHAR(30) NULL,
    correo_electronico VARCHAR(150) NULL,
    direccion TEXT NULL,                                 -- Dirección física o sede
    provincia VARCHAR(100) DEFAULT 'Bioko Norte',        -- Ubicación principal
    estado ENUM('activo', 'inactivo', 'suspendido') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO `entidades_formadoras` (`id`, `codigo_entidad`, `nombre_entidad`, `siglas`, `tipo_entidad`, `responsable_contacto`, `telefono`, `correo_electronico`, `direccion`, `provincia`, `estado`, `fecha_registro`) VALUES
(3, 'ENT-001', 'Eulogio OYO RIQUEZA', 'EOR', 'publica', 'Secretaria', '222346543', 'eulogiooyo34@gmail.com', 'buena esperanza 2', 'Bioko Norte', 'activo', '2026-07-11 19:35:22'),
(4, 'ENT-004', 'Carlos Luanga', 'CL', 'publica', 'Secretaria', '222346543', 'eulogiooyo34@gmail.com', 'al lado del centro cultural Frances', 'Litoral', 'activo', '2026-07-11 19:36:27');


-- =========================================================================
-- 2. TABLA CURSOS REFACTORIZADA (VINCULADA A ENTIDADES_FORMADORAS)
-- =========================================================================
-- Eliminamos el campo VARCHAR 'entidad_imparte' y agregamos la FK 'entidad_id'

DROP TABLE IF EXISTS cursos;

CREATE TABLE cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_curso VARCHAR(20) NOT NULL UNIQUE,            -- Ej: CUR-2026-001
    titulo_curso VARCHAR(150) NOT NULL,
    descripcion_curso TEXT NOT NULL,
    entidad_id INT NOT NULL,                             -- Clave foránea hacia entidades_formadoras
    duracion_horas INT NOT NULL,
    modalidad ENUM('presencial', 'online', 'hibrido') DEFAULT 'presencial',
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    cupos_maximos INT DEFAULT 30,
    estado ENUM('activo', 'finalizado', 'proximamente') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (entidad_id) REFERENCES entidades_formadoras(id) ON DELETE CASCADE
);

ALTER TABLE cursos 
ADD COLUMN imagen_portada VARCHAR(255) DEFAULT 'img/cursos/default.jpg' AFTER descripcion_curso;


INSERT INTO `cursos` (`id`, `codigo_curso`, `titulo_curso`, `descripcion_curso`, `imagen_portada`, `entidad_id`, `duracion_horas`, `modalidad`, `fecha_inicio`, `fecha_fin`, `cupos_maximos`, `estado`, `fecha_creacion`) VALUES
(1, 'CUR-2026-AE6D', 'Redes Informaticas', 'El curso de Redes Informáticas está diseñado para proporcionar una formación sólida e integral en los principios fundamentales que rigen la comunicación de datos y el interconectado de sistemas digitales. A lo largo del programa, los estudiantes exploran desde las bases teóricas de la transmisión de datos hasta la configuración, diseño y gestión práctica de arquitecturas de red locales y distribuidas.Este curso abarca el estudio detallado de los modelos de referencia (OSI y TCP/IP), el direccionamiento e interconexión mediante subredes (IPv4 e IPv6), el funcionamiento de los protocolos clave en cada capa de la red, así como la implementación de mecanismos esenciales de seguridad y enrutamiento en entornos empresariales.🎯 Objetivos de AprendizajeAl finalizar el curso, los participantes serán capaces de:Comprender la estructura y el funcionamiento de las redes LAN, WAN y WLAN.Dominar la pila de protocolos TCP/IP y el modelo OSI para el diagnóstico y resolución de fallos (troubleshooting).Diseñar e implementar esquemas de direccionamiento IP eficientes mediante subnets (VLSM).Configurar dispositivos clave de infraestructura, tales como routers, switches y puntos de acceso inalámbricos.Aplicar buenas prácticas de seguridad informática para proteger la integridad y disponibilidad del tráfico de datos.📊 Estructura del TemarioMóduloContenido Principal1. Introducción a la RedesTopologías de red, medios de transmisión (cobre, fibra, radiofrecuencia) y modelos OSI vs. TCP/IP.2. Capa de Enlace y ConmutaciónConmutación Ethernet, direcciones MAC, configuración de switches y redes virtuales (VLANs).3. Redes e Interconexión (IP)Protocolo IP (IPv4/IPv6), máscaras de red, subneteado (VLSM) y enrutamiento estático y dinámico.4. Capa de Transporte y AplicaciónProtocolos TCP y UDP, servicios fundamentales (DNS, DHCP, HTTP/HTTPS, FTP, SSH).5. Seguridad y MantenimientoControl de acceso (ACLs), firewalls, redes VPN, monitoreo de tráfico y resolución de incidencias.💼 Perfil del Estudiante y Salidas Profesionales¿A quién va dirigido?: Estudiantes de informática, técnicos de soporte, administradores de sistemas junior y cualquier profesional del sector tecnológico que busque afianzar sus bases en infraestructura y comunicaciones.Impacto profesional: Prepara al estudiante para asumir roles como Técnico de Soporte de Redes, Administrador Junior de Infraestructura IT, o como base fundamental para certificaciones de la industria (como Cisco CCNA o CompTIA Network+).', 'uploads/img/cursos/curso_1783836727_6a5330371f24c.jpg', 3, 90, 'presencial', '2026-08-01', '2029-08-01', 30, 'activo', '2026-07-11 20:47:58');




-- TABLA: POSTULACIONES (Registro de candidaturas a ofertas)
CREATE TABLE postulaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    oferta_id INT NOT NULL,
    buscador_id INT NOT NULL,
    mensaje_presentacion TEXT NULL,                     -- Carta o nota del desempleado (opcional)
    estado ENUM('pendiente', 'revisado', 'interesado', 'rechazado') DEFAULT 'pendiente',
    fecha_postulacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(oferta_id, buscador_id),                     -- Un desempleado solo puede postularse 1 vez a la misma oferta
    FOREIGN KEY (oferta_id) REFERENCES ofertas_empleo(id) ON DELETE CASCADE,
    FOREIGN KEY (buscador_id) REFERENCES buscadores_empleo(id) ON DELETE CASCADE
);




DROP TABLE IF EXISTS notificaciones_intermediacion;

CREATE TABLE notificaciones_intermediacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    postulacion_id INT NULL,                            -- Enlace directo a la postulación que originó el trámite
    buscador_id INT NOT NULL,
    empleador_id INT NOT NULL,
    oferta_id INT NULL,
    
    -- Estados del trámite ministerial
    estado_ministerio ENUM('pendiente', 'en_revision', 'aprobado', 'rechazado', 'contratado') DEFAULT 'pendiente',
    
    -- Datos de la credencial / Pase emitido por el Ministerio
    codigo_seguimiento VARCHAR(30) NOT NULL UNIQUE,     -- Ej: MITRAD-2026-X9
    numero_credencial VARCHAR(50) NULL UNIQUE,          -- Código oficial generado al ser aprobado
    fecha_emision_credencial DATETIME NULL,
    
    motivo_empresa TEXT NULL,                            -- Por qué la empresa está interesada en este perfil
    observaciones_ministerio TEXT NULL,                 -- Notas de resolución del funcionario
    
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (postulacion_id) REFERENCES postulaciones(id) ON DELETE SET NULL,
    FOREIGN KEY (buscador_id) REFERENCES buscadores_empleo(id) ON DELETE CASCADE,
    FOREIGN KEY (empleador_id) REFERENCES empleadores(id) ON DELETE CASCADE,
    FOREIGN KEY (oferta_id) REFERENCES ofertas_empleo(id) ON DELETE CASCADE
);



CREATE TABLE credenciales_empleo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notificacion_id INT NOT NULL,                     -- Trámite de origen
    buscador_id INT NOT NULL,                         -- Beneficiario (Empleado)
    numero_credencial VARCHAR(50) NOT NULL UNIQUE,     -- Ej: CRED-2026-XXXXX
    codigo_qr_verificacion VARCHAR(255) NULL,         -- Enlace o hash para verificar autenticidad
    fecha_emision TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_vencimiento DATE NOT NULL,                  -- Vigencia (Ej: 6 meses o 1 año)
    estado ENUM('activa', 'vencida', 'revocada') DEFAULT 'activa',
    
    FOREIGN KEY (notificacion_id) REFERENCES notificaciones_intermediacion(id) ON DELETE CASCADE,
    FOREIGN KEY (buscador_id) REFERENCES buscadores_empleo(id) ON DELETE CASCADE
);



DELIMITER //

CREATE TRIGGER generar_credencial_despues_de_aprobar
AFTER UPDATE ON notificaciones_intermediacion
FOR EACH ROW
BEGIN
    -- Verificar si el estado cambió a 'aprobado'
    IF NEW.estado_ministerio = 'aprobado' AND OLD.estado_ministerio != 'aprobado' THEN
        
        -- Insertar la credencial calculando 1 meses de vigencia
        INSERT INTO credenciales_empleo (
            notificacion_id, 
            buscador_id, 
            numero_credencial, 
            fecha_vencimiento, 
            estado
        ) VALUES (
            NEW.id, 
            NEW.buscador_id, 
            NEW.numero_credencial, 
            DATE_ADD(CURRENT_DATE(), INTERVAL 6 MONTH), 
            'activa'
        );
        
    END IF;
END //

DELIMITER ;