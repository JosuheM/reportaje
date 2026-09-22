<?php
/**
 * Plantilla de conexión. Copia este archivo como "conexion.php" en esta misma
 * carpeta y pon ahí tus valores reales — "conexion.php" NO se sube a git
 * (está en .gitignore) porque tiene contraseñas y códigos maestros.
 *
 *   cp admin/config/conexion.example.php admin/config/conexion.php
 */

const DB_HOST = 'localhost';
const DB_NAME = 'revista_digital';
const DB_USER = 'root';
const DB_PASS = '';
const DB_CHARSET = 'utf8mb4';

/**
 * Código maestro para recuperar la contraseña del panel (admin/recuperar.php).
 * CÁMBIALO por algo tuyo y guárdalo en un lugar seguro. Quien lo tenga puede
 * poner una contraseña nueva a cualquier usuario, así que no lo compartas.
 */
const CODIGO_RECUPERACION = 'CAMBIA-ESTE-CODIGO';

/**
 * Código de invitación para crear una cuenta desde el login (admin/registrarse.php).
 * Las cuentas creadas así son siempre de rol 'autor' (solo crean borradores).
 * CÁMBIALO y compártelo únicamente con quien deba tener cuenta.
 * Si lo dejas vacío ('') se desactiva el registro público.
 */
const CODIGO_REGISTRO = 'CAMBIA-ESTE-CODIGO';

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        exit('Error de conexión a la base de datos: ' . htmlspecialchars($e->getMessage()));
    }

    return $pdo;
}
