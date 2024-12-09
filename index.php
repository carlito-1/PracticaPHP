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
textarea{
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    padding: 10px;
    width: 100%;
    border: 1px solid #ccc;
    border-radius: 15px;
}
#input{
   -webkit-box-sizing: border-box;
   box-sizing: border-box;
   padding: 10px;
   width: 100%;
   border: 1px solid #ccc;
   border-radius: 15px;
}
#form{
    max-width: 500px; margin: 0 auto;
}

</style>
<body>

<?php
session_start();



$dsn = "mysql:host=localhost;dbname=usuarios2";
$user = "root";
$pwd = "";
//metemos la cookie user en la variable usuario para tener mas comodidad
$usuario = $_COOKIE["user"];
//lo mismo con la cookie id
$id = $_COOKIE["id"];
echo "<h1>Gestor de tareas de: $usuario</h1>";
try{
    $conexion = new PDO($dsn, $user, $pwd);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    echo "ERROR: ".$e->getMessage();
}
$errornombre = "";
$errorfecha = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){ 
//si enviamos la tarea : 
if(isset($_POST["enviar"])){
    $tit = $_POST["titulo"];
    $desc = $_POST["descripcion"];
    $fec = $_POST["fecha"];
    if(empty($tit) || empty($fec)){
        $errornombre = "<p style=color:red>ERROR: CREDENCIALES VACIAS(NOMBRE Y/O FECHA)</p>";
        
    }
    else{
    //insertamos el titulo, la descripcion y la fecha limite que hemos puesto en el formulario
    //e introducimos nuestro id y que el estado este en pendiente
    $insert = "INSERT INTO tareas (titulo, t_descripcion, fecha_limite, id_usuario, estado) VALUES (:titulo, :descripcion, :fecha, :id, :estado)";
    $stms = $conexion->prepare($insert);
    $stms->execute([
        ':titulo' => $tit,
        ':descripcion' => $desc,
        ':fecha' => $fec,
        ':id' => $id,
        ':estado' => 'pendiente'
    ]);
    }
}
//para ir a los grupos
if(isset($_POST["crear"])){
    header("Location:grupos.php");
}
//si le damos a completar tarea: 
if(isset($_POST["completada"])){
    //un mensaje si estamos seguros para poder ayudar al usuario
    echo "<h1 style = color:red>Estas seguro de que quieres completar esta tarea?</h1>";
    //creamos un formulario donde esten las opciones continuar o volver
    echo "<form method='POST'> <input type='hidden' name='hidden2' value='$_POST[hidden]'><input type='submit' name='continuar' value='continuar'> <input type='submit' name='volver' value='volver'> </form>";
}
//si le damos a continuar
if(isset($_POST["continuar"])){
    //hacemos un update en tareas cambiando el estado de pendiente a completada cogiendo el id de la tarea con un hidden
    $update = "UPDATE tareas SET estado = :estado WHERE id_tarea=:id_tarea";
    $stms = $conexion->prepare($update);
    $stms->execute([
        ":estado" => "completada",
        ':id_tarea' => $_POST["hidden2"]
    ]);
}
//para cerrar sesion
if(isset($_POST["cerrar"])){
    header("Location:logout.php");
}
if(isset($_POST["borrar"])){
    $delete = "DELETE FROM tareas WHERE id_tarea = :id_tarea";
    $stms = $conexion->prepare($delete);
    $stms->execute([
        ':id_tarea' => $_POST["hidden3"]
    ]);
}
}
?>
<form id="form" method="POST">
    <?php echo $errornombre?>
    Título: <input id="input" type="text" name="titulo">
    Descripción: <textarea name="descripcion"></textarea><br>
    Fecha Límite: <input id="input" type="date" name="fecha">
    <input id="input" type="submit" name="enviar" value="Agregar tarea"><br>
    <input id="input" type="submit" name="crear" value="Grupos"><br>
    <input id="input" type="submit" name="cerrar" value = "Cerrar sesión">
</form>
<hr><hr>
<h2>Lista de tareas: </h2>

<?php
try{
    //seleccionamos las tareas donde el id del usuario sea nuestro id
    
    $select = "SELECT id_tarea, titulo, t_descripcion, fecha_limite, estado FROM tareas WHERE id_usuario = :id AND id_grupo IS NULL";
    $resultado = $conexion->prepare($select);
    $resultado->execute([
        ':id' => $id,
    ]);
while($row = $resultado->fetch(PDO::FETCH_ASSOC)){
    //si el estado de la tarea es completada saldra con una raya para saber que esta completada
if($row["estado"] == "completada"){
    echo "<form method='POST'><del>$row[titulo] - $row[t_descripcion] - $row[fecha_limite]</del><input type='submit' name='borrar' value='Borrar tarea'><input type='hidden' name='hidden3' value='$row[id_tarea]'></form><br>";
}else{
    //si no esta completada pondremios un boton para completarla teniendo que darle a un boton de continuar
    echo "<form id='no' method='POST'>$row[titulo] - $row[t_descripcion] - $row[fecha_limite] : $row[estado]<input id='no2' type='submit' name='completada' value='completar'> <input type='hidden' name='hidden' value=$row[id_tarea]> </form><br>";        
}
}
}catch(PDOException $e){
    echo "ERROR: ".$e->getMessage();
} 
?>
<script src="prueba.js"></script>
</body>
</html>