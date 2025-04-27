<?php
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class ApiMedidas
{
    private $pdo;
    private $key = 'TU_CLAVE_SECRETA_AQUI'; // Cambia esto por una clave secreta segura

    public function __construct()
    {
        $host = 'localhost';
        $dbname = 'nombre_base_de_datos';
        $user = 'usuario_base_de_datos';
        $pass = 'contraseña_base_de_datos';

        $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function login($cuenta, $clave_acceso)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM USUARIOS WHERE cuenta = :cuenta AND clave_acceso = :clave_acceso');
        $stmt->execute([
            ':cuenta' => $cuenta,
            ':clave_acceso' => md5($clave_acceso)
        ]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            http_response_code(401);
            echo json_encode(['error' => 'Autenticación fallida.']);
            exit;
        }

        $payload = [
            'iss' => 'tu-servidor',
            'aud' => 'tus-clientes',
            'iat' => time(),
            'exp' => time() + 3600,
            'sub' => $usuario['id'],
            'cuenta' => $usuario['cuenta']
        ];

        $jwt = JWT::encode($payload, $this->key, 'HS256');

        echo json_encode(['token' => $jwt]);
    }

    public function obtenerMedidas($token, $fecha_inicio, $fecha_fin, $tension_min = null, $tension_max = null, $peso_min = null, $peso_max = null)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            $id_usuario = $decoded->sub;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Token inválido: ' . $e->getMessage()]);
            exit;
        }

        $query = "SELECT id, zona, fecha, tension, peso, estructura, observaciones
                  FROM MEDIDAS
                  WHERE fecha BETWEEN :fecha_inicio AND :fecha_fin";

        $params = [
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin
        ];

        if ($tension_min !== null) {
            $query .= " AND tension >= :tension_min";
            $params[':tension_min'] = $tension_min;
        }
        if ($tension_max !== null) {
            $query .= " AND tension <= :tension_max";
            $params[':tension_max'] = $tension_max;
        }
        if ($peso_min !== null) {
            $query .= " AND peso >= :peso_min";
            $params[':peso_min'] = $peso_min;
        }
        if ($peso_max !== null) {
            $query .= " AND peso <= :peso_max";
            $params[':peso_max'] = $peso_max;
        }

        $query .= " ORDER BY fecha ASC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);

        $medidas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['datos' => $medidas]);
    }
}
