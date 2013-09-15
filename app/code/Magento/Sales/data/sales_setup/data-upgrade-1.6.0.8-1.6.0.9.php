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
$itemsPerPage = 1000;
$currentPosition = 0;

/** Update sales order payment */
do {
    $select = $installer->getConnection()
        ->select()
        ->from(
        $installer->getTable('sales_flat_order_payment'),
        array('entity_id', 'cc_owner', 'cc_exp_month', 'cc_exp_year', 'method')
    )
        ->where('method = ?', 'ccsave')
        ->limit($itemsPerPage, $currentPosition);

    $orders = $select->query()->fetchAll();
    $currentPosition += $itemsPerPage;

    foreach ($orders as $order) {
        $installer->getConnection()
            ->update(
                $installer->getTable('sales_flat_order_payment'),
                array(
                    'cc_exp_month' => $installer->getCoreData()->encrypt($order['cc_exp_month']),
                    'cc_exp_year' => $installer->getCoreData()->encrypt($order['cc_exp_year']),
                    'cc_owner' => $installer->getCoreData()->encrypt($order['cc_owner']),
                ),
                array('entity_id = ?' => $order['entity_id'])
        );
    }

} while (count($orders) > 0);

/** Update sales quote payment */
$currentPosition = 0;
do {
    $select = $installer->getConnection()
        ->select()
        ->from(
        $installer->getTable('sales_flat_quote_payment'),
        array('payment_id', 'cc_owner', 'cc_exp_month', 'cc_exp_year', 'method')
    )
        ->where('method = ?', 'ccsave')
        ->limit($itemsPerPage, $currentPosition);

    $quotes = $select->query()->fetchAll();
    $currentPosition += $itemsPerPage;

    foreach ($quotes as $quote) {
        $installer->getConnection()
            ->update(
                $installer->getTable('sales_flat_quote_payment'),
                array(
                    'cc_exp_month' => $installer->getCoreData()->encrypt($quote['cc_exp_month']),
                    'cc_exp_year' => $installer->getCoreData()->encrypt($quote['cc_exp_year']),
                    'cc_owner' => $installer->getCoreData()->encrypt($quote['cc_owner']),
                ),
                array('payment_id = ?' => $quote['payment_id'])
        );
    }

} while (count($quotes) > 0);

$installer->endSetup();
