<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Controller\Recurring;

class ProfileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Controller\Recurring\Profile
     */
    protected $_controller;

    protected function setUp()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_controller = $objectHelper->getObject('Magento\Sales\Controller\Recurring\Profile');
    }

    public function testOrdersAction()
    {
        $this->assertTrue(method_exists($this->_controller, 'ordersAction'));
    }
}
