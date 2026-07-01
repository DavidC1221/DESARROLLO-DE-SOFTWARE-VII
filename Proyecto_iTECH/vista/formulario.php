<!DOCTYPE html>
<html>
<head>
<title>iTECH</title>
<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 20px;
    }

    .contenedor {
        background: #ffffff;
        width: 100%;
        max-width: 480px;
        border-radius: 12px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.25);
        overflow: hidden;
    }

    .encabezado {
        background: #1e3c72;
        color: #fff;
        padding: 25px 30px;
        text-align: center;
    }

    .encabezado h2 {
        font-size: 22px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .encabezado p {
        margin-top: 5px;
        font-size: 13px;
        color: #cfd9f7;
    }

    form {
        padding: 30px 30px 10px 30px;
    }

    .campo {
        margin-bottom: 18px;
    }

    label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #333;
        margin-bottom: 6px;
    }

    input[type="text"],
    input[type="number"],
    input[type="email"],
    select,
    textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d0d5dd;
        border-radius: 8px;
        font-size: 14px;
        color: #333;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: #2a5298;
        box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.15);
    }

    textarea {
        resize: vertical;
        min-height: 70px;
    }

    .fila-doble {
        display: flex;
        gap: 15px;
    }

    .fila-doble .campo {
        flex: 1;
    }

    button {
        width: 100%;
        padding: 12px;
        background: #1e3c72;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        margin: 10px 0 25px 0;
        transition: background 0.2s;
    }

    button:hover {
        background: #2a5298;
    }

    footer {
        text-align: center;
        font-size: 12px;
        color: #98a2b3;
        padding-bottom: 20px;
    }
</style>
</head>

<body>

<div class="contenedor">
    <div class="encabezado">
        <h2>Formulario de inscripción</h2>
        <p>iTECH · Complete todos los campos</p>
    </div>

    <form action="../controlador/InscriptorController.php" method="POST">

        <div class="campo">
            <label>Identificación</label>
            <input type="text" name="identidad" placeholder="Ej: 8-123-4567" required>
        </div>

        <div class="fila-doble">
            <div class="campo">
                <label>Nombre</label>
                <input type="text" name="nombre" placeholder="Nombre" required>
            </div>
            <div class="campo">
                <label>Apellido</label>
                <input type="text" name="apellido" placeholder="Apellido" required>
            </div>
        </div>

        <div class="fila-doble">
            <div class="campo">
                <label>Edad</label>
                <input type="number" name="edad" placeholder="Edad" required>
            </div>
            <div class="campo">
                <label>Sexo</label>
                <select name="sexo">
                    <option>Masculino</option>
                    <option>Femenino</option>
                </select>
            </div>
        </div>

        <div class="campo">
            <label>Nacionalidad</label>
            <input type="text" name="nacionalidad" placeholder="Nacionalidad" required>
        </div>

        <div class="fila-doble">
            <div class="campo">
                <label>Correo</label>
                <input type="email" name="correo" placeholder="correo@ejemplo.com" required>
            </div>
            <div class="campo">
                <label>Celular</label>
                <input type="text" name="celular" placeholder="6000-0000" required>
            </div>
        </div>

        <div class="campo">
            <label>Observaciones</label>
            <textarea name="observaciones" placeholder="Comentarios adicionales (opcional)"></textarea>
        </div>

        <button type="submit">Enviar</button>

    </form>

    <footer>© 2026 iTECH. All rights reserved.</footer>
</div>

</body>
</html>