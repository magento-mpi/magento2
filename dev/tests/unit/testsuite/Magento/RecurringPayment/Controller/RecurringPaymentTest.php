<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RecurringPayment\Controller;

class RecurringPaymentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\RecurringPayment\Controller\RecurringPayment
     */
    protected $_controller;

    protected function setUp()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_controller = $objectHelper->getObject('Magento\RecurringPayment\Controller\RecurringPayment');
    }

    public function testOrdersAction()
    {
        $this->assertTrue(method_exists($this->_controller, 'ordersAction'));
    }
}
