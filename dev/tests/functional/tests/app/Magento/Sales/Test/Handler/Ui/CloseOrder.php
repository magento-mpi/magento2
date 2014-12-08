<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Handler\Ui;

use Mtf\Factory\Factory;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Ui;

/**
 * Class CloseOrder
 * Closes a sales order
 *
 */
class CloseOrder extends Ui
{
    /**
     * Close sales order
     *
     * @param FixtureInterface $fixture [optional]
     * @return void
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $orderId = $fixture->getOrderId();
        //Pages
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $newInvoicePage = Factory::getPageFactory()->getSalesOrderInvoiceNew();
        $newShipmentPage = Factory::getPageFactory()->getSalesOrderShipmentNew();

        Factory::getApp()->magentoBackendLoginUser();

        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(['id' => $orderId]);

        //Create the Shipment
        $orderPage->getOrderActionsBlock()->ship();
        $newShipmentPage->getTotalsBlock()->submit();

        //Create the Invoice
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(['id' => $orderId]);
        $orderPage->getOrderActionsBlock()->invoice();
        $newInvoicePage->getTotalsBlock()->submit();
    }
}
