<?php
/**
 * Abrir la conexión con la base de datos
 */

require_once("config.php");

class Conexion{
   public static function connect(){
      
      try{
         $conn = new PDO("mysql:host=".HOSTNAME.";
         dbname=".DATABASE.";
         charset=utf8",
         USERNAME,
         PASSWORD);
 
         $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         return $conn;
 
      }catch(PDOException $exception){
         exit($exception->getMessage());
      }
      
  }
}
