<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require 'quote_with_address.php';

$quote->setReservedOrderId(
    'test_order_1_with_payment'
);

$paymentDetails = [
    'transaction_id' => 100500,
    'consumer_key'   => '123123q'
];

$quote->getPayment()
    ->setMethod('checkmo')
    ->setPoNumber('poNumber')
    ->setCcCidEnc('ccCid')
    ->setCcOwner('tester')
    ->setCcNumberEnc('1000-2000-3000-4000')
    ->setCcType('visa')
    ->setCcExpYear(2014)
    ->setCcExpMonth(1)
    ->setAdditionalData(serialize($paymentDetails));

$quote->collectTotals()->save();
