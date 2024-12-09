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
#input {
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
$errornombre = "";

$dsn = "mysql:host=localhost;dbname=usuarios2";
$user = "root";
$pwd = "";
try{
    $conexion = new PDO($dsn, $user, $pwd);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    echo "ERROR: ".$e;
}
$id = $_COOKIE["id"];
$usuario = $_COOKIE["user"];
echo "<h1>Gestor de grupos de: $usuario</h1>";
if($_SERVER["REQUEST_METHOD"] == "POST"){
if(isset($_POST["crear"])){
    if(empty($_POST["nombre"])){
        $errornombre = "ERROR EN EL NOMBRE DE GRUPO";
    }else{
        $insert = "INSERT INTO grupos(nombre_grupo, g_descripcion, admin) VALUES (:nombre_grupo, :descripcion, :admin)";
        $stms = $conexion->prepare($insert);
        $stms->execute([
            ':nombre_grupo' => $_POST["nombre"],
            ':descripcion' => $_POST["descripcion"],
            ':admin' => $id
        ]);
        $select = "SELECT id_grupo from grupos where nombre_grupo = '$_POST[nombre]'";
        $resultado = $conexion->query($select);
    while($row = $resultado->fetch(PDO::FETCH_ASSOC)){
            $insert = "INSERT INTO usuarios_grupos (id_grupo, id_usuario) VALUES(:id_grupo, :id_usuario)";
            $stms = $conexion->prepare($insert);
            $stms->execute([
                ':id_grupo' => $row["id_grupo"],
                ':id_usuario' => $id
            ]);
    }
    }

}
if(isset($_POST["insertar2"])){
try{
    $select = "SELECT id_grupo, id_usuario FROM usuarios, grupos WHERE correo_electronico = '$_POST[correo]' AND nombre_grupo = '$_POST[grupo]'";
    $resultado = $conexion->query($select);
while($row = $resultado->fetch(PDO::FETCH_ASSOC)){
    $insert = "INSERT INTO usuarios_grupos (id_grupo, id_usuario) VALUES ('$row[id_grupo]', '$row[id_usuario]')";
    $conexion->query($insert);
}
}catch(PDOException $e){
        echo "ERROR: ".$e;
}  
}
if(isset($_POST["tarea2"])){
    $select = "SELECT id_grupo FROM grupos where nombre_grupo = :nombre_grupo";
    $resultado = $conexion->prepare($select);
    $resultado->execute([
        ':nombre_grupo' => $_POST["grupo2"]
    ]);
    
while($row = $resultado->fetch(PDO::FETCH_ASSOC)){
    $insert = "INSERT INTO tareas(titulo, t_descripcion, fecha_limite, id_usuario, estado, id_grupo) VALUES ('$_POST[titulo]','$_POST[descripcion]','$_POST[fecha]','$id','pendiente','$row[id_grupo]')";
    $conexion->query($insert);
    $select2 = "SELECT id_tarea FROM tareas WHERE id_grupo = :id_grupo AND titulo = :titulo";
    $resultado2 = $conexion->prepare($select2);
    $resultado2->execute([
        ':id_grupo' => $row['id_grupo'],
        ':titulo' => $_POST['titulo']
]);
while($row2 = $resultado2->fetch(PDO::FETCH_ASSOC)){
    $insert2 = "INSERT INTO tareas_grupos (id_grupo, id_tarea) VALUES ('$row[id_grupo]','$row2[id_tarea]')";
    $conexion->query($insert2);
}
}
}
if(isset($_POST["completar"])){
    //un mensaje si estamos seguros para poder ayudar al usuario
    echo "<h1 style=color:red>Estas seguro de que quieres completar esta tarea?</h1>";
    //creamos un formulario donde esten las opciones continuar o volverS
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
if(isset($_POST["borrar"])){
    $delete = "DELETE FROM tareas_grupos where id_tarea = :id_tarea";
    $stms = $conexion->prepare($delete);
    $stms->execute([
        ':id_tarea' => $_POST["hidden3"]
    ]);
    $delete2 = "DELETE FROM tareas WHERE id_tarea = :id_tarea";
    $stms2 = $conexion->prepare($delete2);
    $stms2->execute([
        ':id_tarea' => $_POST["hidden3"]
    ]);
}
}
try{
    //grupos donde eres admin
    
    $select = "SELECT id_grupo, nombre_grupo, g_descripcion FROM grupos WHERE admin = :admin";
    $resultado = $conexion->prepare($select);
    $resultado->execute([
        ':admin' => $id
    ]);
    echo "<h1>Tus grupos como admin:</h1>";
    echo "<table border = 1>";
    echo "<tr>";
    echo "<td>Nombre del grupo</td>";
    echo "<td>Descripción del grupo</td>";
    echo "<td>Participantes</td>";
    echo "<td>Tareas</td>";
    echo "</tr>";
while($row = $resultado->fetch(PDO::FETCH_ASSOC)){
    echo "<tr>";
    echo "<td>$row[nombre_grupo]</td>";
    echo "<td>$row[g_descripcion]</td>";
    echo "<td>";
    echo "<ul>";
    $select2 = "SELECT correo_electronico FROM usuarios, usuarios_grupos, grupos WHERE nombre_grupo = '$row[nombre_grupo]' AND usuarios.id_usuario = usuarios_grupos.id_usuario AND grupos.id_grupo = usuarios_grupos.id_grupo";
    $resultado2 = $conexion->query($select2);
while($row2 = $resultado2->fetch(PDO::FETCH_ASSOC)){
if($row2["correo_electronico"] == $usuario){
    echo "<li>$row2[correo_electronico] : Admin</li>";
}else{
    echo "<li>$row2[correo_electronico]</li>";
}
}
echo "</ul>";
echo "</td>";
echo "<td>";
echo "<ul>";
$select3 = "SELECT id_tarea, titulo, t_descripcion, fecha_limite, estado FROM tareas WHERE id_grupo = :id_grupo";
$resultado3 = $conexion->prepare($select3);
$resultado3->execute([
    ':id_grupo' => $row["id_grupo"]
]);

    while($row3 = $resultado3->fetch(PDO::FETCH_ASSOC)){
        if($row3["estado"] == "pendiente"){
            echo "<li><form method='POST'>$row3[titulo] - $row3[t_descripcion] - $row3[fecha_limite] : $row3[estado]<input type='submit' name='completar' value='completar'><input type='hidden' name='hidden' value='$row3[id_tarea]'></form></li>";
        }else{
            echo "<li><form method='POST'><del>$row3[titulo] - $row3[t_descripcion] - $row3[fecha_limite]</del><input type='submit' name='borrar' value='Borrar tarea'><input type='hidden' name='hidden3' value='$row3[id_tarea]'></form></li>";
        }
    }

echo "</ul>";
echo "</td>";

echo "</tr>";

}
echo "</table>";
echo "<form method='POST'><input type='submit' name='insertar' value='Agregar participantes'></form>";
echo "<form method='POST'><input type='submit' name='tarea' value='Agregar tarea'></form>";
//grupos donde no eres admin
$select4 = "SELECT nombre_grupo, g_descripcion FROM grupos, usuarios, usuarios_grupos WHERE usuarios_grupos.id_usuario = '$id' AND usuarios_grupos.id_usuario = usuarios.id_usuario AND usuarios_grupos.id_grupo = grupos.id_grupo";
$resultado4 = $conexion->query($select4);
echo "<h1>Tus grupos como participante:</h1>";
echo "<table border = 1>";
echo "<tr>";
echo "<td>Nombre del grupo</td>";
echo "<td>Descripción del grupo</td>";
echo "<td>Participantes</td>";
echo "<td>Tareas</td>";
echo "</tr>";
while($row = $resultado4->fetch(PDO::FETCH_ASSOC)){
    echo "<tr>";
    echo "<td>$row[nombre_grupo]</td>";
    echo "<td>$row[g_descripcion]</td>";
    echo "<td>";
    echo "<ul>";
    $select5 = "SELECT correo_electronico FROM usuarios, grupos, usuarios_grupos WHERE nombre_grupo = '$row[nombre_grupo]' AND usuarios.id_usuario = usuarios_grupos.id_usuario AND grupos.id_grupo = usuarios_grupos.id_grupo";
    $resultado5 = $conexion->query($select5);
while($row2 = $resultado5->fetch(PDO::FETCH_ASSOC)){
    echo "<li>$row2[correo_electronico]</li>";
} 
    echo "</ul>";
    echo "</td>";

    echo "<td>";
    echo "<ul>";
$idgrupo = "";
$idtarea = "";
$select = "SELECT id_grupo FROM grupos WHERE nombre_grupo = :nombre_grupo";
$resultado = $conexion->prepare($select);
$resultado->execute([
    ':nombre_grupo' => $row["nombre_grupo"]
]);
while($row2 = $resultado->fetch(PDO::FETCH_ASSOC)){
    $idgrupo = $row2["id_grupo"];
}
$select2 = "SELECT id_tarea FROM tareas where id_grupo = :id_grupo";
$resultado2 = $conexion->prepare($select2);
$resultado2->execute([
    ':id_grupo' => $idgrupo
]);
while($row3 = $resultado2->fetch(PDO::FETCH_ASSOC)){
    $idtarea = $row3["id_tarea"];
}
$select6 = "SELECT titulo, t_descripcion, fecha_limite, estado FROM tareas, grupos, tareas_grupos WHERE nombre_grupo = '$row[nombre_grupo]' AND tareas.id_tarea=tareas_grupos.id_tarea AND grupos.id_grupo = tareas_grupos.id_grupo";
$resultado6 = $conexion->query($select6);
while($row3 = $resultado6->fetch(PDO::FETCH_ASSOC)){
    if($row3["estado"] == "pendiente"){
        echo "<li><form method='POST'>$row3[titulo] - $row3[t_descripcion] - $row3[fecha_limite] : $row3[estado]<input type='submit' name='completar' value='completar'><input type='hidden' name='hidden' value='$idtarea'></form></li>";
    }else{
        echo "<li><form method='POST'><del>$row3[estado] - $row3[t_descripcion] - $row3[fecha_limite]</del><input type='submit' name='borrar' value='Borrar tarea'><input type='hidden' name='hidden3' value='$idtarea'></form></li>";
    }
}
echo "</ul>";
echo "</td>";
echo "</tr>";
}
echo "</table>";
echo "<form method='POST'><input type='submit' name='tarea' value='Agregar tarea'></form>";
echo "<form method='POST'><input type='submit' name='crear2' value='Crear grupo'></form>";
}
catch(PDOException $e){
    echo "ERROR: ".$e;
}
?>
<?php if(isset($_POST["insertar"])):?>
    <h1 id="letras">Nuevo participante</h1>
    <form id="form" method="POST">
    Correo electronico: <input id="input" type="email" name="correo">
    Nombre del grupo: <select id="input" name="grupo">
    <?php
        $select = "SELECT nombre_grupo FROM grupos WHERE admin = '$id'";
        $resultado = $conexion->query($select);
        while($row = $resultado->fetch(PDO::FETCH_ASSOC)){
            echo "<option>$row[nombre_grupo]</option>";
        }    
    ?>
    </select>
    <input id="input" type="submit" name="insertar2" value="agregar">
    </form>
<?php elseif(isset($_POST["tarea"])):?>
<h1 id="letras">Agrega tareas a un grupo</h1>
<form id="form" method="POST">
    Nombre del grupo: <select id="input"name="grupo2">
        <?php
        $select = "SELECT nombre_grupo FROM grupos, usuarios, usuarios_grupos WHERE usuarios_grupos.id_usuario = '$id' AND usuarios_grupos.id_usuario = usuarios.id_usuario AND usuarios_grupos.id_grupo = grupos.id_grupo";
        $resultado = $conexion->query($select);
        while($row = $resultado->fetch(PDO::FETCH_ASSOC)){
            echo "<option>$row[nombre_grupo]</option>";
        }
        ?>
    </select>
    Titulo de la tarea: <input id="input" type="text" name="titulo">
    Descripcion de la tarea: <textarea name="descripcion"></textarea>
    Fecha limite de la tarea: <input id="input" type="date" name="fecha">
    <input id="input" type="submit" name="tarea2" value="Agregar tarea">
</form>
<?php else:?>
    <h1 id="letras">Crea tu grupo</h1>
    <form id="form" method="POST">
    Nombre del grupo: <input id="input" type="text" name="nombre"><?php if(!empty($errornombre)): echo "<p style=color:red>$errornombre </p><br>"; endif;?><br>
    Descripción del grupo: <input id="input" type="text" name="descripcion">
    <input id="input" type="submit" name="crear" value="Crear grupo">
</form>
<?php endif;?>
<h3><a href="index.php">Vovler al gestor de tareas</a></h3>
<script src="prueba.js"></script>
</body>
</html>