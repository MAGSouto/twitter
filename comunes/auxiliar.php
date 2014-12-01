<?php

function conectar()
{
    return pg_connect("host=localhost user=twitter password=twitter
                      dbname=twitter");
}

function contar_filas($tabla)
  {
    $con = conectar();

    $res = pg_query($con, "select count(*) as nfilas from $tabla");

    $fila = pg_fetch_assoc($res);

    pg_close();

    return $fila['nfilas'];
  }