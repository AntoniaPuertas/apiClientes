<?php
/**
 * end point para la tabla clientes
 * controla las peticiones y devuelve los datos
 */

include_once("model/clientes.php");
include_once("model/usuarios.php");

//comprueba el método de la llamada
$peticion = $_SERVER['REQUEST_METHOD'];

//comprueba la autorización en las cabeceras
$cabeceras = apache_request_headers();



if(Usuarios::comprobarAutorizacion($cabeceras)){
    switch ($peticion) {
        case 'GET':
            peticionGet();
            break;
        case 'POST':
            Clientes::setNewCliente($_POST);
            break;
        case 'DELETE':
            Clientes::deleteCliente($_GET);
            break;
        case 'PUT':
            Clientes::modificaCliente($_GET);
            break;
        default:
            header("HTTP/1.1 400 Bad Request");
    }
}

/**
 * Comprueba si en la petición viene un parámetro id
 * @return llama a la función que corresponda
 */
function peticionGet(){
    if(isset($_GET['id'])){
        Clientes::getClientById($_GET['id']);
    }else{
        Clientes::getAllClients();
    }
}