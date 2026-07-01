<?php
require_once "../modelo/Inscriptor.php";
require_once "../clases/Sanitizar.php";

$datos=[];

foreach($_POST as $key=>$valor){
    $datos[$key]=Sanitizar::limpiar($valor);
}

$obj = new Inscriptor();

if($obj->guardar($datos)){
    echo "Registro guardado correctamente";
}else{
    echo "Error";
}
?>