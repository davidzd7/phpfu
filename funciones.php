<?php
//[D] DEFINICION DE LA FUNCION PARSEAR FECHA A FORMATO YYYY-mm-dd
function stringAfecha($fecha){
	$time = strtotime($fecha);
	$newformat = date('Y-m-d',$time);
	return $newformat;	
}

//[D] 2018/01/24 Convierte array asociativo a csv disponible para descargar con hipervinculo
function ArregloaCSVExport($rutaarchivo,$arreglo){
	$i=0;
	$campos = array();
	
	foreach ($arreglo[0] as $clave=> $valor) {
		array_push($campos,$clave);
	}
	//print_r($campos);
	
	$fp = fopen($rutaarchivo, 'w');
	foreach ($arreglo as $fields) {
		
		if($i==0){
			fputcsv($fp, $campos);
		}
		fputcsv($fp, $fields);
		
		$i++;
	}
	if(fclose($fp)){
		echo '<a href="'.$rutaarchivo.'">Descargar</a>';
	}
}

//[D]OBTENER LA IP DEL USUARIO
function getRealIP()
{
    if (isset($_SERVER["HTTP_CLIENT_IP"]))
    {
        return $_SERVER["HTTP_CLIENT_IP"];
    }
    elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
    {
        return $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    elseif (isset($_SERVER["HTTP_X_FORWARDED"]))
    {
        return $_SERVER["HTTP_X_FORWARDED"];
    }
    elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]))
    {
        return $_SERVER["HTTP_FORWARDED_FOR"];
    }
    elseif (isset($_SERVER["HTTP_FORWARDED"]))
    {
        return $_SERVER["HTTP_FORWARDED"];
    }
    else
    {
        return $_SERVER["REMOTE_ADDR"];
    }
}

//[D]ARRAY EN TABLA HTML
function arrayEnTabla($arreglo, $width_, $border_)
{
	if($width_!=false)
	{
		$var=$width_;
		$width_= 'width="'.$var.'%"';
	}
	else
	{
		$width_='';
	}
	if($border_!=false)
	{
		$var=$border_;
		$border_= 'border="'.$var.'"%';
	}
	else
	{
		$border_='';
	}
	
	//echo "<h1>width:::".$width_." border::: ".$border_."</h1>";
	$filas= count($arreglo);
	if($filas>0)
	{
		
		$columnas=count($arreglo[0]);
		$imprime='<table '.$width_.' '.$border_.'>';
		for($x=0;$x<$filas;$x++)
		{
			$imprime.='<tr>';
			for($j=0;$j<$columnas;$j++)
			{
				$imprime.="<td>".$arreglo[$x][$j]."</td>";
			}
			$imprime.='</tr>';
		}
		$imprime.='</table>';
		echo $imprime;
	}
	else
	{
		echo "No hay registros";
	}
}

//[D]CIFRAR [2017-07-07]
function encriptar($string)
{
	
	$key=llave();
	
	$result = '';
	for($i=0; $i<strlen($string); $i++) {
	  $char = substr($string, $i, 1);
	  $keychar = substr($key, ($i % strlen($key))-1, 1);
	  $char = chr(ord($char)+ord($keychar));
	  $result.=$char;
	}
	
	$result = base64_encode($result);
	
	$healthy = array("+", " ", "&nbsp;","&#43;");
	$yummy   = array("_", "-", "-", "_");
	$newphrase = str_replace($healthy, $yummy, $result);
	$result = $newphrase;
	
	return $result;
	//return base64_encode($string);
}

//[D] DESCIFRAR [2017-07-07]
function desencriptar($string)
{
	$yummy = array("+", " ", "&nbsp;","&#43;");
	$healthy   = array("_", "-", "-", "_");
	$newphrase = str_replace($healthy, $yummy, $string);
	$string = $newphrase;
	
	$key=llave();
	
	$result = '';
	$string = base64_decode($string);
	
	for($i=0; $i<strlen($string); $i++) {
	  $char = substr($string, $i, 1);
	  $keychar = substr($key, ($i % strlen($key))-1, 1);
	  $char = chr(ord($char)-ord($keychar));
	  $result.=$char;
	}
	return $result;
	//return base64_decode($string);
	
	
}

