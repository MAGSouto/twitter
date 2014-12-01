<?php 
session_start();

require 'comunes/auxiliar.php';

function menu_paginacion($fpp, $pag, $npags, $nenlaces)
{
    $siguiente = $pag+1;
    $anterior = $pag-1;
    $nenlaceslado = floor($nenlaces/2);

    if ($pag > 1) { ?>
        <a href="<?="index.php?pag=$anterior"?>">&lt;</a><?php
    }

    if ($pag - $nenlaceslado <= 0) {
        $i = 1;
        $fin = $nenlaces;
    } elseif ($npags - $pag <= $nenlaceslado) {
        $i = $npags - $nenlaces;
        $fin = $npags;
    } else{
        $i = $pag-$nenlaceslado;
        $fin = $pag+$nenlaceslado;
    }

    for ($i; $i <= $fin; $i++) {            
        if ($i == $pag) { ?>
            <?= $i ?><?php
        } else { ?>
        <a href="index.php?pag=<?=$i?>"><?=$i?></a><?php
        }
    }

    if ($pag < $npags) { ?>
        <a href="<?="index.php?pag=$siguiente"?>">&gt;</a><?php
    }
}

function comprobar_longitud($mensaje)
{
    if (strlen($mensaje) > 140)
    {
        throw new Exception("el mensaje debe tener un maximo de 140 caracteres");        
    }
}

$nfilas = contar_filas('twitts');
$fpp = 3;
$npags = ceil($nfilas/$fpp);
$pag = isset($_GET['pag']) ? trim($_GET['pag']) : 1;
$nenlaces = 5;

if (isset($_SESSION['usuario'], $_SESSION['nick']))
{
    $usuario = $_SESSION['usuario'];
    $nick = $_SESSION['nick'];
} 
else 
{
    header('Location: /twitter/usuarios/login.php');
}

if (isset($_POST['mensaje']) && $_POST['mensaje'] != "")
{
    $mensaje = trim($_POST['mensaje']);
    try {
        comprobar_longitud($mensaje);

        $con = conectar();
        $res = pg_query($con, "insert into twitts (mensaje, usuario_id)
                               values ('$mensaje', $usuario)");

        pg_close();
    } catch (Exception $e) {?>
        <p><?=$e->getMessage();?></p><?php
    }
}

if (isset($_POST['mensaje_id']))
{
    $mensaje_id = trim($_POST['mensaje_id']);

    $con = conectar();
    $res = pg_query($con, "delete from twitts where id = $mensaje_id");

    pg_close();
}?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?=$nick?> - Muro</title>
</head>
<body>
    <p style="text-align: right">Usuario: 
        <span style="font-weight: bold;"><?=$nick?></span>
        <a href="/twitter/usuarios/logout.php"><button>SALIR</button></a>
    </p><hr>

    <form action="index.php" method="post">
    <p>Estado:</p>
    <textarea name="mensaje" cols="50" rows="3" maxlength="140"></textarea>
    <br><br>
    <input type="submit" value="PUBLICAR">
    </form><hr><?php

    $con = conectar();

    $res = pg_query($con, "select id,
                                  mensaje, 
                                  to_char(fecha, 'dd-mm-yyyy\" a las \"HH24:MI:SS') as fecha_format                                  
                             from twitts 
                            where usuario_id = $usuario
                            order by fecha desc
                            limit $fpp
                           offset ($pag-1)*$fpp");

    if (pg_num_rows($res) > 0)
    {
        for ($i = 0; $i < pg_num_rows($res); $i++)
        {
            $fila = pg_fetch_assoc($res, $i);
            extract($fila)?>
            <div style="width: 30%; background-color: #D6E8F4; 
                        margin: 20px 0px; padding: 10px;">
                <p><?=$fecha_format?></p>
                <p><?=$mensaje?></p>
                <form action="index.php" method="post">
                    <input type="hidden" name="mensaje_id" value="<?=$id?>">
                    <input type="submit" value="BORRAR">
                </form> 
            </div><?php
        }
        menu_paginacion($fpp, $pag, $npags, $nenlaces);
    }
    pg_close();
?>
    

</body>
</html>