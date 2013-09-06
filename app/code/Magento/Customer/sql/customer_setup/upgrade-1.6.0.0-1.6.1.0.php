<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Magento_Customer_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

// Add reset password link token attribute
$installer->addAttribute('customer', 'rp_token', array(
    'type'     => 'varchar',
    'input'    => 'hidden',
    'visible'  => false,
    'required' => false
));

// Add reset password link token creation date attribute
$installer->addAttribute('customer', 'rp_token_created_at', array(
    'type'           => 'datetime',
    'input'          => 'date',
    'validate_rules' => 'a:1:{s:16:"input_validation";s:4:"date";}',
    'visible'        => false,
    'required'       => false
));

$installer->endSetup();
