<?php
class Sanitizar{
    public static function limpiar($dato){
        $dato = trim($dato);
        $dato = strtolower($dato);
        return ucwords($dato);
    }
}
?>