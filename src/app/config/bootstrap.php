<?php
/**
 * bootstrap.php
 *
 * PHP Version 8.2+
 *
 * @author David Ghyse <david.ghysefree.fr>
 * @version XXX
 * @package app\config
 */
use Dotenv\Dotenv;

/**
 * @param string $name environment var
 * @return bool
 */
function getboolenv($name, $default = false) : bool {
    $value = $_ENV[$name] ?? ($_SERVER[$name] ?? $default);
    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
}

/**
 * @param string $name environment var
 * @param string $default default value
 * @return string
 */
function getstrenv($name, $default = '') : string {
    $value = $_ENV[$name] ?? ($_SERVER[$name] ?? false);
    return $value === false ? $default : $value;
}

/**
 * @param string $name environment var
 * @param int $default default value
 * @return int
 */
function getintenv($name, $default = 0) : int {
    $value = $_ENV[$name] ?? ($_SERVER[$name] ?? false);
    if ($value !== false) {
        $value = filter_var($value, FILTER_VALIDATE_INT);
    }
    return $value === false ? (int)$default : $value;
}

try {
    $dotEnv = Dotenv::createImmutable(dirname(__DIR__, 3));
    $dotEnv->safeLoad();
    $dotEnv->required([
        'DB_DRIVER',
        'DB_DATABASE',
        'DB_USER',
        'DB_HOST',
        'DB_PORT',
        'DB_PASSWORD',
    ]);
    $dotEnv->required('REQUEST_KEY')->notEmpty();
    $dotEnv->required('DB_DRIVER')->allowedValues(['mysql', 'pgsql']);
} catch (Exception $e) {
    die('Application not configured : '.$e->getMessage());
}