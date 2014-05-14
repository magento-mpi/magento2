<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/* @var $installer \Magento\Framework\Module\Setup */

$data = array();
$statuses = array(
    'pending_ogone' => __('Pending Ogone'),
    'cancel_ogone' => __('Cancelled Ogone'),
    'decline_ogone' => __('Declined Ogone'),
    'processing_ogone' => __('Processing Ogone Payment'),
    'processed_ogone' => __('Processed Ogone Payment'),
    'waiting_authorozation' => __('Waiting Authorization')
);
foreach ($statuses as $code => $info) {
    $data[] = array('status' => $code, 'label' => $info);
}
$installer->getConnection()->insertArray($installer->getTable('sales_order_status'), array('status', 'label'), $data);

$data = array();
$states = array(
    'pending_payment' => array('statuses' => array('pending_ogone' => array())),
    'processing' => array('statuses' => array('processed_ogone' => array()))
);

foreach ($states as $code => $info) {
    if (isset($info['statuses'])) {
        foreach ($info['statuses'] as $status => $statusInfo) {
            $data[] = array(
                'status' => $status,
                'state' => $code,
                'is_default' => is_array($statusInfo) && isset($statusInfo['default']) ? 1 : 0
            );
        }
    }
}
$installer->getConnection()->insertArray(
    $installer->getTable('sales_order_status_state'),
    array('status', 'state', 'is_default'),
    $data
);
