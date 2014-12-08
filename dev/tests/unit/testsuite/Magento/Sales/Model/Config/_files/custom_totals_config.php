<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$result = require __DIR__ . '/core_totals_config.php';
$result += [
    'handling' => ['after' => ['shipping'], 'before' => ['tax']],
    'handling_tax' => ['after' => ['tax_shipping'], 'before' => ['tax']],
    'own_subtotal' => ['after' => ['nominal'], 'before' => ['subtotal']],
    'own_total1' => ['after' => ['nominal'], 'before' => ['subtotal']],
    'own_total2' => ['after' => ['nominal'], 'before' => ['subtotal']]
];
return $result;
