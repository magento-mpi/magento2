<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @package     Enterprise_Reward
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$customerEntity = $installer->getEntityType('customer');
$customerEntityTypeId = $customerEntity['entity_type_id'];
$customerEntityTable = $this->getTable($customerEntity['entity_table']);
$updateAttributeId = $installer->getAttributeId($customerEntityTypeId, 'reward_update_notification');
$warningAttributeId = $installer->getAttributeId($customerEntityTypeId, 'reward_warning_notification');
$attributeTable = $installer->getAttributeTable($customerEntityTypeId, 'reward_update_notification');

$stmt = $installer->getConnection()->query("SELECT `entity_id` FROM `{$customerEntityTable}`");
$data = array();
while($row = $stmt->fetch()) {
    $sample = array(
        'entity_type_id' => $customerEntityTypeId,
        'attribute_id' => $updateAttributeId,
        'entity_id' => $row['entity_id'],
        'value' => 1
    );
    $data[] = $sample;
    $sample['attribute_id'] = $warningAttributeId;
    $data[] = $sample;
}
$installer->getConnection()->insertOnDuplicate($attributeTable, $data, array('value'));
$installer->endSetup();
