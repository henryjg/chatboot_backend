<?php
include_once './utils/response.php';

require './vendor/autoload.php'; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
 
class ArchivosController {
    private $database;

    public function __construct() {
        global $database;
        $this->database = $database;
    }
    
    //---------------------------------------------------------------------------------
    
    public function detecta_extension($mi_extension) {
        $ext = explode(".", $mi_extension);
        return end($ext);
    }
    // ------------------------------------------------------
    public function Subir_Imagen($file,$ruta) {
        $url = $this->subir_archivo($file,$ruta );
        if($url=="NOPERMITIDO"){
            response::error('Archivo no permitido, seleccione un archivo de imágen válido');
        }else if ($url!='') {
            response::success($url, 'Imágen subida correctamente');
        } else {
            response::error('Error en la subida de la fotografía');
        }
    }

    public function subir_archivo($file, $ruta) {
        $ruta_archivo = "";
    
        if ($file && isset($file['tmp_name']) && $file['tmp_name'] != "") {
            if (!file_exists($ruta)) {
                mkdir($ruta, 0777, true);
            }
    
            $nuevo_nombre = "file_" . rand(1000000, 9999999);
            $extension = $this->detecta_extension(basename($file['name']));
    
            if (!$extension) {
                return "Extension no válida";
            }
    
            $nuevo_nombre_completo = $nuevo_nombre . '.' . $extension;
            $uploadfile = $ruta . $nuevo_nombre_completo;
    
            // Lista de tipos permitidos
            $permitidos = array(
                "image/bmp", 
                "image/jpg", 
                "image/jpeg", 
                "image/png"
            );
    
            if (in_array($file['type'], $permitidos)) {
                // Intentar mover el archivo
                if (move_uploaded_file($file['tmp_name'], $uploadfile)) {
                    // Verificar si es una imagen válida antes de procesarla
                    if (exif_imagetype($uploadfile)) {
                        switch ($file['type']) {
                            case 'image/bmp':
                                $imagen = imagecreatefrombmp($uploadfile);
                                break;
                            case 'image/jpg':
                            case 'image/jpeg':
                                $imagen = imagecreatefromjpeg($uploadfile);
                                break;
                            case 'image/png':
                                $imagen = imagecreatefrompng($uploadfile);
                                break;
                            case 'image/avif':
                                if (function_exists('imagecreatefromavif')) {
                                    $imagen = imagecreatefromavif($uploadfile);
                                } else {
                                    return "AVIF no soportado en esta versión de PHP";
                                }
                                break;
                            case 'image/webp':
                                $imagen = imagecreatefromwebp($uploadfile);
                                break;
                            default:
                                $imagen = false;
                        }
    
                        if ($imagen !== false) {
                            // Obtener dimensiones de la imagen
                            $ancho_original = imagesx($imagen);
                            $alto_original = imagesy($imagen);
    
                            // Redimensionar si el ancho es mayor que 1200px
                            $max_ancho = 1400;
                            if ($ancho_original > $max_ancho) {
                                $ratio = $alto_original / $ancho_original;
                                $nuevo_ancho = (int) $max_ancho;   // Conversión explícita a entero
                                $nuevo_alto = (int) ($max_ancho * $ratio);  // Conversión explícita a entero
    
                                // Crear una imagen redimensionada
                                $imagen_redimensionada = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
                                imagecopyresampled($imagen_redimensionada, $imagen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho_original, $alto_original);
    
                                // Guardar la imagen redimensionada
                                switch ($file['type']) {
                                    case 'image/bmp':
                                        imagebmp($imagen_redimensionada, $uploadfile);
                                        break;
                                    case 'image/jpg':
                                    case 'image/jpeg':
                                        imagejpeg($imagen_redimensionada, $uploadfile);
                                        break;
                                    case 'image/png':
                                        imagepng($imagen_redimensionada, $uploadfile);
                                        break;
                                    case 'image/avif':
                                        if (function_exists('imageavif')) {
                                            imageavif($imagen_redimensionada, $uploadfile);
                                        }
                                        break;
                                    case 'image/webp':
                                        imagewebp($imagen_redimensionada, $uploadfile);
                                        break;
                                }
    
                                imagedestroy($imagen_redimensionada);
                            }
                            imagedestroy($imagen);
                        } else {
                            return "Error al procesar la imagen";
                        }
    
                        $ruta_archivo = $ruta . $nuevo_nombre_completo;
                    } else {
                        return "El archivo no es una imagen válida.";
                    }
                } else {
                    return "Error al mover el archivo";
                }
            } else {
                return "Formato de archivo no permitido";
            }
        }
    
        return $ruta_archivo;
    }
    
    

