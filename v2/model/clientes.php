<?php
/**
 * clase clientes
 * se realiza la conexión con la base
 * y las consultas
 * se devuelven los datos
 */

include_once("utils.php");

class Clientes {
    const NOMBRE_TABLA = "clientes";
    const ID = "id";
    const NOMBRE = "nombre";
    const APELLIDOS = "apellidos";
    const TELEFONO = "telefono";
    const EMAIL = "email";
    const DETALLE = "detalle";


    /**
     * Busca un cliente por su id
     * @param $id id del cliente a buscar
     * @return respuesta con el resultado de la consulta: datos del cliente o error
     */
    public static function getClientById($id){
        //realiza la conexion
        $dbConn = Conexion::connect();
        $resultado = array();
        try{
            //prepara la sentencia
            $sql = $dbConn->prepare("SELECT * FROM " . self::NOMBRE_TABLA . " WHERE " . self::ID . " =?");
            //relaciona los parámetros
            $sql->bindParam(1, $id);

            //ejecuta la sentencia preparada
            $sql->execute();
            $respuesta = $sql->fetch(PDO::FETCH_ASSOC);

            if($respuesta){
                //guarda los datos en el array
                $resultado['cabecera'] = "HTTP/1.1 200 OK";
                $resultado['datos'] = $respuesta;
            }else{
                //no se ha encontrado el cliente
                $resultado['cabecera'] = "HTTP/1.1 404 Not Found";
                $resultado['datos'] = "No encontrado";
            }

        }catch (PDOException $e) {
            //se produjo un error
            $resultado['cabecera'] = "HTTP/1.1 500 Internal Server Error";
            $resultado['datos'] = $e->getMessage();
        }
        //devuelve el resultado
        return $resultado;
    }


    /**
     * Devuelve todos los clientes
     * @return lista con todos los clientes o error
     */
    public static function getAllClients(){
        //realiza la conexion
        $dbConn = Conexion::connect();
        $resultado = array();
        try{
            //prepara la sentencia
            $sql = $dbConn->prepare("SELECT * FROM " . self::NOMBRE_TABLA);
            //ejecuta la sentencia
            $sql->execute();

            $sql->setFetchMode(PDO::FETCH_ASSOC);

            $respuesta = $sql->fetchAll();

            if($respuesta){
                //guarda los datos en el array
                $resultado['cabecera'] = "HTTP/1.1 200 OK";
                $resultado['datos'] = $respuesta;
            }else{
                //no hay datos
                $resultado['cabecera'] = "HTTP/1.1 404 Not Found";
                $resultado['datos'] = "No encontrado";
            }

        }catch (PDOException $e) {
            //se produjo un error
            $resultado['cabecera'] = "HTTP/1.1 500 Internal Server Error";
            $resultado['datos'] = $e->getMessage();
        }

        //devuelve el resultado
        return $resultado;
    }

    /**
     * Inserta nuevo cliente
     * @param $cliente array con los datos del nuevo cliente
     * @return id del cliente insertado o error
     */
    public static function setNewCliente($cliente){
        //realiza la conexion
        $dbConn = Conexion::connect();
        $resultado = array();
        try{
            //crea la consulta
            $sql = "INSERT INTO " . self::NOMBRE_TABLA . " (" . self::NOMBRE . ", " . self::APELLIDOS . ", " . self::TELEFONO . ", " . self::EMAIL . ", " . self::DETALLE . ")
                    VALUES (?,?,?,?,?)";

            //prepara la sentencia
            $statement = $dbConn->prepare($sql);

            //relaciona los parámetros
            $statement->bindParam(1, $cliente['nombre']);
            $statement->bindParam(2, $cliente['apellidos']);
            $statement->bindParam(3, $cliente['telefono']);
            $statement->bindParam(4, $cliente['email']);
            $statement->bindParam(5, $cliente['detalle']);

            //ejecuta la sentencia
            $statement->execute();

            //rescatamos el id del cliente insertado
            $clienteId = $dbConn->lastInsertId();

            if($clienteId){
                $input['id'] = $clienteId;
                //guarda el resultado en el array
                $resultado['cabecera'] = "HTTP/1.1 200 OK";
                $resultado['id'] = $input;
            }
        }catch (PDOException $e) {
            //se produjo un error
            $resultado['cabecera'] = "HTTP/1.1 500 Internal Server Error";
            $resultado['id'] = $e->getMessage();
        }
        //devuelve el resultado
        return $resultado;
    }

