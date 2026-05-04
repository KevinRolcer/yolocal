# Documentación de Base de Datos - Módulo de Categorías

Este documento detalla los cambios realizados en la base de datos y las consultas principales para el módulo de Categorías modernizado.

## Cambios en la Estructura (Schema)

Para soportar el nuevo diseño de tarjetas con color y fotos/iconos personalizados, se agregaron las siguientes columnas a la tabla `categorias`.

```sql
-- Cambios aplicados a la base de datos marcoto1_yolocal
ALTER TABLE categorias 
ADD COLUMN Color VARCHAR(20) DEFAULT '#ffffff', 
ADD COLUMN Imagen VARCHAR(255) DEFAULT NULL;

-- Ajuste para permitir valores nulos en Color (si no se especifica uno)
ALTER TABLE categorias 
MODIFY COLUMN Color VARCHAR(20) DEFAULT NULL;
```

## Consultas Principales (Queries)

### 1. Listado con Filtros y Paginación
Utilizada para cargar el directorio de categorías en el panel administrativo.

```sql
-- Obtener total de registros para paginación
SELECT COUNT(*) as total FROM categorias WHERE 1=1 AND Descripcion LIKE '%busqueda%';

-- Obtener registros paginados
SELECT ID_Categoria, Descripcion, Color, Imagen 
FROM categorias 
WHERE 1=1 
AND Descripcion LIKE '%busqueda%'
ORDER BY Descripcion ASC -- Si se aplica ordenamiento
LIMIT ? OFFSET ?;
```

### 2. Obtener Categoría por ID
Utilizada para cargar los datos en el modal de edición.

```sql
SELECT ID_Categoria, Descripcion, Color, Imagen 
FROM categorias 
WHERE ID_Categoria = ?;
```

### 3. Inserción de Nueva Categoría
```sql
INSERT INTO categorias (Descripcion, Color, Imagen) 
VALUES (?, ?, ?);
```

### 4. Actualización de Categoría
```sql
UPDATE categorias 
SET Descripcion = ?, 
    Color = ?, 
    Imagen = ? 
WHERE ID_Categoria = ?;
```

### 5. Eliminación
```sql
DELETE FROM categorias 
WHERE ID_Categoria = ?;
```

---
*Documentación generada el 24 de abril de 2026 para el sistema YoLocal.*