    public function subir_archivo_pdf($file){
        if( $file != "" ){
            // Declarar la ruta
            $ruta = 'uploads/documentos/';
    
            if (!file_exists($ruta)) {
                mkdir($ruta, 0777, true);
            }
    
            $nuevo_nombre = "pdf_" . date("dmY") . "_" . rand(1000000, 9999999);
            $nuevo_nombre_completo = $nuevo_nombre . '.' . $this->detecta_extension(basename($file['name']));
            $uploadfile = $ruta . $nuevo_nombre_completo;
            $ruta_archivo = $ruta . $nuevo_nombre_completo;
    
    
            // Validamos Tipo --------------------------------------------------------
            $permitidos = array("application/pdf");
            if (in_array($file['type'], $permitidos)) {                
                if (move_uploaded_file($file['tmp_name'], $uploadfile)) {
                    response::success($ruta_archivo, 'Archivo subido correctamente');
                }
            }else{
                response::error('Este documento no fue subido');
            }
        } else {
            response::error('No hay adjunto');
        }
        
    }

    
    

    // function EnviarCorreo($Pcorreo,$nombre){          
    //     // Crear instancia de PHPMailer
    //     $mail = new PHPMailer();
    //     // Configuraciones del servidor
    //     $mail->isSMTP();  // Usar SMTP
    //     $mail->Host = "imperu.com.pe";  // Dirección del servidor SMTP
    //     $mail->SMTPAuth = false;  // Si el servidor requiere autenticación SMTP, cambia esto a true
    //     $mail->Port = 25;  // Puerto SMTP. Dependiendo de tu configuración puede ser 25, 587, o 465
    //     $mail->CharSet = "UTF-8";
    //     $mail->Priority = 1;
        
    //     // Configuración del idioma
    //     $mail->setLanguage('es');  // Configurar idioma a español
    
    //     // Configuraciones del correo
    //     $mail->From = "carreraimp15k@imperu.com.pe";  // Dirección de correo del remitente
    //     $mail->FromName = "Carrera IMP15K - Piura 2024";  // Nombre del remitente
    //     $mail->Subject = "CORREO";  // Asunto del correo
    //     $mail->AddAddress($Pcorreo,$nombre);  // Destinatario principal
    
    //     // Cuerpo del mensaje
    //     $body = "<html>
    //                 <div width='800' style='background-color:#F7F7F7; padding:30px;'>
    //                     <table  style='border:4px solid #102ab8 ; font-family:Helvetica, sans-serif; border-spacing:0;'  bordercolor='#3324' align='center'>    
    //                         <tr>
    //                             <td style='background: #102ab8; text-align: center;'>
    //                                 <img style='margin: auto; width: 100%;'  src='https://carreraimp15k.imperu.com.pe/img/metaimg.jpg' alt='IMDPERU'/>
    //                                 <br>
    //                                 <div style='background-color:#FFFFFF;'>
    //                                     <div style='overflow:hidden; padding:40px;'>
    //                                         <div style='font-size:15pt; color:#102ab8; text-align: center; font-family:Arial, Helvetica, sans-serif; padding:20px;  background-color:#FFFFFF;'>
    //                                             <strong>Tu registro ha sido completado</strong><br><br>
    //                                             Gracias por realizar tu Inscripción a la carrera IMP15K,
    //                                         </div>
    //                                     </div>
    //                                 </div>
    //                                 <div style='padding:20px; background-color:#102ab8; text-align:center'>
    //                                     <div style='font-size:12pt; font-weight:500; color:#FFFFFF; font-family:Arial, Helvetica, sans-serif; text-align:center'>
    //                                         CARRERA IMP15K
    //                                     </div>
    //                                 </div>
    //                             </td>
    //                         </tr>
    //                     </table>
    //                 </div>
    //             </html>";
    
    //     // Asignar cuerpo HTML al correo
    //     $mail->Body = $body;
    //     $mail->isHTML(true);
    
    //     // Intentar enviar el correo
    //     if (!$mail->Send()) {
    //         // Si el correo falla, lanzar un error
    //         return true;
    //         // response::error('Error: ' . $mail->ErrorInfo);
    //     } else {
    //         // Si el correo se envía con éxito
    //         return false;
    //         // response::success($mail, 'Correo Enviado');
    //     }
    // }    
}
?>