    /**
     * Elimina un cliente por su id
     * @param $datos con el id
     * @return resultado de la eliminación
     */
    public static function deleteCliente($datos){
        //realiza la conexion
        $dbConn = Conexion::connect();
        $resultado = array();

        if(isset($datos['id'])){
            $id = $datos['id'];
            try{
                //prepara la consulta
                $statement = $dbConn->prepare("DELETE FROM " . self::NOMBRE_TABLA . " WHERE " . self::ID . "=?");

                //relaciona los parámetros
                $statement->bindParam(1, $id);

                //ejecuta la sentencia
                $statement->execute();

                //comprobamos el número de filas que se han borrado
                $registros = $statement->rowCount();

                //guarda el resultado en el array
                $resultado['cabecera'] = "HTTP/1.1 200 OK";
                $resultado['Registros eliminados'] = $registros;
            }catch (PDOException $e) {
                //se produjo un error
                $resultado['cabecera'] = "HTTP/1.1 500 Internal Server Error";
                $resultado['Registros eliminados'] = $e->getMessage();
            } 
        }else{
            //falta el id
            $resultado['cabecera'] = "HTTP/1.1 400 Bad Request";
            $resultado['Registros eliminados'] = "Solicitud incorrecta";
        }
        //devuelve el resultado
        return $resultado;

    }

    /**
     * Actualiza un cliente relacionado con un id de la base de datos 
     * @param $datos nuevos datos
     * @return resultado de la modificación
     */
    public static function modificaCliente($datos){
        $resultado = array();
            //comprueba si vienen todos los datos necesarios para la modificación
            if(self::datosCorrectos($datos)){
                $nombre = $datos['nombre'];         
                $apellidos = $datos['apellidos'];
                $telefono = $datos['telefono'];
                $email = $datos['email'];
                $detalle = $datos['detalle'];
                $id = $datos['id'];
            }else{
                //faltan datos
                $resultado['cabecera'] = "HTTP/1.1 422 Unprocessable Entity";
                $resultado['Registros modificados'] = "Solicitud incorrecta";
                return $resultado;
            }

        //realiza la conexion
        $dbConn = Conexion::connect();

        try{
            //crea la consulta
            $sql = "UPDATE " . self::NOMBRE_TABLA . "
                    SET " . self::NOMBRE . " = ?,
                    " . self::APELLIDOS . " = ?,
                    " . self::TELEFONO . " = ?,
                    " . self::EMAIL . " = ?,
                    " . self::DETALLE . " = ?
                    WHERE " . self::ID . " = ?";
            //prepara la sentencia
            $statement = $dbConn->prepare($sql);

            //relaciona los parámetros
            $statement->bindParam(1, $nombre);
            $statement->bindParam(2, $apellidos);
            $statement->bindParam(3, $telefono);
            $statement->bindParam(4, $email);
            $statement->bindParam(5, $detalle);
            $statement->bindParam(6, $id);

            //ejecuta la consulta
            $statement->execute();

            //comprueba cuantos registros han sido modificados
            $registros = $statement->rowCount();
            //guarda el resultado en el array
            $resultado['cabecera'] = "HTTP/1.1 200 OK";
            $resultado['Registros modificados'] = $registros;

        }catch (PDOException $e) {
            //se produjo un error
            $resultado['cabecera'] = "HTTP/1.1 500 Internal Server Error";
            $resultado['Registros modificados'] = $e->getMessage();
        } 
        //devuelve el resultado
        return $resultado;
    }


    /**
     * Comprueba que vengan los parámetros y que no vengan vacíos
     * @param array con los datos
     * @return verdadero o falso
     */
    public static function datosCorrectos($datos){
        return isset($datos['nombre']) && 
                    !empty(trim($datos['nombre'])) &&
                    isset($datos['apellidos']) && 
                    !empty(trim($datos['apellidos'])) &&
                    isset($datos['telefono']) && 
                    !empty(trim($datos['telefono'])) &&
                    isset($datos['email']) && 
                    !empty(trim($datos['email'])) &&
                    isset($datos['detalle']) && 
                    !empty(trim($datos['detalle'])) &&
                    isset($datos['id']) && 
                    !empty(trim($datos['id']));
    }

}