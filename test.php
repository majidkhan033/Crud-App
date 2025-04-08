<?php
require 'vendor/autoload.php';

$client = new MongoDB\Client("mongodb://127.0.0.1:27017");
$collection = $client->phpdemo->users;

$result = $collection->insertOne(['name' => 'Test User', 'email' => 'test@example.com']);
echo "Inserted with ID: " . $result->getInsertedId();
