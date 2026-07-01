<?php
class Conexion{
    private $host = "localhost";
    private $usuario = "root";
    private $clave = "";
    private $base = "itech";

    public function conectar(){
        $conexion = new mysqli($this->host,$this->usuario,$this->clave,$this->base);

        if($conexion->connect_error){
            die("Error: ".$conexion->connect_error);
        }

        return $conexion;
    }
}
?>