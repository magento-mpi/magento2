<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Global array that holds test scenarios data
 *
 * @var array
 */
$taxCalculationData = [];

require __DIR__ . '/scenarios/excluding_tax_apply_tax_after_discount.php';
require __DIR__ . '/scenarios/excluding_tax_apply_tax_before_discount.php';
require __DIR__ . '/scenarios/excluding_tax_unit.php';
require __DIR__ . '/scenarios/excluding_tax_row.php';
require __DIR__ . '/scenarios/excluding_tax_total.php';

