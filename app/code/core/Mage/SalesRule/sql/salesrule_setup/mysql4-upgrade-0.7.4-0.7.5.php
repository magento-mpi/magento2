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

$conn->addColumn($this->getTable('salesrule'), 'website_ids', 'text');
$q = $conn->query("select rule_id, store_ids from {$this->getTable('salesrule')}");
while ($r = $q->fetch()) {
    $websiteIds = array();
    foreach (explode(',',$r['store_ids']) as $storeId) {
        if ($storeId!=='') {
            $websiteIds[$websites[$storeId]] = true;
        }
    }
    $conn->update($this->getTable('salesrule'),
        array('website_ids'=>join(',',array_keys($websiteIds))),
        "rule_id=".$r['rule_id']
    );
}
$conn->dropColumn($this->getTable('salesrule'), 'store_ids');

$installer->endSetup();