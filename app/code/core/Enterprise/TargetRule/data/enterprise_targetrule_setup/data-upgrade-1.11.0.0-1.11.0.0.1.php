<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Enterprise_GiftRegistry_Model_Resource_Setup */
$installer = $this;

if ($installer->getAttributeId('catalog_product', 'related_targetrule_position_limit')
    && !$installer->getAttributeId('catalog_product',  'related_tgtr_position_limit')
) {
    $installer->updateAttribute(
        'catalog_product',
        'related_targetrule_position_limit',
        'attribute_code',
        'related_tgtr_position_limit'
    );
}

if ($installer->getAttributeId('catalog_product', 'related_targetrule_position_behavior')
    && !$installer->getAttributeId('catalog_product', 'related_tgtr_position_behavior')
) {
    $installer->updateAttribute(
        'catalog_product',
        'related_targetrule_position_behavior',
        'attribute_code',
        'related_tgtr_position_behavior'
    );
}

if ($installer->getAttributeId('catalog_product', 'upsell_targetrule_position_limit')
    && !$installer->getAttributeId('catalog_product', 'upsell_tgtr_position_limit')
) {
    $installer->updateAttribute(
        'catalog_product',
        'upsell_targetrule_position_limit',
        'attribute_code',
        'upsell_tgtr_position_limit'
    );
}

if ($installer->getAttributeId('catalog_product', 'upsell_targetrule_position_behavior')
    && !$installer->getAttributeId('catalog_product', 'upsell_tgtr_position_behavior')
) {
    $installer->updateAttribute(
        'catalog_product',
        'upsell_targetrule_position_behavior',
        'attribute_code',
        'upsell_tgtr_position_behavior'
    );
}

$indexerCodes = array(
    'catalog_product_attribute',
    'catalog_product_price',
    'catalog_product_flat'
);

$indexer = Mage::getModel('Mage_Index_Model_Process');
foreach ($indexerCodes as $code) {
    $indexer->load($code, 'indexer_code')
        ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
}
