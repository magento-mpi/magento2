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

$quote->getPayment()
    ->setMethod('checkmo')
    ->setPoNumber('poNumber')
    ->setCcCidEnc('ccCid')
    ->setCcOwner('tester')
    ->setCcNumberEnc('1000-2000-3000-4000')
    ->setCcType('visa')
    ->setCcExpYear(2014)
    ->setCcExpMonth(1);

$quote->collectTotals()->save();