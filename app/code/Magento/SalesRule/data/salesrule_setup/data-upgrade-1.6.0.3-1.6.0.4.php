<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\SalesRule\Model\Resource\Setup */
$installer = $this->getMigrationModel();
$installer->startSetup();

$installer->appendClassAliasReplace('salesrule', 'conditions_serialized',
    \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Core\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('rule_id')
);
$installer->appendClassAliasReplace('salesrule', 'actions_serialized',
    \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Core\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('rule_id')
);

$installer->doUpdateClassAliases();

$installer->endSetup();
