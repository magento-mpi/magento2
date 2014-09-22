<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = $this;

foreach (array('news_from_date', 'custom_design_from') as $attributeCode) {
    $installer->updateAttribute(
        \Magento\Catalog\Model\Product::ENTITY,
        $attributeCode,
        'backend_model',
        'Magento\Catalog\Model\Product\Attribute\Backend\Startdate'
    );
}
