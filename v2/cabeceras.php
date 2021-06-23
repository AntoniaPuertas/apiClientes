<?php
class Cabeceras {

  /**
   * Devuelve las cabeceras de la petición
   * dependiendo si el servidor es apache o nginx
   */
  public static function getCabeceras(){
    // apache_request_headers replicement for nginx
    if (!function_exists('apache_request_headers')) {
                foreach($_SERVER as $key=>$value) {
                    if (substr($key,0,5)=="HTTP_") {
                        $key=str_replace(" ","-",strtolower(str_replace("_"," ",substr($key,5))));
                        $out[$key]=$value;
                    }else{
                        $out[$key]=$value;
    		}
                }
                return $out;
    }else{
      return apache_request_headers();
    }
  } 

}


 ?>