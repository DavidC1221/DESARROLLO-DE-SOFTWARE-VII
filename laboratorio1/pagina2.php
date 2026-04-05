<?PHP

$Nombre = $_REQUEST['nombre'];
echo "El nombre es: ".$Nombre."<br>";

$Edad = $_POST["edad"];

if (isset($Edad) and $Edad > 18){

    //Acciones

    echo "usted puede votar en las próximas elecciones 2028";
}else echo "Usted no es mayor de edad";

?>