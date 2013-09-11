<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer \Magento\Catalog\Model\Resource\Setup */

$installer->installEntities();

foreach (array('news_from_date', 'custom_design_from') as $attributeCode) {
    $installer->updateAttribute(
        \Magento\Catalog\Model\Product::ENTITY,
        $attributeCode,
        'backend_model',
        '\Magento\Catalog\Model\Product\Attribute\Backend\Startdate'
    );
}
