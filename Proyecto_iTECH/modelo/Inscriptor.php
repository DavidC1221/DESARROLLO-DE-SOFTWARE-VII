<?php
require_once "../config/Conexion.php";

class Inscriptor{

    public function guardar($datos){

        $db = new Conexion();
        $con = $db->conectar();

        $sql = "INSERT INTO inscriptor
        (identidad,nombre,apellido,edad,sexo,nacionalidad,correo,celular,observaciones)
        VALUES (?,?,?,?,?,?,?,?,?)";

        $stmt = $con->prepare($sql);

        $stmt->bind_param(
            "sssisisss",
            $datos['identidad'],
            $datos['nombre'],
            $datos['apellido'],
            $datos['edad'],
            $datos['sexo'],
            $datos['nacionalidad'],
            $datos['correo'],
            $datos['celular'],
            $datos['observaciones']
        );

        return $stmt->execute();
    }
}
?>