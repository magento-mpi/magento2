<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Paypal\Block\PayflowExpress;

class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Paypal\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_paypalConfig;

    /**
     * @var Form
     */
    protected $_model;

    protected function setUp()
    {
        $this->_paypalConfig = $this->getMock('Magento\Paypal\Model\Config', [], [], '', false);
        $this->_paypalConfig->expects($this->once())->method('setMethod')->will($this->returnSelf());
        $paypalConfigFactory = $this->getMock(
            'Magento\Paypal\Model\ConfigFactory',
            ['create'],
            [],
            '',
            false
        );
        $paypalConfigFactory->expects($this->once())->method('create')->will($this->returnValue($this->_paypalConfig));
        $mark = $this->getMock('Magento\Framework\View\Element\Template', [], [], '', false);
        $mark->expects($this->once())->method('setTemplate')->will($this->returnSelf());
        $mark->expects($this->any())->method('__call')->will($this->returnSelf());
        $layout = $this->getMockForAbstractClass('Magento\Framework\View\LayoutInterface');
        $layout->expects(
            $this->once()
        )->method(
            'createBlock'
        )->with(
            'Magento\Framework\View\Element\Template'
        )->will(
            $this->returnValue($mark)
        );
        $localeResolver = $this->getMock(
            'Magento\Framework\Locale\ResolverInterface',
            [],
            [],
            '',
            false,
            false
        );
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $helper->getObject(
            'Magento\Paypal\Block\PayflowExpress\Form',
            [
                'paypalConfigFactory' => $paypalConfigFactory,
                'layout' => $layout,
                'localeResolver' => $localeResolver
            ]
        );
    }

    public function testGetBillingAgreementCode()
    {
        $this->assertFalse($this->_model->getBillingAgreementCode());
    }
}
