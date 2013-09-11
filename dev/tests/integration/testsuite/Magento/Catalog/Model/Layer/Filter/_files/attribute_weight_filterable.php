<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = Mage::getResourceModel('\Magento\Catalog\Model\Resource\Setup', array('resourceName' => 'catalog_setup'));

$installer->updateAttribute('catalog_product', 'weight', 'is_filterable', 1);

