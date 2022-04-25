<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


//esto hace que la info del formulario valla al archivo.txt
//si el archivo existe 
if (file_exists("archivo.txt")) {
    //leer y almacenar el contenido json en una variable
    $strJson = file_get_contents("archivo.txt"); //convertir el json en un array aClientes
    $aClientes = json_decode($strJson, true);
    //creo el aClientes con todo lo que va echo un array de json
} else { /* suponiendo que no exixte el archivp*/

    $aClientes = array(); //array vacio de clientes aClientes
}

if (isset($_GET["id"])) { /*$id= isset($_GET["id"])? $_GET ["id"]: ""; */ /*  todo este if puede ser remplazado por este codigito*/
  $id=$_GET["id"];
  //gaurada  una variable de un cliente que ya esta insertado,un cliente nuevo no tiene id
}else{
   $id= "";
}

//Si es eliminar
if (isset($_GET["do"]) && $_GET["do"] == "eliminar") {

    if (file_exists("imagenes/" . $aClientes[$id]["imagen"])) {
        unlink("imagenes/" . $aClientes[$id]["imagen"]);//unliki desvincular la función () para eliminar archivos.
    }
    //Elimino la posición $aClientes[$id]
    unset($aClientes[$id]);//unset  elimina una variable o un elemento de un array u objeto.

    //Convertir el array en json
    $strJson = json_encode($aClientes);

    //Actualizar archivo con el nuevo array de clientes
    file_put_contents( "archivo.txt", $strJson);

    header("Location: index.php");
}


if ($_POST) {
    //obtengo la info del formulario
    $dni = trim($_POST["txtDni"]); //trim elimina los espacion en blanco del formulario
    $nombre = trim($_POST["txtNombre"]);
    $telefono = trim($_POST["txtTel"]);
    $correo = trim($_POST["txtCorreo"]);
    

    //Si viene una imagen adjunta la guardo
    if ($_FILES["archivo"]["error"] === UPLOAD_ERR_OK) { // si viene una imagen y se subio bien,upload es si se subio algo correctamente
        if (isset($aClientes[$id]["imagen"]) && $aClientes[$id]["imagen"] != "") { //si existe una imagen que es dif de vacio tengo que eliminar la que habia antes
            if (file_exists("imagenes/" . $aClientes[$id]["imagen"])) {
                unlink("imagenes/" . $aClientes[$id]["imagen"]);
            }
        }
        $nombreAleatorio = date("Ymdhmsi");
        $archivo_tmp = $_FILES["archivo"]["tmp_name"];
        $nombreArchivo = $_FILES["archivo"]["name"];
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $imagen = "$nombreAleatorio.$extension";

        if ($extension == "png" || $extension == "jpg" || $extension == "jpeg") {
            move_uploaded_file($archivo_tmp, "imagenes/$imagen");
        }
    } else {
        //Sino sube la imagen nueva conservamos la imagen que habia previamente
        if ($id >= 0 && $_REQUEST["do"]== "editar") { //sie esta actualizando rescata la imagen y sino vacio
            $imagen = $aClientes[$id]["imagen"]; //el request es una array asociativo que contiene los datos del post y del get al mismo tiempo(id,do,txt)
        } else {
            $imagen = "";
        }
    }

    //crear un array con todos los datos
    if ($id >= 0) {
        //actualizo
        $aClientes[$id] = array("dni" => $dni, 
                                "nombre" => $nombre,
                                "telefono" => $telefono,
                                "correo" => $correo,
                                "imagen" => $imagen);   
    } else {
        $aClientes[] = array("dni" => $dni,
                            "nombre" => $nombre,
                            "telefono" => $telefono,
                            "correo" => $correo,
                            "imagen" => $imagen);   
    }

    //converir el array en json
    $jsonClientes = json_encode($aClientes);

    //almacenar el json en archivo.txt

    file_put_contents("archivo.txt", $jsonClientes);
}



?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABM Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/40e341f8f7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<style>
    table,
    tr,
    th,
    td {
        border: 1px solid #000000;
        text-align: center;
    }
</style>

<body>
    <main class="container">
        <div class="col-12 col-sm-12 text-center py-5">
            <h1>Registro de Clientes</h1>
        </div>
        <div class="row">
            <div class="col-6 col-sm-6">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="formulario">
                        <div class="pb-3">
                            <label for="">DNI:</label>
                            <!--requierd value conctas la tabla con el formulario -->
                            <input type="text" name="txtDni" id="txtDni" class="form-control" required value="<?php echo isset($aClientes[$id]["dni"]) ? $aClientes[$id]["dni"] : ""; ?>"> <!-- aClientes[id-[dni lo conecta con el post de arriba-->
                        </div>
                        <div class="pb-3">
                            <label for="">Nombre:</label>
                            <input type="text" name="txtNombre" id="txtNombre" class="form-control" required value="<?php echo isset($aClientes[$id]["nombre"]) ? $aClientes[$id]["nombre"] : ""; ?>">
                        </div>
                        <div class="pb-3">
                            <label for="">Telefono:</label>
                            <input type="text" name="txtTel" id="txtTel" class="form-control" required value="<?php echo isset($aClientes[$id]["tel"]) ? $aClientes[$id]["tel"] : ""; ?>">
                        </div>
                        <div class="pb-3">
                            <label for="">Correo:</label>
                            <input type="text" name="txtCorreo" id="txtCorreo" class="form-control" required value="<?php echo isset($aClientes[$id]["correo"]) ? $aClientes[$id]["correo"] : ""; ?>">
                        </div>
                        <div class="pb-3">
                            <label for="">Seleccionar Archivo</label>
                            <input type="file" name="archivo" id="archivo" accept=".jpeg , .jpg , .png">
                            <br>
                            <small>Archivos admitidos: .jpg, .jpeg, .png</small>
                        </div>
                        <div class="pb-3">
                            <button type="submit" class="btn btn-danger">Guardar</button>
                            <a href="index.php" class="btn btn-warning my-2">Nuevo</a>
                            <!--nuevo elimine todo lo escrito-->
                        </div>
                    </div>
                </form>

            </div>

            <div class="col-6 col-sm-6">
                <table class="table table-hover border">
                    <tr>
                        <th colspan="2">Imagen</th>
                        <th colspan="2">DNI</th>
                        <th colspan="2">Nombre</th>
                        <th colspan="2">Correo</th>
                        <th colspan="2">Acciones</th>
                    </tr>

                    <!--concta el formulario con la tabla-->
                    <?php foreach ($aClientes as $pos => $cliente) : ?>
                        <!--recorre el array aClientes y ademas se guarda en cliente ademas asigna la clave del elemento actual a pos-->
                        <tr>
                            <!--for each porque quiero que muiestre en pantalla lo que meta aca-->
                            <td colspan="2"><img src="imagenes/<?php echo $cliente["imagen"]; ?>" class="img-thumbnail"></td>
                            <td colspan="2"><?php echo $cliente["dni"]; ?></td>
                            <td colspan="2"><?php echo $cliente["nombre"]; ?></td>
                            <td colspan="2"><?php echo $cliente["correo"]; ?></td>
                            <td colspan="2">
                                <a href="?id=<?php echo $pos; ?>&do=editar"><i class="fas fa-edit"></i></a>
                                <!--lllamo a todo lo que guardo pos para poder editar-->
                                <a href="?id=<?php echo $pos; ?>&do=eliminar"><i class="fas fa-trash-alt"></i></a>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </main>

</body>

</html>