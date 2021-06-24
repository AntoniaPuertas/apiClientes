<?php
/**
 * end point para la tabla clientes
 * controla las peticiones y devuelve los datos
 */

include_once("model/clientes.php");
include_once("model/usuarios.php");
include_once("cabeceras.php");

//comprueba el método de la llamada
$peticion = $_SERVER['REQUEST_METHOD'];

$cabeceras = Cabeceras::getCabeceras();


if(Usuarios::comprobarAutorizacion($cabeceras)){
    switch ($peticion) {
        case 'GET':
            peticionGet();
            break;
        case 'POST':
            peticionPost();
            break;
        case 'DELETE':
            peticionDelete();
            break;
        case 'PUT':
            peticionPut();
            break;
        default:
            header("HTTP/1.1 400 Bad Request");
    }
}

/**
 * Comprueba si en la petición viene un parámetro id
 * llama a la función que corresponda
 * muestra el resultado
 */
function peticionGet(){
    if(isset($_GET['id'])){
        $respuesta = Clientes::getClientById($_GET['id']);
    }else{
        $respuesta = Clientes::getAllClients();
    }
    respuesta($respuesta);
}

/**
 * llama a la función que guarda los datos del nuevo registro
 * muestra el resultado
 */
function peticionPost(){
    $respuesta = Clientes::setNewCliente($_POST);
    respuesta($respuesta);
}

/**
 * llama a la función que elimina un registro
 *  muestra el resultado
 */
function peticionDelete(){
    $respuesta = Clientes::deleteCliente($_GET);
    respuesta($respuesta);
}

/**
 * llama a la función que modifica un registro
 * muestra el resultado
 */
function peticionPut(){
    respuesta(Clientes::modificaCliente($_GET));
}

/**
 * función que muestra la respuesta
 * @param array con los datos de la respuesta
 * muestra la respuesta
 */
function respuesta($respuesta){
    header($respuesta['cabecera']);
    //devuelve el segundo elemento del array
    echo json_encode(array_slice($respuesta, 1, 1, true));
}