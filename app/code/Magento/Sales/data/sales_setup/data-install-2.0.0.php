<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Sales\Model\Resource\Setup */

/**
 * Install eav entity types to the eav/entity_type table
 */
$this->installEntities();

/**
 * Install order statuses from config
 */
$data = array();
$statuses = array(
    'pending' => __('Pending'),
    'pending_payment' => __('Pending Payment'),
    'processing' => __('Processing'),
    'holded' => __('On Hold'),
    'complete' => __('Complete'),
    'closed' => __('Closed'),
    'canceled' => __('Canceled'),
    'fraud' => __('Suspected Fraud'),
    'payment_review' => __('Payment Review')
);
foreach ($statuses as $code => $info) {
    $data[] = array('status' => $code, 'label' => $info);
}
$this->getConnection()->insertArray($this->getTable('sales_order_status'), array('status', 'label'), $data);

/**
 * Install order states from config
 */
$data = array();
$states = array(
    'new' => array(
        'label' => __('New'),
        'statuses' => array('pending' => array('default' => '1')),
        'visible_on_front' => true
    ),
    'pending_payment' => array(
        'label' => __('Pending Payment'),
        'statuses' => array('pending_payment' => array('default' => '1'))
    ),
    'processing' => array(
        'label' => __('Processing'),
        'statuses' => array('processing' => array('default' => '1')),
        'visible_on_front' => true
    ),
    'complete' => array(
        'label' => __('Complete'),
        'statuses' => array('complete' => array('default' => '1')),
        'visible_on_front' => true
    ),
    'closed' => array(
        'label' => __('Closed'),
        'statuses' => array('closed' => array('default' => '1')),
        'visible_on_front' => true
    ),
    'canceled' => array(
        'label' => __('Canceled'),
        'statuses' => array('canceled' => array('default' => '1')),
        'visible_on_front' => true
    ),
    'holded' => array(
        'label' => __('On Hold'),
        'statuses' => array('holded' => array('default' => '1')),
        'visible_on_front' => true
    ),
    'payment_review' => array(
        'label' => __('Payment Review'),
        'statuses' => array('payment_review' => array('default' => '1'), 'fraud' => array()),
        'visible_on_front' => true
    )
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
$this->getConnection()->insertArray(
    $this->getTable('sales_order_status_state'),
    array('status', 'state', 'is_default'),
    $data
);

$entitiesToAlter = array('quote_address', 'order_address');

$attributes = array(
    'vat_id' => array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT),
    'vat_is_valid' => array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT),
    'vat_request_id' => array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT),
    'vat_request_date' => array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT),
    'vat_request_success' => array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT)
);

foreach ($entitiesToAlter as $entityName) {
    foreach ($attributes as $attributeCode => $attributeParams) {
        $this->addAttribute($entityName, $attributeCode, $attributeParams);
    }
}

/** Update visibility for states */
$states = array('new', 'processing', 'complete', 'closed', 'canceled', 'holded', 'payment_review');
foreach ($states as $state) {
    $this->getConnection()->update(
        $this->getTable('sales_order_status_state'),
        array('visible_on_front' => 1),
        array('state = ?' => $state)
    );
}
