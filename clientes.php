<?php
/**
 * end point para la tabla clientes
 * se realiza la conexión con la base
 * y las consultas
 * se devuelven los datos
 */
include "config.php";
include "utils.php";

//realiza la conexion
$dbConn = connect($db);

//Devuelve todos los elementos o uno solo
if($_SERVER['REQUEST_METHOD'] == 'GET'){

    if(isset($_GET['id'])){
        $id = $_GET['id'];
        //devuelve los datos del registro correspondiente al id
        $sql = $dbConn->prepare("SELECT * FROM clientes WHERE id=?");
        $sql->bindParam(1, $id);
        $sql->execute();

        //devuelve los datos
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
        exit();

    }else{
        //devuelve todos los datos de la tabla
        //hace la consulta
        $sql = $dbConn->prepare("SELECT * FROM clientes");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);

        //devuelve los datos
        header("HTTP/1.1 200 OK");
        echo json_encode($sql->fetchAll());
        exit();
    }

}

//crear un nuevo elemento
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $sql = "INSERT INTO clientes (nombre, apellidos, telefono, email, detalle)
            VALUES (?,?,?,?,?)";
    $statement = $dbConn->prepare($sql);
    $statement->bindParam(1, $_POST['nombre']);
    $statement->bindParam(2, $_POST['apellidos']);
    $statement->bindParam(3, $_POST['telefono']);
    $statement->bindParam(4, $_POST['email']);
    $statement->bindParam(5, $_POST['detalle']);

    $statement->execute();

    $clienteId = $dbConn->lastInsertId();

    if($clienteId){
        $input['id'] = $clienteId;
        //devuelve los datos
        //header("HTTP/1.1 200 OK");
        echo json_encode($input);
        exit();
    }
}

//borrar un elemento
if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    if(isset($_GET['id'])){
        $id = $_GET['id'];

        $statement = $dbConn->prepare("DELETE FROM clientes WHERE id=?");
        $statement->bindParam(1, $id);
        $statement->execute();
        header("HTTP/1.1 200 OK");
        exit();
    }
}

//Actualizar o modificar
if($_SERVER['REQUEST_METHOD'] == 'PUT'){
    
    $nombre = $_GET['nombre'];
    $apellidos = $_GET['apellidos'];
    $telefono = $_GET['telefono'];
    $email = $_GET['email'];
    $detalle = $_GET['detalle'];
    $id = $_GET['id'];

    $sql = "UPDATE clientes
            SET nombre = ?,
            apellidos = ?,
            telefono = ?,
            email = ?,
            detalle = ?
            WHERE id = ?";

    $statement = $dbConn->prepare($sql);

    $statement->bindParam(1, $nombre);
    $statement->bindParam(2, $apellidos);
    $statement->bindParam(3, $telefono);
    $statement->bindParam(4, $email);
    $statement->bindParam(5, $detalle);
    $statement->bindParam(6, $id);

    $statement->execute();
    header("HTTP/1.1 200 OK");
    exit();
}
//Si llega aquí la ejecución es que la llamada no es válida
header("HTTP/1.1 400 Bad Request");