<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Helper\Shortcut;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Paypal\Helper\Shortcut\Factory */
    protected $factory;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $objectManagerMock;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManager');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->factory = $this->objectManagerHelper->getObject(
            'Magento\Paypal\Helper\Shortcut\Factory',
            [
                'objectManager' => $this->objectManagerMock
            ]
        );
    }

    public function testCreateDefault()
    {
        $instance = $this->getMockBuilder('Magento\Paypal\Helper\Shortcut\ValidatorInterface')->getMock();

        $this->objectManagerMock->expects($this->once())->method('create')->with(Factory::DEFAULT_VALIDATOR)
            ->will($this->returnValue($instance));

        $this->assertInstanceOf(
            'Magento\Paypal\Helper\Shortcut\ValidatorInterface',
            $this->factory->create()
        );
    }

    public function testCreateCheckout()
    {
        $checkoutMock = $this->getMockBuilder('Magento\Checkout\Model\Session')->disableOriginalConstructor()
            ->setMethods([])->getMock();
        $instance = $this->getMockBuilder('Magento\Paypal\Helper\Shortcut\ValidatorInterface')->getMock();

        $this->objectManagerMock->expects($this->once())->method('create')->with(Factory::CHECKOUT_VALIDATOR)
            ->will($this->returnValue($instance));

        $this->assertInstanceOf(
            'Magento\Paypal\Helper\Shortcut\ValidatorInterface',
            $this->factory->create($checkoutMock)
        );
    }
}
