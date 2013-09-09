<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Sales_Model_Resource_Setup */
$installer = $this;

/**
 * Install order statuses from config
 */
$data     = array();
$statuses = Mage::getConfig()->getNode('global/sales/order/statuses')->asArray();
foreach ($statuses as $code => $info) {
    $data[] = array(
        'status' => $code,
        'label'  => $info['label']
    );
}
$installer->getConnection()->insertArray(
    $installer->getTable('sales_order_status'),
    array('status', 'label'),
    $data
);

/**
 * Install order states from config
 */
$data   = array();
$states = $installer->getConfigModel()->getNode('global/sales/order/states')->asArray();

foreach ($states as $code => $info) {
    if (isset($info['statuses'])) {
        foreach ($info['statuses'] as $status => $statusInfo) {
            $data[] = array(
                'status'     => $status,
                'state'      => $code,
                'is_default' => is_array($statusInfo) && isset($statusInfo['@']['default']) ? 1 : 0
            );
        }
    }
}
$installer->getConnection()->insertArray(
    $installer->getTable('sales_order_status_state'),
    array('status', 'state', 'is_default'),
    $data
);
