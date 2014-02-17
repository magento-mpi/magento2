<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RecurringProfile\Controller;

class RecurringProfileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\RecurringProfile\Controller\RecurringProfile
     */
    protected $_controller;

    protected function setUp()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_controller = $objectHelper->getObject('Magento\RecurringProfile\Controller\RecurringProfile');
    }

    public function testOrdersAction()
    {
        $this->assertTrue(method_exists($this->_controller, 'ordersAction'));
    }
}
