<?php

class PublicacionDAO {
    private mysqli $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Inserta una nueva publicación en la base de datos
     * @param Publicacion $publicacion La publicación a insertar
     * @return int|bool Devuelve el ID de la publicación insertada si tiene éxito, de lo contrario devuelve false
     */
    public function insert(Publicacion $publicacion): int|bool {
        // Prepara la consulta SQL
        if(!$stmt = $this->conn->prepare("INSERT INTO publicaciones (fecha, mensaje, idusuario) VALUES (?, ?, ?)")) {
            echo "Error al preparar la consulta insert: " . $this->conn->error;
            return false;
        }
        
        // Obtiene los datos de la publicación
        $fecha = $publicacion->getFecha();
        $mensaje = $publicacion->getMensaje();
        $idusuario = $publicacion->getIdUsuario();
        
        // Asocia los parámetros a la consulta SQL
        $stmt->bind_param('ssi', $fecha, $mensaje, $idusuario);
        
        // Ejecuta la consulta
        if($stmt->execute()) {
            return $stmt->insert_id; // Devuelve el ID de la publicación insertada
        } else {
            return false;
        }
    }

    /**
     * Elimina una publicación de la base de datos por su ID
     * @param int $id El ID de la publicación a eliminar
     * @return bool Devuelve true si se elimina correctamente, de lo contrario devuelve false
     */
    public function delete(int $id): bool {
        // Prepara la consulta SQL para eliminar la publicación por su ID
        if(!$stmt = $this->conn->prepare("DELETE FROM publicaciones WHERE idpublicacion = ?")) {
            echo "Error al preparar la consulta delete: " . $this->conn->error;
            return false;
        }
        
        // Asocia el parámetro a la consulta SQL
        $stmt->bind_param('i', $id);
        
        // Ejecuta la consulta
        return $stmt->execute();
    }

    /**
     * Obtiene una publicación de la base de datos por su ID
     * @param int $id El ID de la publicación
     * @return Publicacion|null Devuelve el objeto Publicacion si se encuentra, de lo contrario devuelve null
     */
    public function getById(int $id): ?Publicacion {
        // Prepara la consulta SQL para obtener la publicación por su ID
        if(!$stmt = $this->conn->prepare("SELECT * FROM publicaciones WHERE idpublicacion = ?")) {
            echo "Error en la SQL: " . $this->conn->error;
            return null;
        }
        
        // Asocia el parámetro a la consulta SQL
        $stmt->bind_param('i', $id);
        // Ejecuta la consulta
        $stmt->execute();
        // Obtiene el resultado
        $result = $stmt->get_result();
        
        // Comprueba si se encontró algún resultado
        if($result->num_rows >= 1) {
            $publicacion = $result->fetch_object('Publicacion'); // Convierte el resultado en objeto Publicacion
            return $publicacion;
        } else {
            return null;
        }
    }

    /**
     * Obtiene todas las publicaciones de la base de datos
     * @return array Devuelve un array de objetos Publicacion
     */
    public function getAll(): array {
        // Prepara la consulta SQL para obtener todas las publicaciones
        if(!$stmt = $this->conn->prepare("SELECT * FROM publicaciones")) {
            echo "Error en la SQL: " . $this->conn->error;
            return array();
        }
        
        // Ejecuta la consulta
        $stmt->execute();
        // Obtiene el resultado
        $result = $stmt->get_result();
        
        $publicaciones = array(); // Inicializa el array de publicaciones
        
        // Itera sobre el resultado y convierte cada fila en objeto Publicacion
        while($publicacion = $result->fetch_object('Publicacion')) {
            $publicaciones[] = $publicacion;
        }
        return $publicaciones;
    }
}