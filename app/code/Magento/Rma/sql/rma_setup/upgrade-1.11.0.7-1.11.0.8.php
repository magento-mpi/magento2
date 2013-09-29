<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Rma\Model\Resource\Setup */
//Product's Attribute is_returnable shouldn't be applied to grouped product
//Because it has no sense
/** @var \Magento\Catalog\Model\Resource\Setup $installer */
$installer = $this->getCatalogSetup(array('resourceName' => 'catalog_setup'));

$applyTo = \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE . ',' .
    \Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE . ',' .
    \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE;

$installer->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'is_returnable', 'apply_to', $applyTo);
