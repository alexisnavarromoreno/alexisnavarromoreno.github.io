<?php
function form_mail($sPara, $sAsunto, $sTexto, $sDe) {
    $bHayFicheros = 0;
    $sCabeceraTexto = "";
    $sAdjuntos = "";

    if ($sDe)$sCabeceras = "From:".$sDe."\n";
    else $sCabeceras = "";
    $sCabeceras .= "MIME-version: 1.0\n";
    $sCabeceras .= "Content-Type: text/plain; charset=UTF-8"; 
    foreach ($_POST as $sNombre => $sValor)
        $sTexto = $sTexto."\n".$sNombre." = ".$sValor;

        foreach ($_FILES as $vAdjunto) {
            if ($bHayFicheros == 0) {
                $bHayFicheros = 1;
				$cabeceras = "MIME-Version: 1.0\r\n";
				$sCabeceras .= "Content-Type: text/plain; charset=UTF-8\r\n";
				$cabeceras .= "Content-Transfer-Encoding: 8bit\r\n";
                $sCabeceras .= "Content-type: multipart/mixed;";
                $sCabeceras .= "boundary=\"--_Separador-de-mensajes_--\"\n";

                $sCabeceraTexto = "----_Separador-de-mensajes_--\n";
                $sCabeceraTexto .= "Content-Type: text/plain; charset=UTF-8\r\n";
                $sCabeceraTexto .= "Content-transfer-encoding: 7BIT\n";

                $sTexto = $sCabeceraTexto.$sTexto;
            }

            if ($vAdjunto["size"] > 0) {
                $sAdjuntos .= "\n\n----_Separador-de-mensajes_--\n";
                $sAdjuntos .= "Content-type: ".$vAdjunto["type"].";name=\"".$vAdjunto["name"]."\"\n";;
                $sAdjuntos .= "Content-Transfer-Encoding: BASE64\n";
                $sAdjuntos .= "Content-disposition: attachment;filename=\"".$vAdjunto["name"]."\"\n\n";

                $oFichero = fopen($vAdjunto["tmp_name"], 'r');
                $sContenido = fread($oFichero, filesize($vAdjunto["tmp_name"]));
                $sAdjuntos .= chunk_split(base64_encode($sContenido));
                fclose($oFichero);
            }
        }

    if ($bHayFicheros)
        $sTexto .= $sAdjuntos."\n\n----_Separador-de-mensajes_----\n";
    return(mail($sPara, mb_encode_mimeheader($sAsunto), $sTexto, $sCabeceras));
}

// Cambia aqui el email. Revisa los nombres de los campos de email y asunto según el video
// https://www.youtube.com/formaciongrafica
if (form_mail("alexisnm.1990@gmail.com", $_POST["Email de contacto"],
    "Nombre completo:\n\n", $_POST[fullName],
    "Email:\n\n", $_POST[email],
    "Mensaje:\n\n", $_POST[message]
)),
    
    header("Location: envioOK.html");
?>