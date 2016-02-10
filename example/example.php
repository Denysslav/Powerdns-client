<?php
/**
 * Created by IntelliJ IDEA.
 * User: Denislav
 * Date: 28.1.2016 г.
 * Time: 12:55
 */

require_once '../vendor/autoload.php';

use PowerdnsClient\PowerdnsClient;

$client = new PowerdnsClient();
$client->setHeaders(['Accept' => 'application/json', 'Content-Type' => 'application/json', 'X-API-Key' => 'your-api-key']);
$client->setBaseUri('http://your-server-url/');
$client->init();

$data = [
    'name' => "test2.org",
    "kind" => "Native",
    "masters" => [],
    "nameservers" => []
];

echo "<pre>";
var_dump($client->createZone($data));
echo "</pre>" . PHP_EOL;

echo "<pre>";
var_dump($client->getZone('test2.org'));
echo "</pre>";