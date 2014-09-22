<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = $this;

$msrpEnabled = $installer->getAttribute('catalog_product', 'msrp_enabled', 'apply_to');
if ($msrpEnabled && strstr($msrpEnabled, \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE) == false) {
    $installer->updateAttribute(
        'catalog_product',
        'msrp_enabled',
        array('apply_to' => $msrpEnabled . ',' . \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE)
    );
}

$msrpDisplay = $installer->getAttribute('catalog_product', 'msrp_display_actual_price_type', 'apply_to');
if ($msrpDisplay && strstr($msrpEnabled, \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE) == false) {
    $installer->updateAttribute(
        'catalog_product',
        'msrp_display_actual_price_type',
        array('apply_to' => $msrpDisplay . ',' . \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE)
    );
}

$msrp = $installer->getAttribute('catalog_product', 'msrp', 'apply_to');
if ($msrp && strstr($msrpEnabled, \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE) == false) {
    $installer->updateAttribute(
        'catalog_product',
        'msrp',
        array('apply_to' => $msrp . ',' . \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE)
    );
}
