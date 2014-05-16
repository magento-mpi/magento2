<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Sales\Model\Resource\Setup */
$installer = $this;
$installer->startSetup();

/** Update visibility for states */
$states = array('new', 'processing', 'complete', 'closed', 'canceled', 'holded', 'payment_review');
foreach ($states as $state) {
    $installer->getConnection()->update(
        $installer->getTable('sales_order_status_state'),
        array('visible_on_front' => 1),
        array('state = ?' => $state)
    );
}
$installer->endSetup();
