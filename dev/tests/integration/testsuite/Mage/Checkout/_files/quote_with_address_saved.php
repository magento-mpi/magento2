<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Original fixture doesn't save quote, and if we'll try to save it there - tests those depend on child fixtures like
 * quote_with_ccsave_payment.php are broken.
 */
require 'quote_with_address.php';

$quote->collectTotals()->save();
