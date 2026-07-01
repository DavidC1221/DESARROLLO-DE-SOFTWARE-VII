<?php
class Validacion{
    public static function correo($correo){
        return filter_var($correo,FILTER_VALIDATE_EMAIL);
    }

    public static function nombre($texto){
        return preg_match("/^[a-zA-Z ]+$/",$texto);
    }
}
?>