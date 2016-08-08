<!--
//  SISTEMA DE ANÁLISIS INTELIGENTE DE DATOS
//  SOFTWARE DE MINERÍA DE DATOS

//  Copyright 2014 Pedro Fernández Bosch.
//  Departamento de Lenguajes y Sistemas Informáticos.
//  Universidad de Granada.
//  $Revisión: 1.0 $ $Fecha: 2014/05/19 13:45:30 $ 
//  $Ejecución Windows: XAMPP v3.2.1
-->
<?php     
    set_time_limit(3600); //Configuración/Ampliación del tiempo límite de buble (60 minutos)
    
    $leyenda="LEYENDA: Las unidades estan expresadas en microgramos/metrocubico(ug/m3).<br />
    Fecha-Hora<br />
    SO2: Dioxido de azufre<br />
    PART: Particulas en suspension<br />
    NO2: Dioxido de nitrogeno<br />
    CO: Monoxido de carbono<br />
    O3: Ozono<br />
    SH2: Acido sulfhidrico<br />";
    
    echo $leyenda."<br />";
    
    //LECTURA Y FILTRADO DEL CÓDIGO HTML
    $fp = fopen($provincia.".txt", "r") or exit("Unable to open file!");

    while(!feof($fp)){
        //Leemos una lineadel fichero
        $entrada=fgets($fp);
        $entrada = strtolower($entrada);
        
        //Recogida de estaciones
        if(strstr($entrada, 'estacion')){
            $valor[1]='n'; $valor[2]='n'; $valor[3]='n'; $valor[4]='n'; $valor[5]='n'; $valor[6]='n';
            
            //Extraemos el texto que hay entre Estacion y Direccion (Que es el nombre de la estación)
            $estacion = explode('estacion',$entrada);
            $estacion = explode('direccion',$estacion[1]);
            $estacion = $estacion[0];
            
            //Filtro de codigo HTML
            $estacion = utf8_decode($estacion);
            $estacion = strip_tags($estacion); //http://notasweb.com/articulo/php/eliminar-etiquetas-html-de-una-cadena-de-texto.html
            $estacion = substr($estacion,7);
            $estacion = str_replace(' ', '-', $estacion);
            
            //echo $estacion."<br />";
            
            //TEST DE ERRORES
            $bnd=1;
        }
        
        //Recogida de valores
        if(strstr($entrada, '<td class="cabtabla">')){
            if(strstr($entrada,'so2'))
                $valor[1]='s';
            if(strstr($entrada,'part'))
                $valor[2]='s';
            if(strstr($entrada,'no2'))
                $valor[3]='s';
            if(strstr($entrada,'co'))
                $valor[4]='s';
            if(strstr($entrada,'o3'))
                $valor[5]='s';
            if(strstr($entrada,'sh2'))
                $valor[6]='s';
        }

        //Recogida de valores
        if(strstr($entrada, '<tr><td>')){
            
            //TEST ERRORES
            if($bnd==1){
                echo "Valor[1]-so2: ".$valor[1]."<br />";
                echo "Valor[2]-part: ".$valor[2]."<br />";
                echo "Valor[3]-no2: ".$valor[3]."<br />";
                echo "Valor[4]-co: ".$valor[4]."<br />";
                echo "Valor[5]-o3: ".$valor[5]."<br />";
                echo "Valor[6]-sh2: ".$valor[6]."<br />";
                $bnd=0;
            }
            
            echo htmlspecialchars($entrada)."<br />"; //Imprimir codigo html
            $entrada = str_replace("<td>&nbsp;</td>",'-?-', $entrada);
            echo htmlspecialchars($entrada)."<br />"; //Imprimir codigo html
            
            //Añadir coma (,) entre valores
            $atb = array('-','</td><td>');
            $entrada  = str_replace($atb,',',$entrada);
            
            //Filtro de codigo HTML
            $entrada=utf8_decode($entrada);
            $entrada=strip_tags($entrada); //http://notasweb.com/articulo/php/eliminar-etiquetas-html-de-una-cadena-de-texto.html
            
            $atributo=explode(",",$entrada);
            unset($entrada);
            
            $entrada=$atributo[0]; //Fecha
            $entrada=$entrada.",".$atributo[1]; //Hora
            
            for($i=1;$i<=6;$i++){
                if($valor[$i]=='s') //SO2 PART NO2 CO O3 SH2
                    $entrada=$entrada.",".$atributo[$i+1];
                else
                    $entrada=$entrada.",?";
            }
            
            //$entrada  = str_replace("&nbsp;",'?',$entrada); //Eliminar el &nbsp;
            $entrada = $entrada."\n";
            echo $entrada."<br />";
            
            $fp2 = fopen($provincia."-".$estacion.".txt","a+") or exit("Unable to open file!");
            fwrite($fp2,"$entrada");
            fclose($fp2);
        }
    }
    
    fclose($fp);
    
    //RESULTADOS
    echo"<br />TAREA FINALIZADA"; 
?>