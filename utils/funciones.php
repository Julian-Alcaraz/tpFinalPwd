<?php

/**
 * Retorna un array con los datos enviados. Prioriza POST sobre GET.
 * @return array
 */
function devolverDatos()
{
    $colDatos = array();
    if (!empty($_POST)) {
        $colDatos = $_POST;
    } else {
        if (!empty($_GET)) {
            $colDatos = $_GET;
        }
    }
    
    if (count($colDatos)) {
        foreach ($colDatos as $indice => $valor) {
            if ($valor == "") {
                $colDatos[$indice] = 'null';
            }
        }
    }
    
    return $colDatos;
}

function devolverDatos1()
{
    $colDatos = array();
    if (!empty($_GET)) {
        $colDatos = $_GET;
    } else {
        if (!empty($_POST)) {
            $colDatos = $_POST;
        }
    }
    if (count($colDatos)) {
        foreach ($colDatos as $indice => $valor) {
            if ($valor == "") {
                $colDatos[$indice] = 'null';
            }
        }
    }
    return $colDatos;
}


function data_submitted()
{
    $_AAux = array();

    if (!empty($_REQUEST)) {
        foreach ($_REQUEST as $indice => $valor) {
            $_AAux[$indice] = ($valor === "") ? null : $valor;
        }
    }

    return $_AAux;
}

/**
 * Convierte un OBJ a array
 */
function dismount($object)
{
    $reflectionClass = new ReflectionClass(get_class($object));
    $array = array();
    foreach ($reflectionClass->getProperties() as $property) {
        $property->setAccessible(true);
        $array[$property->getName()] = $property->getValue($object);
        $property->setAccessible(false);
    }
    return $array;
}



/**
 * Convierte una lista OBJ->OBJ->OBJ a array
 * @return array
 */
function dismountList_ObjwObjwobj($objectList){
    $arrayParaJson = [];
    foreach ($objectList as $compraEstado) {
        $arrayDatos = [];
        $arrayObjeto = dismount($compraEstado);
        foreach ($arrayObjeto as $clave => $valor) {
            if (strncmp("obj", $clave, 3) === 0) {
                $arrayDatos1 = [];
                $arrayObjeto1 = dismount($valor);
                foreach ($arrayObjeto1 as $clave1 => $valor1) {
                    if (strncmp("obj", $clave1, 3) === 0) {
                        $objArray1 = dismount($valor1);
                        $arrayDatos1[$clave1] = $objArray1;
                    } else {
                        $arrayDatos1[$clave1] = $valor1;
                    }
                }
                $objArray = $arrayDatos1;
                $arrayDatos[$clave] = $objArray;
            } else {
                $arrayDatos[$clave] = $valor;
            }
        }
        array_push($arrayParaJson, $arrayDatos);
    }
    return $arrayParaJson;
}

function convert_array($param)
{
    $_AAux = array();
    if (!empty($param)) {
        if (count($param)) {
            foreach ($param as $obj) {
                array_push($_AAux, dismount($obj));
            }
        }
    }
    return $_AAux;
}

/**
 * Carga automaticamente una clase
 */
spl_autoload_register(function ($class_name) {
    //echo "class ".$class_name ;
    $directorys = array(
        $GLOBALS['ROOT'] . 'modelo/',
        $GLOBALS['ROOT'] . 'modelo/conector/',
        $GLOBALS['ROOT'] . 'control/',
        //  $GLOBALS['ROOT'].'util/class/',
    );
    // print_r($directorys);
    foreach ($directorys as $directory) {
        /*
            echo "<br>";
            echo "DIRECTORIO:".$directory."".$class_name.'.php';
            echo "<br>";
            */
        if (file_exists($directory . "" . $class_name . '.php')) {
            // echo "se incluyo".$directory.$class_name . '.php';
            require_once($directory . "" . $class_name . '.php');
            return;
        }
    }
});
