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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$attributes = array(
    $installer->getAttribute('catalog_product', 'price'),
    $installer->getAttribute('catalog_product', 'special_price'),
    $installer->getAttribute('catalog_product', 'special_from_date'),
    $installer->getAttribute('catalog_product', 'special_to_date'),
    $installer->getAttribute('catalog_product', 'cost'),
    $installer->getAttribute('catalog_product', 'tier_price'),
);

$installer->startSetup();
foreach ($attributes as $attr) {
    $attr['apply_to'] = array_flip(explode(',', $attr['apply_to']));
    unset($attr['apply_to']['grouped']);
    $attr['apply_to'] = implode(',', array_flip($attr['apply_to']));

    $installer->run("UPDATE `{$installer->getTable('eav_attribute')}`
                SET `apply_to` = '{$attr['apply_to']}'
                WHERE `attribute_id` = {$attr['attribute_id']}");
}
$installer->endSetup();