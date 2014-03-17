<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

$tableName = $installer->getTable('admin_rule');
/** @var \Magento\DB\Adapter\AdapterInterface $connection */
$connection = $installer->getConnection();

$condition = $connection->prepareSqlCondition(
    'resource_id',
    array(
        array('like' => '%xmlconnect%'),
        array(
            'in' => array(
                'admin/system/convert/gui',
                'Magento_Adminhtml::gui',
                'admin/system/convert/profiles',
                'Magento_Adminhtml::profiles'
            )
        )
    )
);
$connection->delete($tableName, $condition);
