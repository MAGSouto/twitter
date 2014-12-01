<?php session_start(); ?>
<!DOCTYPE html> 
<html>
    <head>
        <meta charset="utf-8"/>
        <title>LOGIN</title> 
    </head>
    <body><?php
        
            require '../comunes/auxiliar.php';
            
            if(isset($_POST['nick'],$_POST['pass'])):
                $nick = trim($_POST['nick']);
                $pass = trim($_POST['pass']);
                $con = conectar();
                $res = pg_query($con, "select id, nick
                                    from usuarios
                                    where nick = '$nick' and
                                      pass = md5('$pass')"); 
                if(pg_num_rows($res) > 0):
                    $fila = pg_fetch_assoc($res, 0);

                    $_SESSION['usuario'] = $fila['id'];
                    $_SESSION['nick'] = $fila['nick'];

                    header('Location: /twitter/');

                else: ?>

                    <h3>Error: Contraseña no válida </h3><?php

                endif;
            endif; ?>   
             
            <form action ="login.php" method="post">
                <label>Nombre: </label>
                <input type="text" name="nick"><br><br>
                <label>Contraseña:</label>
                <input type="pass" name="pass"><br><br>
                <input type="submit" value ="Entrar">   
            </form>
    </body>
</html>