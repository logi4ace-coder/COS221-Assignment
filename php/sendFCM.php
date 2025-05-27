<?php
require 'vendor/autoload.php';
require_once '../../../Assignment/configs/send.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;


$factory = (new Factory)->withServiceAccount('../../../Assignment/configs/comparexit-bb66b-481e252f25ad.json');

$messaging = $factory->createMessaging();


$deviceToken = 'user_token_fcm';


$message = CloudMessage::withTarget('token', $deviceToken)
    ->withNotification(Notification::create('📢 Price Drop!', 'A product you’re watching just dropped in price.'))
    ->withData(['productId' => '123', 'newPrice' => '99.99']);

try {
    $messaging->send($message);
    echo "✅ Notification sent.";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
