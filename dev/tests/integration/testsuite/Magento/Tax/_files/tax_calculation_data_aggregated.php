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

require_once __DIR__ . '/scenarios/excluding_tax_apply_tax_after_discount.php';
require_once __DIR__ . '/scenarios/excluding_tax_apply_tax_before_discount.php';
require_once __DIR__ . '/scenarios/excluding_tax_unit.php';
require_once __DIR__ . '/scenarios/excluding_tax_row.php';
require_once __DIR__ . '/scenarios/excluding_tax_total.php';
require_once __DIR__ . '/scenarios/including_tax_unit.php';
require_once __DIR__ . '/scenarios/including_tax_row.php';
require_once __DIR__ . '/scenarios/including_tax_total.php';
require_once __DIR__ . '/scenarios/excluding_tax_multi_item_unit.php';
require_once __DIR__ . '/scenarios/excluding_tax_multi_item_row.php';
require_once __DIR__ . '/scenarios/excluding_tax_multi_item_total.php';

