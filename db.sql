
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

-- 7. TABLA: NOTIFICACIONES_INTERMEDIACION (Control regulador de alertas para el Ministerio)
CREATE TABLE notificaciones_intermediacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    origen ENUM('empleador', 'buscador') NOT NULL,  -- Quién inició la acción de contacto
    buscador_id INT NOT NULL,                        -- El desempleado implicado
    oferta_id INT NULL,                              -- La oferta vinculada (NULL si es búsqueda directa de perfil)
    empleador_id INT NOT NULL,                       -- La empresa implicada
    mensaje_motivo TEXT NULL,                        -- Notas aclaratorias para el administrador estatal
    estado_ministerio ENUM('pendiente', 'en_revision', 'aprobado', 'rechazado') DEFAULT 'pendiente',
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
