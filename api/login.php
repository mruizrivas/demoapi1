<?php
require 'ApiMedidas.php';

$api = new ApiMedidas();

$cuenta = $_POST['cuenta'] ?? '';
$clave_acceso = $_POST['clave_acceso'] ?? '';

if (!$cuenta || !$clave_acceso) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan parámetros.']);
    exit;
}

$api->login($cuenta, $clave_acceso);