//[D] CONVIERTE EN ARRAY LOS DATOS DE UNA TABLA SQL, CONSIDERANDO UN INDISADOR
function getArraySQL($sql){
    
    $conexion = conexion();
    //generamos la consulta

     mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

    if(!$result = mysqli_query($conexion, $sql)) die(); //si la conexión cancelar programa

    $rawdata = array(); //creamos un array

    //guardamos en un array multidimensional todos los datos de la consulta
    $i=0;
	//mysqli_fetch_assoc
	//mysqli_fetch_row
	//mysqli_fetch_array

    while($row = mysqli_fetch_assoc($result))
    {
        $rawdata[$i] = $row;
        $i++;
    }

    cerrarConexion($conexion); //desconectamos la base de datos
    return $rawdata; //devolvemos el array
}

include "/phpqrcode/qrlib.php";
function QR($nombre_corto_marca_alias_etc,$rutas_estaticas,$ruta_guardar,$imprimir,$intx)
{
	$ok=false;
	
	//set it to writable location, a place for temp generated PNG files
	$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'phpqrcode/temp'.DIRECTORY_SEPARATOR;
	
	//html PNG location prefix
	$PNG_WEB_DIR = 'phpqrcode/temp/';
	//include ...
	   
	
	for($j=0;$j<2;$j++)
	{
		//ofcourse we need rights to create temp dir
		if (!file_exists($PNG_TEMP_DIR))
			mkdir($PNG_TEMP_DIR);
		
		$filename = $PNG_TEMP_DIR.$nombre_corto_marca_alias_etc.'.png';
		
		$matrixPointSize = 10;
		$errorCorrectionLevel = 'L';
		
		$filename = $PNG_TEMP_DIR.$nombre_corto_marca_alias_etc.md5(@$_REQUEST['data'].'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
		
		if (file_exists($filename)) {
			if($imprimir & $j==1)
			{
			echo "<h2><b>El fichero $filename existe :) </b></h2>";
			}
			$ok=true;
			rename($filename,$ruta_guardar.$nombre_corto_marca_alias_etc.".png");
			
		} else {
			if($imprimir & $j==1){
			echo "<h2><b>El fichero $filenameno NO existe >:(</b></h2>";
			}
		}
		//rename($filename,"../result_qr/".$nombre_corto_marca_alias_etc[$x].".png");
		
		QRcode::png($rutas_estaticas, $filename, $errorCorrectionLevel, $matrixPointSize, 2); 
		if($imprimir & $j==1)
		{
			$y=$intx;
			$PNG_WEB_DIR.basename($filename);
			echo "<h1>Marca '".($y+1)."': ".$nombre_corto_marca_alias_etc." ::: ".$rutas_estaticas."</h1>";
			echo '<img src="'.$ruta_guardar.$nombre_corto_marca_alias_etc.".png".'" /><hr/>';  
		}
		else
		{
			$PNG_WEB_DIR.basename($filename);
		}
	}
	
	return $ok;
}//ef

function dirToArray($dir,$formato) { 
   
   $result = array(); 

   $cdir = scandir($dir); 
   foreach ($cdir as $key => $value) 
   { 
      if (!in_array($value,array(".",".."))) 
      { 
         if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) 
         { 
            //$result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value); 
         } 
         else 
         { 
            if(strpos($value,$formato) !== false)
			{
				$result[] = $value; 
			}
         } 
      } 
   } 
   return $result; 
} 
//----------------------------------------------
function zzip($destino,$ruta_archivos,$ficheros)
{
	$zip = new ZipArchive();
	
	if($zip->open($destino,ZIPARCHIVE::CREATE)===true) 
	{
			//$zip->addFile('log_sad2.txt');
			//$zip->addFile('log_sad.txt');
			foreach($ficheros as $key => $value)
			{
				//echo $value."<br>";
				$zip->addFile($ruta_archivos."/".$value,$value);
			}
			$zip->close();
			echo '<br>[Creado: '.$destino.']';
	}
	else {
			echo '<br>[Error creando: '.$destino.']';
	}
}
//*********************

function borrar_archivos($ruta)//'ruta/al/folder/*'
{
	$files = glob($ruta.'*'); // obtiene todos los archivos
	$contador=0;
	foreach($files as $file)
	{
	  if(is_file($file)) // si se trata de un archivo
		
		//echo '<br>'.$file;
		unlink($file); // lo elimina
		$contador++;
	}
	echo "<br>[ Total de archivo borrados : ".$contador." en -> ".$ruta."]";
}



?>
