<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Block\Info;

class SubstitutionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    /**
     * @var \Magento\Payment\Block\Info\Substitution
     */
    protected $block;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->layout = $this->getMockBuilder(
            'Magento\View\LayoutInterface'
        )->disableOriginalConstructor()->setMethods(
            []
        )->getMock();

        $eventManager = $this->getMockBuilder(
            'Magento\Event\ManagerInterface'
        )->disableOriginalConstructor()->setMethods(
            []
        )->getMock();

        $storeConfig = $this->getMockBuilder(
            'Magento\Core\Model\Store\Config'
        )->disableOriginalConstructor()->setMethods(
            []
        )->getMock();
        $storeConfig->expects(
            $this->any()
        )->method(
            'getConfig'
        )->with(
            $this->stringContains(
                'advanced/modules_disable_output/'
            )
        )->will(
            $this->returnValue(
                false
            )
        );

        $context = $this->getMockBuilder(
            'Magento\View\Element\Template\Context'
        )->disableOriginalConstructor()->setMethods(
            ['getLayout', 'getEventManager', 'getStoreConfig']
        )->getMock();
        $context->expects(
            $this->any()
        )->method(
            'getLayout'
        )->will(
            $this->returnValue(
                $this->layout
            )
        );
        $context->expects(
            $this->any()
        )->method(
            'getEventManager'
        )->will(
            $this->returnValue(
                $eventManager
            )
        );
        $context->expects(
            $this->any()
        )->method(
            'getStoreConfig'
        )->will(
            $this->returnValue(
                $storeConfig
            )
        );

        $this->block = $this->objectManager->getObject(
            'Magento\Payment\Block\Info\Substitution',
            [
                'context' => $context,
                'data' => [
                    'template' => null
                ]
            ]
        );
    }

    public function testBeforeToHtml()
    {
        $abstractBlock = $this->getMockBuilder(
            'Magento\View\Element\AbstractBlock'
        )->disableOriginalConstructor()->setMethods(
            []
        )->getMock();
        $childAbstractBlock = clone($abstractBlock);

        $abstractBlock->expects($this->any())->method('getParentBlock')->will($this->returnValue($childAbstractBlock));

        $this->layout->expects($this->any())->method('getParentName')->will($this->returnValue('parentName'));
        $this->layout->expects($this->any())->method('getBlock')->will($this->returnValue($abstractBlock));

        $infoMock = $this->getMockBuilder(
            'Magento\Payment\Model\Info'
        )->disableOriginalConstructor()->setMethods(
            []
        )->getMock();
        $methodMock = $this->getMockBuilder(
            'Magento\Payment\Model\MethodInterface'
        )->disableOriginalConstructor()->setMethods(
            []
        )->getMock();
        $infoMock->expects($this->once())->method('getMethodInstance')->will($this->returnValue($methodMock));
        $this->block->setInfo($infoMock);

        $fakeBlock = new \StdClass;
        $this->layout->expects(
            $this->any()
        )->method(
            'createBlock'
        )->with(
            'Magento\View\Element\Template',
            '',
            ['data' => ['method' => $methodMock, 'template' => 'Magento_Payment::info/substitution.phtml']]
        )->will(
                $this->returnValue(
                    $fakeBlock
                )
            );

        $childAbstractBlock->expects(
            $this->any()
        )->method(
            'setChild'
        )->with(
            'order_payment_additional',
            $fakeBlock
        );

        $this->block->toHtml();
    }
}
