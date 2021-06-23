<?php
/**
 * clase Usuarios
 * se realiza la conexión con la base
 * y las consultas
 * se devuelven los datos
 */

include_once("utils.php");

class Usuarios {
    const NOMBRE_TABLA = "usuario";
    const ID = "id";
    const NOMBRE = "nombre";
    const EMAIL = "email";
    const CLAVE = "clave";


/**
 * comprueba que la petición venga con una clave que exista en la base con el usuario id = 1
 * @param $cabeceras cabeceras de la petición
 * 
 * @return verdadero o falso
 */
public static function comprobarAutorizacion($cabeceras){
    if(isset($cabeceras['auth'])){
        //realiza la conexion
        $dbConn = Conexion::connect();

        //comprueba si la key es correcta
        //esta key debería estar guardada en la bd
        $id = 1;
        //prepara la consulta
        $sql = $dbConn->prepare("SELECT ". self::CLAVE ." FROM " . self::NOMBRE_TABLA . " WHERE " . self::ID . "=?");

        //relaciona los parámetros
        $sql->bindParam(1, $id);

        //ejecuta la consulta
        $sql->execute();

        //guarda el resultado
        $respuesta = $sql->fetch(PDO::FETCH_ASSOC);

        //comprueba si las claves coinciden
        if($respuesta['clave'] == $cabeceras['auth']){
            return true;
        }
    }
    return false;
}
}