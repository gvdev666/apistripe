<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
// Permite solicitudes desde cualquier origen (modifica según tu necesidad)
header("Access-Control-Allow-Origin: *");  // O usa 'http://localhost:5173' si deseas limitarlo a tu frontend específico
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // Métodos permitidos
header("Access-Control-Allow-Headers: Content-Type, Authorization");  // Encabezados permitidos

// Si es una solicitud OPTIONS (preflight), responde con un código 200 sin procesar la solicitud real
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Incluir la librería Stripe
require_once 'vendor/autoload.php';


\Stripe\Stripe::setApiKey('sk_test_51RNfDME40jbMpv95rrhu2rg6ZVXnMPQbpgsDhNclTeDacH21dwmvwYnRSWGOEcZCmvyL3QBTNlP4zop7GTxmrRXk009o45r8oV');

// Configurar el Content-Type como JSON
header('Content-Type: application/json');

// Obtener el monto desde la solicitud
$data = json_decode(file_get_contents('php://input'), true);
$amount = $data['amount']; // El monto debería venir en centavos (por ejemplo, 6364600 = 63646.00 USD)

// Crear el Payment Intent
try {
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount,
        'currency' => 'usd', // Puedes cambiarlo a la moneda que prefieras
        'description' => 'Compra en tu tienda', // Descripción de la compra
    ]);

    // Enviar el clientSecret al frontend
    echo json_encode([
        'clientSecret' => $paymentIntent->client_secret,
    ]);
} catch (\Stripe\Exception\ApiErrorException $e) {
    // Manejar errores de la API de Stripe
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
