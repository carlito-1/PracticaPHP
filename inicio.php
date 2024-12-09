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
   form{
    justify-content:center;
    display:flex;
    max-width: 500px; margin: 0 auto;
   }
   input {
   -webkit-box-sizing: border-box;
   box-sizing: border-box;
   padding: 10px;
   width: 100%;
   border: 1px solid #ccc;
   border-radius: 15px;
}

   
</style>
<body>
   
    
<?php
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["iniciar_sesion"])){
            header("Location:login.php");
        }else{
            header("Location:registrar.php");
        }
    }
?>

<h1 id="letras">¡Bienvenido Usuario!</h1>
<form id= "form" method="POST">
    <input type="submit" name="iniciar_sesion" value = "Iniciar Sesion">
    <input type="submit" name="registrarse" value = "Registrate">
</form>
<script src="prueba.js"></script>
</body>
</html>