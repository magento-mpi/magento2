<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    // Totals declared in Magento_Sales
    'nominal' => array(
        'before' => array('subtotal'),
        'after'  => array(),
    ),
    'subtotal' => array(
        'after'  => array('nominal'),
        'before' => array('grand_total'),
    ),
    'shipping' => array(
        'after'  => array('subtotal', 'freeshipping', 'tax_subtotal'),
        'before' => array('grand_total'),
    ),
    'grand_total' => array(
        'after'  => array('subtotal'),
        'before' => array(),
    ),
    'msrp' => array(
        'after'  => array(),
        'before' => array(),
    ),
    // Totals declared in Magento_SalesRule
    'freeshipping' => array(
        'after'  => array('subtotal'),
        'before' => array('tax_subtotal', 'shipping'),
    ),
    'discount' => array(
        'after'  => array('subtotal', 'shipping'),
        'before' => array('grand_total'),
    ),
    // Totals declared in Magento_Tax
    'tax_subtotal' => array(
        'after'  => array('freeshipping'),
        'before' => array('tax', 'discount'),
    ),
    'tax_shipping' => array(
        'after'  => array('shipping'),
        'before' => array('tax', 'discount'),
    ),
    'tax' => array(
        'after'  => array('subtotal', 'shipping'),
        'before' => array('grand_total'),
    ),
    // Totals declared in Magento_Weee
    'weee' => array(
        'after'  => array('subtotal', 'tax', 'discount', 'grand_total', 'shipping'),
        'before' => array(),
    ),
);
