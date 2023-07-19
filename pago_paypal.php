<?php

require 'vendor/autoload.php';

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

$payer = new Payer();
$payer->setPaymentMethod("paypal");

$clientId = 'AWnNf840GxHOPV3UbpmStjjEr9MXo6tjOW86JONwaBlizOeE4H8VqoZbE0vEElUZxPMyNhcR-hF-CaZE';
$clientSecret = 'EBaw6gsQa_XVHMOlKggULTX0WZPMNA2kVmxdsSLjkgGm2IeUXb-6fUD_vLlmlTXXiUxXZxuU9VYC-Qag';

$apiContext = new ApiContext(
    new OAuthTokenCredential($clientId, $clientSecret)
);

$apiContext->setConfig(
    array(
        'mode' => 'sandbox', // Cambia a 'live' para entorno de producción
        'http.ConnectionTimeOut' => 30,
        'log.LogEnabled' => false,
        'log.FileName' => '',
        'log.LogLevel' => 'FINE',
        'validation.level' => 'log'
    )
);

$item1 = new Item();
$item1->setName('Ground Coffee 40 oz')
    ->setCurrency('USD')
    ->setQuantity(1)
    ->setSku("123123") // Similar to `item_number` in Classic API
    ->setPrice(7.5);
$item2 = new Item();
$item2->setName('Granola bars')
    ->setCurrency('USD')
    ->setQuantity(5)
    ->setSku("321321") // Similar to `item_number` in Classic API
    ->setPrice(2);

$itemList = new ItemList();
$itemList->setItems(array($item1, $item2));


$details = new Details();
$details->setShipping(1.2)
    ->setTax(1.3)
    ->setSubtotal(18.50);


$amount = new Amount();
$amount->setCurrency("USD")
    ->setTotal(20)
    ->setDetails($details);


$transaction = new Transaction();
$transaction->setAmount($amount)
    ->setItemList($itemList)
    ->setDescription("Payment description");


$redirectUrls = new RedirectUrls();
$redirectUrls->setReturnUrl('http://localhost:8080/PaginaWebGM/pago_concretado.php')
    ->setCancelUrl('http://localhost:8080/PaginaWebGM/index.php');


$payment = new Payment();
$payment->setIntent("sale")
    ->setPayer($payer)
    ->setRedirectUrls($redirectUrls)
    ->setTransactions(array($transaction));


$request = clone $payment;


try {
    $payment->create($apiContext);
    $approvalUrl = $payment->getApprovalLink();
    header("Location: $approvalUrl");
    exit;
} catch (Exception $ex) {
    echo ($ex);
}

?>

