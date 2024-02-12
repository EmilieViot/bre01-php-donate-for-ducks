<?php

require_once '../../vendor/autoload.php';

// Chargement des variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable('../../config');
$dotenv->load();

// Utilisation des variables d'environnement
$APIKey = $_ENV['API_KEY'];

$stripe = new \Stripe\StripeClient($APIKey);


function calculateOrderAmount(int $amount): int 
{
    // Calculate the order total on the server to prevent people from directly manipulating the amount on the client
    $totalAmount = $amount * 100;
    
    return $totalAmount;
}

header('Content-Type: application/json');

try {
    // retrieve JSON from POST body
    $jsonStr = file_get_contents('php://input');
    $jsonObj = json_decode($jsonStr);
    
    // TODO : Create a PaymentIntent with amount and currency in '$paymentIntent'
    $stripe = new \Stripe\StripeClient($APIKey);
    $amount = calculateOrderAmount($jsonObj->amount);
    $intent = $stripe->paymentIntents->create(
      [
        'amount' => $amount,
        'currency' => 'eur',
      ]
    );
    

    $output = [
        'clientSecret' => $intent->client_secret,
    ];

    echo json_encode($output);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

