<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$result = require __DIR__ . '/core_totals_config.php';
$result += array(
    'handling' => array('after' => array('shipping'), 'before' => array('tax')),
    'handling_tax' => array('after' => array('tax_shipping'), 'before' => array('tax')),
    'own_subtotal' => array('after' => array('nominal'), 'before' => array('subtotal')),
    'own_total1' => array('after' => array('nominal'), 'before' => array('subtotal')),
    'own_total2' => array('after' => array('nominal'), 'before' => array('subtotal'))
);
return $result;
