<?php
// Archivo de prueba para verificar que PHP está funcionando correctamente
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test PHP - API Chatbot</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            font-weight: bold;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .test-section {
            margin: 30px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        h2 {
            color: #666;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Test de Conexión PHP - API Chatbot</h1>
        
        <?php
        echo '<div class="status success">✅ PHP está funcionando correctamente!</div>';
        ?>

        <div class="test-section">
            <h2>📋 Información del Sistema</h2>
            <table>
                <tr>
                    <th>Configuración</th>
                    <th>Valor</th>
                </tr>
                <tr>
                    <td>Versión de PHP</td>
                    <td><?php echo phpversion(); ?></td>
                </tr>
                <tr>
                    <td>Sistema Operativo</td>
                    <td><?php echo PHP_OS; ?></td>
                </tr>
                <tr>
                    <td>Servidor Web</td>
                    <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'No disponible'; ?></td>
                </tr>
                <tr>
                    <td>Fecha y Hora</td>
                    <td><?php echo date('Y-m-d H:i:s'); ?></td>
                </tr>
                <tr>
                    <td>Directorio del Proyecto</td>
                    <td><?php echo __DIR__; ?></td>
                </tr>
            </table>
        </div>

        <div class="test-section">
            <h2>🔧 Test de Extensiones PHP Necesarias</h2>
            <?php
            $extensiones = [
                'mysqli' => 'Conexión a MySQL',
                'pdo' => 'PDO para base de datos',
                'json' => 'Manejo de JSON',
                'curl' => 'Peticiones HTTP',
                'mbstring' => 'Manejo de strings multibyte',
                'openssl' => 'Seguridad SSL'
            ];

            foreach ($extensiones as $extension => $descripcion) {
                if (extension_loaded($extension)) {
                    echo "<div class='status success'>✅ $extension - $descripcion</div>";
                } else {
                    echo "<div class='status error'>❌ $extension - $descripcion (NO INSTALADA)</div>";
                }
            }
            ?>
        </div>

        <div class="test-section">
            <h2>🗄️ Test de Conexión a Base de Datos</h2>
            <?php
            // Intentar incluir el archivo de configuración de base de datos
            $db_config_path = __DIR__ . '/config/database.php';
            
            if (file_exists($db_config_path)) {
                echo "<div class='status success'>✅ Archivo de configuración de BD encontrado</div>";
                
                try {
                    include_once $db_config_path;
                    
                    // Verificar si la variable $database está definida
                    if (isset($database) && $database instanceof mysqli) {
                        if ($database->ping()) {
                            echo "<div class='status success'>✅ Conexión a la base de datos establecida correctamente</div>";
                            echo "<div class='status info'>ℹ️ Servidor: " . $database->host_info . "</div>";
                        } else {
                            echo "<div class='status error'>❌ La conexión a la base de datos no responde</div>";
                        }
                    } else {
                        echo "<div class='status warning'>⚠️ Variable \$database no encontrada o no es una instancia de mysqli</div>";
                    }
                } catch (Exception $e) {
                    echo "<div class='status error'>❌ Error al conectar con la base de datos: " . $e->getMessage() . "</div>";
                }
            } else {
                echo "<div class='status error'>❌ Archivo de configuración de BD no encontrado en: $db_config_path</div>";
            }
            ?>
        </div>

        <div class="test-section">
            <h2>📁 Test de Archivos del Proyecto</h2>
            <?php
            $archivos_importantes = [
                'apiweb.php' => 'Archivo principal de la API',
                'controllers/controllers.php' => 'Controladores principales',
                'config/database.php' => 'Configuración de base de datos',
                'utils/response.php' => 'Utilidades de respuesta',
                'composer.json' => 'Dependencias de Composer'
            ];

            foreach ($archivos_importantes as $archivo => $descripcion) {
                $ruta_completa = __DIR__ . '/' . $archivo;
                if (file_exists($ruta_completa)) {
                    $tamaño = filesize($ruta_completa);
                    echo "<div class='status success'>✅ $archivo - $descripcion (Tamaño: " . number_format($tamaño) . " bytes)</div>";
                } else {
                    echo "<div class='status error'>❌ $archivo - $descripcion (NO ENCONTRADO)</div>";
                }
            }
            ?>
        </div>

        <div class="test-section">
            <h2>🔗 Test de API</h2>
            <div class="status info">
                <strong>URL de la API:</strong> 
                <?php 
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'];
                $uri = dirname($_SERVER['REQUEST_URI']);
                echo $protocol . '://' . $host . $uri . '/apiweb.php';
                ?>
            </div>
            <div class="status info">
                <strong>Método de prueba:</strong> Puedes usar Postman o curl para probar la API
            </div>
            <div class="status warning">
                <strong>Ejemplo de prueba:</strong><br>
                <code>
                curl -X POST <?php echo $protocol . '://' . $host . $uri . '/apiweb.php'; ?> \<br>
                -H "Content-Type: application/json" \<br>
                -d '{"op": "get_Empresa"}'
                </code>
            </div>
        </div>

        <div class="test-section">
            <h2>📊 Información Adicional</h2>
            <table>
                <tr>
                    <th>Variable</th>
                    <th>Valor</th>
                </tr>
                <tr>
                    <td>memory_limit</td>
                    <td><?php echo ini_get('memory_limit'); ?></td>
                </tr>
                <tr>
                    <td>max_execution_time</td>
                    <td><?php echo ini_get('max_execution_time'); ?> segundos</td>
                </tr>
                <tr>
                    <td>upload_max_filesize</td>
                    <td><?php echo ini_get('upload_max_filesize'); ?></td>
                </tr>
                <tr>
                    <td>post_max_size</td>
                    <td><?php echo ini_get('post_max_size'); ?></td>
                </tr>
                <tr>
                    <td>display_errors</td>
                    <td><?php echo ini_get('display_errors') ? 'On' : 'Off'; ?></td>
                </tr>
            </table>
        </div>

        <div style="text-align: center; margin-top: 30px; color: #666;">
            <p>🔄 Actualiza esta página para volver a ejecutar las pruebas</p>
            <p><small>Generado el <?php echo date('Y-m-d H:i:s'); ?></small></p>
        </div>
    </div>
</body>
</html>