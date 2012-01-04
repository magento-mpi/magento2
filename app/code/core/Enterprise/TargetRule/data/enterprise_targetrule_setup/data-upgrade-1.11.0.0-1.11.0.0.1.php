<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
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

$indexer = Mage::getModel('index/process');
foreach ($indexerCodes as $code) {
    $indexer->load($code, 'indexer_code')
        ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
}
