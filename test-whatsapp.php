<?php

$from = "YAKOAFRICA";
$phoneNumber = "2250500130762";
$message = "Cher client(e) , la pré-declaration de sinistre n° SIN-WZN62 a been accepted. Merci de vous rendre en Agence YAKO AFRICA, muni des originaux des documents.";

$url = "https://wp2e3q.api.infobip.com/sms/2/text/advanced";
$apiKey = "ca9b1e97d87d27dc425b2d598aa83c46-cbbd83f5-f0af-49ae-9bc0-02ba090ecac3";

$headers = [
    'Authorization: App ' . $apiKey,
    'Content-Type: application/json',
    'Accept: application/json',
];

$body = [
    "messages" => [
        [
            "from" => $from,
            "destinations" => [["to" => $phoneNumber]],
            "text" => "$message"
        ]
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

$response = curl_exec($ch);
curl_close($ch);

//@file_put_contents('sms-notif-ynov.log', date('Y-m-d H:i:s') . '|' . $phoneNumber . '|' . $from . '| ' . $message . '| result = ' . json_encode($response) . ': ' . PHP_EOL, FILE_APPEND);

if ($response === false) {
    print_r(json_encode(['error' => 'Erreur lors de l\'envoi du SMS via Infobip : ' . curl_error($ch)]));
}
//return json_decode($response, true);
print_r(json_decode($response, true));
