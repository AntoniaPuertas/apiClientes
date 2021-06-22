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

//comprueba el método de la llamada
$peticion = $_SERVER['REQUEST_METHOD'];

//comprueba la autorización en las cabeceras
$cabeceras = apache_request_headers();

if(comprobarAutorizacion($cabeceras, $dbConn)){
    switch ($peticion) {
        case 'GET':
            peticionGet($dbConn);
            break;
        case 'POST':
            setNewCliente($dbConn);
            break;
        case 'DELETE':
            deleteCliente($dbConn);
            break;
        case 'PUT':
            modificaCliente($dbConn);
            break;
        default:
            header("HTTP/1.1 400 Bad Request");
    }
}


function comprobarAutorizacion($cabeceras, $dbConn){
    if(isset($cabeceras['auth'])){
        //comprueba si la key es correcta
        //esta key debería estar guardada en la bd
        $id = 1;
        $sql = $dbConn->prepare("SELECT clave FROM usuario WHERE id=?");
        $sql->bindParam(1, $id);
        $sql->execute();
        $respuesta = $sql->fetch(PDO::FETCH_ASSOC);
        if($respuesta['clave'] == $cabeceras['auth']){
            return true;
        }
    }
    return false;
}

function peticionGet($dbConn){
    if(isset($_GET['id'])){
        getClientById($dbConn);
    }else{
        getAllClients($dbConn);
    }
}

function getClientById($dbConn){
    $id = $_GET['id'];
    //devuelve los datos del registro correspondiente al id
    $sql = $dbConn->prepare("SELECT * FROM clientes WHERE id=?");
    $sql->bindParam(1, $id);
    $sql->execute();

    //devuelve los datos
    header("HTTP/1.1 200 OK");
    echo json_encode($sql->fetch(PDO::FETCH_ASSOC));
    exit();
}

function getAllClients($dbConn){
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

function setNewCliente($dbConn){
    //crear un nuevo elemento
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

function deleteCliente($dbConn){
    //borrar un elemento
    if(isset($_GET['id'])){
        $id = $_GET['id'];

        $statement = $dbConn->prepare("DELETE FROM clientes WHERE id=?");
        $statement->bindParam(1, $id);
        $statement->execute();
        header("HTTP/1.1 200 OK");
        exit();
    }
}

function modificaCliente($dbConn){
    //modificar un elemento
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
