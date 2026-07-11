-- =========================================================================
-- SCRIPT GENERAL DE LA BASE DE DATOS: PORTAL DE EMPLEO INTERMEDIADO
-- MINISTERIO DE TRABAJO Y FOMENTO DE EMPLEO - GUINEA ECUATORIAL
-- =========================================================================

-- 1. TABLA: USUARIOS (Centraliza credenciales de acceso para todos los roles)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    documento_identidad VARCHAR(30) NOT NULL UNIQUE, -- DIP o Pasaporte
    password VARCHAR(255) NOT NULL,                  -- Contraseña encriptada
    rol ENUM('buscador', 'empleador', 'administrador') DEFAULT 'buscador',
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

-- 9. TABLA: CURSOS (Oferta informativa de capacitación del Estado)
CREATE TABLE cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo_curso VARCHAR(150) NOT NULL,
    descripcion_curso TEXT NOT NULL,
    entidad_imparte VARCHAR(150) NOT NULL,          -- Ej: INEM, Centros Técnicos, etc.
    duracion_horas INT NOT NULL,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    estado ENUM('activo', 'finalizado', 'proximamente') DEFAULT 'activo'
);