<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    body{
        background-image:url("fondo.jpg");
    }
    #letras{
        justify-content:center;
        display:flex;
        color:blue;
    }
   input {
   -webkit-box-sizing: border-box;
   box-sizing: border-box;
   padding: 10px;
   width: 100%;
   border: 1px solid #ccc;
   border-radius: 15px;
}
form{
    max-width: 500px; margin: 0 auto;
}
</style>
<body>

    <h1 id="letras">¡Registrate!</h1>
<?php
$dsn = "mysql:host=localhost;dbname=usuarios2";
$user = "root";
$pwd = "";
try{
    $conexion = new PDO($dsn, $user, $pwd);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    echo "ERROR: ".$e->getMessage();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty($_POST["correo"])){
        echo "<p style='color:red'>ERROR: No has rellenado el campo : correo</p>";
    }
    if(empty($_POST["contraseña1"]) || empty($_POST["contraseña2"])){
        echo "<p style='color:red'>ERROR: No has rellenado el campo: contraseña</p>";
    }else{
        //comprobamos que las contraseñas son iguales si son iguales
        if($_POST["contraseña1"] == $_POST["contraseña2"]){
            //hasheamos la password
            $hash = password_hash($_POST["contraseña1"], PASSWORD_DEFAULT);
            //e insertamos en la tabla usuarios al nuevo usuario
            $insert = "INSERT INTO usuarios (correo_electronico, contraseña) VALUES (:correo, :contra)";
            $stms = $conexion->prepare($insert);
            $stms->execute([
                ':correo' => $_POST["correo"],
                'contra' => $hash
            ]);
            setcookie("user", $_POST["correo"], time()+3600*24);
            header("Location:login.php");
            
        }else{
            //si las contraseñas no son iguales salta error
            echo "ERROR: las contraseñas no coinciden";
        }
    }
    //para volver al inicio
    if(isset($_POST["volver"])){
        header("Location:inicio.php");
    }
}
?>
<form id="form" method="POST">
    Correo electronico: <input type="email" name="correo"><br>
    Contraseña: <input type="password" name="contraseña1"><br>
    Repite la contraseña: <input type="password" name="contraseña2"><br>
    <input type="submit" name="enviar" value="Registrar"><br>
    <input type="submit" name="volver" value="Volver a inicio">
</form>
<script src="prueba.js"></script>
</body>
</html>