<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerSegment\Model\Observer
     */
    private $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_segmentHelper;

    /**
     * @var \Magento\Customer\Model\CustomerFactory |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerFactoryMock;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $extensibleDataObjectConverterMock;

    protected function setUp()
    {
        $this->_segmentHelper = $this->getMock(
            'Magento\CustomerSegment\Helper\Data',
            ['isEnabled', 'addSegmentFieldsToForm'],
            [],
            '',
            false
        );
        $this->customerFactoryMock = $this->getMock('Magento\Customer\Model\CustomerFactory', [], [], '', false);
        $this->extensibleDataObjectConverterMock = $this->getMock(
            'Magento\Framework\Api\ExtensibleDataObjectConverter',
            [],
            [],
            '',
            false
        );
        $coreRegistry = $this->getMock('Magento\Framework\Registry', [], [], '', false);
        $this->_model = new \Magento\CustomerSegment\Model\Observer(
            $this->getMock('Magento\Framework\StoreManagerInterface', [], [], '', false),
            $this->getMock('Magento\Customer\Model\Session', [], [], '', false),
            $this->getMock('Magento\CustomerSegment\Model\Customer', [], [], '', false),
            $this->getMock('Magento\Backend\Model\Config\Source\Yesno', [], [], '', false),
            $this->_segmentHelper,
            $coreRegistry,
            $this->customerFactoryMock,
            $this->extensibleDataObjectConverterMock
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_segmentHelper = null;
    }

    public function testAddFieldsToTargetRuleForm()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(true));

        $formDependency = $this->getMock(
            'Magento\Backend\Block\Widget\Form\Element\Dependence',
            [],
            [],
            '',
            false
        );

        $layout = $this->getMock('Magento\Framework\View\Layout', ['createBlock'], [], '', false);
        $layout->expects(
            $this->once()
        )->method(
            'createBlock'
        )->with(
            'Magento\Backend\Block\Widget\Form\Element\Dependence'
        )->will(
            $this->returnValue($formDependency)
        );

        $factoryElement = $this->getMock('Magento\Framework\Data\Form\Element\Factory', [], [], '', false);
        $collectionFactory = $this->getMock(
            'Magento\Framework\Data\Form\Element\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $formKey = $this->getMock('Magento\Framework\Data\Form\FormKey', [], [], '', false);
        $form = new \Magento\Framework\Data\Form($factoryElement, $collectionFactory, $formKey);
        $model = new \Magento\Framework\Object();
        $block = new \Magento\Framework\Object(['layout' => $layout]);

        $this->_segmentHelper->expects(
            $this->once()
        )->method(
            'addSegmentFieldsToForm'
        )->with(
            $form,
            $model,
            $formDependency
        );

        $this->_model->addFieldsToTargetRuleForm(
            new \Magento\Framework\Event\Observer(
                [
                    'event' => new \Magento\Framework\Object(
                            ['form' => $form, 'model' => $model, 'block' => $block]
                        ),
                ]
            )
        );
    }

    public function testAddFieldsToTargetRuleFormDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $layout = $this->getMock('Magento\Framework\View\Layout', ['createBlock'], [], '', false);
        $layout->expects($this->never())->method('createBlock');

        $factoryElement = $this->getMock('Magento\Framework\Data\Form\Element\Factory', [], [], '', false);
        $collectionFactory = $this->getMock(
            'Magento\Framework\Data\Form\Element\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $formKey = $this->getMock('Magento\Framework\Data\Form\FormKey', [], [], '', false);
        $form = new \Magento\Framework\Data\Form($factoryElement, $collectionFactory, $formKey);
        $model = new \Magento\Framework\Object();
        $block = new \Magento\Framework\Object(['layout' => $layout]);

        $this->_segmentHelper->expects($this->never())->method('addSegmentFieldsToForm');

        $this->_model->addFieldsToTargetRuleForm(
            new \Magento\Framework\Event\Observer(
                [
                    'event' => new \Magento\Framework\Object(
                            ['form' => $form, 'model' => $model, 'block' => $block]
                        ),
                ]
            )
        );
    }
}
