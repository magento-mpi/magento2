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
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $_blockFactory;

    /**
     * @var \Magento\Registry
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

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_blockFactory = $this->getMock(
            'Magento\Framework\View\Element\BlockFactory',
            array('createBlock'),
            array(),
            '',
            false
        );

        $this->_registry = $this->getMock('Magento\Registry', array(), array(), '', false);

        $this->_eventManager = $this->getMock('Magento\Framework\Event\Manager', array(), array(), '', false);
        $this->_scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->_context = $helper->getObject(
            'Magento\Backend\Block\Template\Context',
            array('eventManager' => $this->_eventManager, 'scopeConfig' => $this->_scopeConfig)
        );

        $this->_testModel = $helper->getObject(
            'Magento\RecurringPayment\Block\Adminhtml\Product\Edit\Tab\Price\Recurring',
            array('blockFactory' => $this->_blockFactory, 'registry' => $this->_registry, 'context' => $this->_context)
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
        $map = array(
            array('Magento\RecurringPayment\Block\Adminhtml\Payment\Edit\Form', array(), $blockMock),
            array('Magento\Backend\Block\Widget\Form\Element\Dependence', array(), $blockMock)
        );
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

        $this->_blockFactory->expects($this->any())->method('createBlock')->will($this->returnValueMap($map));

        $blockMock->expects($this->any())->method('setNameInLayout');
        $blockMock->expects($this->once())->method('setProductEntity')->with($product);
        $blockMock->expects($this->exactly(2))->method('toHtml')->will($this->returnValue('html'));
        $blockMock->expects($this->once())->method('addConfigOptions')->with(array('levels_up' => 2));

        $this->assertEquals('htmlhtml', $this->_testModel->getElementHtml());
    }
}
