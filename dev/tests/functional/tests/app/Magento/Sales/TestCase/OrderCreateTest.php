<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

class OrderCreateTest extends Functional
{
    public function testCreateOrder()
    {
        $orderGridPage = Factory::getPageFactory()->getAdminSalesOrder();
        $gridPageActionsBlock = $orderGridPage->getPageActionsBlock();
        $orderCreatePage = Factory::getPageFactory()->getAdminSalesOrderCreateIndex();
        $customerSelectionGrid = $orderCreatePage->getOrderCustomerBlock();

        Factory::getApp()->magentoBackendLoginUser();
        $orderGridPage->open();
        $gridPageActionsBlock->addNew();

        //TODO: make possible to avoid creating new and select existing customer instead
        $customerSelectionGrid->createNewCustomer();



    }
}
