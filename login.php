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
$mensaje = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST["enviar"])){
        //recogemos las variables POST 
        $nom = $_POST["correo"];
        $pwd = $_POST["contraseña"];
        //seleccionamos TODOS los usuarios de la tabla usuarios
        $select = "SELECT id_usuario, correo_electronico, contraseña FROM Usuarios";
        $resultado = $conexion->query($select);
try{
    while($row = $resultado->fetch(PDO::FETCH_ASSOC)){
    //si el correo coincide y la contraseña tambien
    if($row["correo_electronico"] == $nom && password_verify($pwd, $row["contraseña"])){
        //creamos la cookie user y la cookie id del usuario
        setcookie("user", $nom, time()+3600*24);
        setcookie("id", $row["id_usuario"], time()+3600*24);
        header("Location: index.php");
    }else{
        //si no coinicde mensaje de error
        $mensaje = "ERROR: Fallo en el inicio de sesion";
    }
    }
}catch(PDOException $e){
        echo "ERROR: ".$e->getMessage();
}
echo "<h1><p style='color:red'>$mensaje</p></h1>";
}
//para voler a inicio
if(isset($_POST["volver"])){
    header("Location:inicio.php");
}
}
?>
<h1 id="letras">Inicio de sesion: </h1>
<form id="form" method="POST">
    Correo electronico: <input type="email" name="correo"><br>
    Contraseña: <input type="password" name="contraseña"><br>
    <input type="submit" name="enviar" value="Iniciar sesion"><br>
    <input type="submit" name="volver" value="Volver a inicio">
</form>
<script src="prueba.js"></script>
</body>
</html>