-- 1. TABLA: USUARIOS (Centraliza credenciales y datos personales clave)
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

-- 2. TABLA: BUSCADORES_EMPLEO (Perfil específico del desempleado)
CREATE TABLE buscadores_empleo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    estado_civil VARCHAR(30) NOT NULL,
    foto_carnet VARCHAR(255) NOT NULL,               -- Ruta de la foto en el servidor
    provincia VARCHAR(50) NOT NULL,
    distrito VARCHAR(50) NOT NULL,
    ciudad_municipio VARCHAR(50) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- 3. TABLA: DOCUMENTOS (Columnas específicas para tus archivos requeridos)
CREATE TABLE documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    copia_dip VARCHAR(255) NOT NULL,                 -- Ruta del archivo DIP escaneado
    cv VARCHAR(255) NOT NULL,                        -- Ruta del archivo Currículum
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

-- 5. TABLA: EMPLEADORES (Perfil de empresas que publicarán ofertas)
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

-- 6. TABLA: CURSOS (Formaciones disponibles o tomadas por los usuarios)
CREATE TABLE cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo_curso VARCHAR(150) NOT NULL,
    descripcion_curso TEXT NOT NULL,
    entidad_imparte VARCHAR(150) NOT NULL,          -- Ej: INEM, Centro de Formación, etc.
    duracion_horas INT NOT NULL,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    estado ENUM('activo', 'finalizado', 'proximamente') DEFAULT 'activo'
);