<?php
require 'vendor/autoload.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

// Claves VAPID de ejemplo (debes generar nuevas para producción)
$auth = [
    'VAPID' => [
        'subject' => 'mailto:admin@tudominio.com',
        'publicKey' => 'TU_PUBLIC_KEY_AQUI',
        'privateKey' => 'TU_PRIVATE_KEY_AQUI',
    ],
];

// Suscripción de prueba (deberías obtenerla y almacenarla al suscribirte en frontend)
$subscription = Subscription::create([
    'endpoint' => 'ENDPOINT_DEL_CLIENTE',
    'publicKey' => 'LLAVE_PUBLICA_DEL_CLIENTE',
    'authToken' => 'TOKEN_AUTH_DEL_CLIENTE',
]);

// Crear objeto WebPush
$webPush = new WebPush($auth);

// Cargar contenido de la notificación
$payload = json_encode([
    'title' => 'Alerta de Sensor',
    'body' => 'Se detectó un valor fuera de rango.'
]);

// Enviar notificación
$webPush->queueNotification($subscription, $payload);

// Ejecutar envío
foreach ($webPush->flush() as $report) {
    $endpoint = $report->getRequest()->getUri()->__toString();
    if ($report->isSuccess()) {
        echo "Push enviado correctamente a {$endpoint}.\n";
    } else {
        echo "Error enviando push a {$endpoint}: {$report->getReason()}.\n";
    }
}
