<?php
require 'ApiMedidas.php';

$api = new ApiMedidas();

$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Token no proporcionado.']);
    exit;
}

list($type, $token) = explode(' ', $headers['Authorization'], 2);

// Leer parámetros GET
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$tension_min = $_GET['tension_min'] ?? null;
$tension_max = $_GET['tension_max'] ?? null;
$peso_min = $_GET['peso_min'] ?? null;
$peso_max = $_GET['peso_max'] ?? null;

if (!$fecha_inicio || !$fecha_fin) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan parámetros de fecha.']);
    exit;
}

$api->obtenerMedidas($token, $fecha_inicio, $fecha_fin, $tension_min, $tension_max, $peso_min, $peso_max);
