<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Block\Adminhtml\Product\Edit\Tab\Price;

class RecurringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\RecurringPayment\Block\Adminhtml\Product\Edit\Tab\Price\Recurring
     */
    protected $_testModel;

    /**
     * @var \Magento\Backend\Block\Template\Context
     */
    protected $_context;

    /**
     * @var \Magento\Framework\Event\Manager
     */
    protected $_eventManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_registry = $this->getMock('Magento\Framework\Registry', array(), array(), '', false);

        $this->_eventManager = $this->getMock('Magento\Framework\Event\Manager', array(), array(), '', false);
        $this->_scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->layoutMock = $this->getMockBuilder('Magento\Framework\View\LayoutInterface')->getMock();
        $this->_context = $helper->getObject(
            'Magento\Backend\Block\Template\Context',
            array(
                'eventManager' => $this->_eventManager,
                'scopeConfig' => $this->_scopeConfig,
                'layout' => $this->layoutMock
            )
        );

        $this->_testModel = $helper->getObject(
            'Magento\RecurringPayment\Block\Adminhtml\Product\Edit\Tab\Price\Recurring',
            array('registry' => $this->_registry, 'context' => $this->_context)
        );
    }

    public function testRenderRecurringPaymentForm()
    {
        $blockMock = $this->getMock(
            'Magento\Framework\View\Element\BlockInterface',
            array(
                'setNameInLayout',
                'setParentElement',
                'setProductEntity',
                'toHtml',
                'addFieldMap',
                'addFieldDependence',
                'addConfigOptions'
            )
        );
        $map = [
            [
                'Magento\RecurringPayment\Block\Adminhtml\Payment\Edit\Form',
                'adminhtml_recurring_payment_edit_form',
                array(),
                $blockMock],
            [
                'Magento\Backend\Block\Widget\Form\Element\Dependence',
                'adminhtml_recurring_payment_edit_form_dependence',
                array(),
                $blockMock
            ]
        ];
        $paymentElement = $this->getMock(
            'Magento\Framework\Data\Form\Element\AbstractElement',
            array(),
            array(),
            '',
            false
        );
        $this->_scopeConfig->expects($this->any())->method('getValue')->will($this->returnValue(true));

        $this->_testModel->render($paymentElement);

        $product = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->_registry->expects($this->once())->method('registry')->will($this->returnValue($product));
        $this->layoutMock->expects($this->any())
            ->method('createBlock')
            ->will($this->returnValueMap($map));

        $blockMock->expects($this->any())->method('setNameInLayout');
        $blockMock->expects($this->once())->method('setProductEntity')->with($product);
        $blockMock->expects($this->exactly(2))->method('toHtml')->will($this->returnValue('html'));
        $blockMock->expects($this->once())->method('addConfigOptions')->with(array('levels_up' => 2));

        $this->assertEquals('htmlhtml', $this->_testModel->getElementHtml());
    }
}
