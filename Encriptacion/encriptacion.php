<!DOCTYPE html>
<html>
<head>
    <title>Encriptación OpenSSL</title>
    <style>
        body{
            font-family: Arial;
            margin:40px;
        }

        input, textarea{
            width:100%;
            padding:10px;
            margin-bottom:15px;
        }

        .resultado{
            border:1px solid #ccc;
            padding:15px;
            margin-top:15px;
            background:#f4f4f4;
        }
    </style>
</head>
<body>

<h2>Cifrado Simétrico con OpenSSL AES-128-CBC</h2>

<form method="POST">

<label>Mensaje:</label>

<textarea name="mensaje" required></textarea>

<label>Clave secreta:</label>

<input type="text" name="clave" required>

<button type="submit">
Procesar
</button>

</form>

<?php

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $mensaje=$_POST["mensaje"];
    $clave=$_POST["clave"];

    if(!empty($mensaje) && !empty($clave)){

        // Normalizar a exactamente 16 caracteres
        $clave=str_pad(
            substr($clave,0,16),
            16,
            "0"
        );

        $metodo="AES-128-CBC";

        // Generar IV
        $iv=openssl_random_pseudo_bytes(
            openssl_cipher_iv_length($metodo)
        );

        // Encriptar
        $cifrado=openssl_encrypt(
            $mensaje,
            $metodo,
            $clave,
            0,
            $iv
        );

        // Desencriptar
        $descifrado=openssl_decrypt(
            $cifrado,
            $metodo,
            $clave,
            0,
            $iv
        );

        echo "<div class='resultado'>";
        echo "<h3>IV hexadecimal:</h3>";
        echo bin2hex($iv);
        echo "</div>";

        echo "<div class='resultado'>";
        echo "<h3>Texto cifrado:</h3>";
        echo $cifrado;
        echo "</div>";

        echo "<div class='resultado'>";
        echo "<h3>Texto descifrado:</h3>";
        echo $descifrado;
        echo "</div>";
    }
}

?>

</body>
</html>