<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->changeColumn(
    $installer->getTable('tax_calculation_rate'),
    'tax_postcode',
    'tax_postcode',
    'VARCHAR(21) NULL DEFAULT NULL'
);
$installer->endSetup();
