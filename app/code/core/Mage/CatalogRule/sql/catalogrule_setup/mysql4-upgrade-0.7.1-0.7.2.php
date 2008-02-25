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
 * @package    Mage_CatalogRule
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
$websites = $conn->fetchPairs("select store_id, website_id from {$this->getTable('core_store')}");


// catalogrule
$ruleTable = $this->getTable('catalogrule');
$conn->addColumn($ruleTable, 'website_ids', 'text');
$q = $conn->query("select rule_id, store_ids from `$ruleTable`");
while ($r = $q->fetch()) {
    $websiteIds = array();
    foreach (explode(',',$r['store_ids']) as $storeId) {
        if (($storeId!=='') && isset($websites[$storeId])) {
            $websiteIds[$websites[$storeId]] = true;
        }
    }
    $conn->update($ruleTable, array('website_ids'=>join(',',array_keys($websiteIds))), "rule_id=".$r['rule_id']);
}
$conn->dropColumn($ruleTable, 'store_ids');


// catalogrule_product
$ruleProductTable = $this->getTable('catalogrule_product');
$conn->addColumn($ruleProductTable, 'website_id', 'smallint unsigned not null');
$unique = array();
$q = $conn->query("select * from `$ruleProductTable`");
while ($r = $q->fetch()) {
    $websiteId = $websites[$r['store_id']];
    $key = $r['from_time'].'|'.$r['to_time'].'|'.$websiteId.'|'.$r['customer_group_id'].'|'.$r['product_id'].'|'.$r['sort_order'];
    if (isset($unique[$key])) {
        $conn->delete($ruleProductTable, $conn->quoteInto("rule_product_id=?", $r['rule_product_id']));
    } else {
        $conn->update($ruleProductTable, array('website_id'=>$websiteId), "rule_product_id=".$r['rule_product_id']);
        $unique[$key] = true;
    }
}
$conn->dropKey($ruleProductTable, 'sort_order');
$conn->raw_query("ALTER TABLE `$ruleProductTable` ADD UNIQUE KEY `sort_order` (`from_time`,`to_time`,`website_id`,`customer_group_id`,`product_id`,`sort_order`)");

$conn->dropForeignKey($ruleProductTable, 'FK_catalogrule_product_store');
$conn->dropColumn($ruleProductTable, 'store_id');

$conn->dropForeignKey($ruleProductTable, 'FK_catalogrule_product_website');
$conn->raw_query("ALTER TABLE `$ruleProductTable` ADD CONSTRAINT `FK_catalogrule_product_website` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE");


// catalogrule_product_price
$ruleProductPriceTable = $this->getTable('catalogrule_product_price');
$conn->addColumn($ruleProductPriceTable, 'website_id', 'smallint unsigned not null');
$conn->delete($ruleProductPriceTable);

$conn->dropKey($ruleProductPriceTable, 'rule_date');
$conn->raw_query("ALTER TABLE `$ruleProductPriceTable` ADD UNIQUE KEY `rule_date` (`rule_date`,`website_id`,`customer_group_id`,`product_id`)");

$conn->dropForeignKey($ruleProductPriceTable, 'FK_catalogrule_product_store');
$conn->dropColumn($ruleProductPriceTable, 'store_id');

$conn->dropForeignKey($ruleProductPriceTable, 'FK_catalogrule_product_price_website');
$conn->raw_query("ALTER TABLE `$ruleProductPriceTable` ADD CONSTRAINT `FK_catalogrule_product_price_website` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE");


$installer->endSetup();
