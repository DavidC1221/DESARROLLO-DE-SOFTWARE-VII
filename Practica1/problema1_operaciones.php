<?php
$num1 = 2;
$num2 = 3;
$num3 = 4;

// Operación con precedencia
$resultado = $num1 + $num2 * $num3;

echo "Resultado sin paréntesis: " . $resultado . "<br>";

// Con paréntesis
$resultado2 = ($num1 + $num2) * $num3;

echo "Resultado con paréntesis: " . $resultado2;
?>