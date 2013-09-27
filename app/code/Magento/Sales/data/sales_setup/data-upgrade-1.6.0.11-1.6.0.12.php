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
