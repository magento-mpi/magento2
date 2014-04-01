<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
        $this->_paypalConfig = $this->getMock('Magento\Paypal\Model\Config', array(), array(), '', false);
        $this->_paypalConfig->expects($this->once())->method('setMethod')->will($this->returnSelf());
        $paypalConfigFactory = $this->getMock(
            'Magento\Paypal\Model\ConfigFactory',
            array('create'),
            array(),
            '',
            false
        );
        $paypalConfigFactory->expects($this->once())->method('create')->will($this->returnValue($this->_paypalConfig));
        $mark = $this->getMock('Magento\View\Element\Template', array(), array(), '', false);
        $mark->expects($this->once())->method('setTemplate')->will($this->returnSelf());
        $mark->expects($this->any())->method('__call')->will($this->returnSelf());
        $layout = $this->getMockForAbstractClass('Magento\View\LayoutInterface');
        $layout->expects(
            $this->once()
        )->method(
            'createBlock'
        )->with(
            'Magento\View\Element\Template'
        )->will(
            $this->returnValue($mark)
        );
        $localeResolver = $this->getMock('Magento\Locale\ResolverInterface', array(), array(), '', false, false);
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
