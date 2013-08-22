<?php
/**
 * Save quote_with_address fixture
 *
 * The quote is not saved inside the original fixture. It is later saved inside child fixtures, but along with some
 * additional data which may break some tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require 'quote_with_address.php';

$quote->collectTotals()->save();
