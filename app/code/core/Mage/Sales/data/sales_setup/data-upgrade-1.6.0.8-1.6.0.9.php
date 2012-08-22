<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Sales_Model_Resource_Setup */
$installer = $this;
/** @var Mage_Sales_Model_Order_Payment $modelOrderPayment */
$modelOrderPayment = Mage::getModel('Mage_Sales_Model_Order_Payment');

/** @var Mage_Sales_Model_Quote_Payment $modelQuotePayment */
$modelQuotePayment = Mage::getModel('Mage_Sales_Model_Quote_Payment');

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
        $modelOrderPayment->setData('method', $order['method']);
        $modelOrderPayment->setCcExpMonth($order['cc_exp_month']);
        $modelOrderPayment->setCcExpYear($order['cc_exp_year']);
        $modelOrderPayment->setCcOwner($order['cc_owner']);

        $installer->getConnection()
            ->update(
                $installer->getTable('sales_flat_order_payment'),
                array(
                    'cc_exp_month' => $modelOrderPayment->getData('cc_exp_month'),
                    'cc_exp_year' => $modelOrderPayment->getData('cc_exp_year'),
                    'cc_owner' => $modelOrderPayment->getData('cc_owner'),
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
        $modelQuotePayment->setData('method', $quote['method']);
        $modelQuotePayment->setCcExpMonth($quote['cc_exp_month']);
        $modelQuotePayment->setCcExpYear($quote['cc_exp_year']);
        $modelQuotePayment->setCcOwner($quote['cc_owner']);

        $installer->getConnection()
            ->update(
                $installer->getTable('sales_flat_quote_payment'),
                array(
                    'cc_exp_month' => $modelQuotePayment->getData('cc_exp_month'),
                    'cc_exp_year' => $modelQuotePayment->getData('cc_exp_year'),
                    'cc_owner' => $modelQuotePayment->getData('cc_owner'),
                ),
                array('payment_id = ?' => $quote['payment_id'])
        );
    }

} while (count($quotes) > 0);

$installer->endSetup();
