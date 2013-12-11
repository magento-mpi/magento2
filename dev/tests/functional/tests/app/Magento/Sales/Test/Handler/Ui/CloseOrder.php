<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Handler\Ui;

use Mtf\Fixture;
use Mtf\Handler\Ui;
use Mtf\Factory\Factory;

/**
 * Class CreatePaypalExpressOrder
 * Create a product
 *
 * @package Magento\Checkout\Test\Handler\Ui
 */
class CloseOrder extends Ui
{
    /**
     * Close sales order
     *
     * @param Fixture $fixture [optional]
     * @return mixed|string
     */
    public function execute(Fixture $fixture = null)
    {
        $orderId = $fixture->getOrderId();
        //Pages
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $newInvoicePage = Factory::getPageFactory()->getSalesOrderInvoiceNew();
        $newShipmentPage = Factory::getPageFactory()->getSalesOrderShipmentNew();

        Factory::getApp()->magentoBackendLoginUser();

        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));

        //Create the Shipment
        $orderPage->getOrderActionsBlock()->ship();
        $newShipmentPage->getTotalsBlock()->submit();

        //Create the Invoice
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $orderPage->getOrderActionsBlock()->invoice();
        $newInvoicePage->getInvoiceTotalsBlock()->submit();

        return $orderPage;
    }
}