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
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Customer_Model_Entity_Setup  */

$installer->startSetup();

$customerTaxClassIds = $installer->getConnection()->fetchCol(
    "SELECT class_id FROM {$installer->getTable('tax_class')}
        WHERE class_type = 'CUSTOMER'
        ORDER BY class_id ASC"
);

if (count($customerTaxClassIds) > 0) {
    $installer->run(
        "UPDATE {$installer->getTable('customer_group')}
            SET tax_class_id = {$customerTaxClassIds[0]}
            WHERE tax_class_id NOT IN (".implode(',', $customerTaxClassIds).")"
    );
}

$installer->endSetup();