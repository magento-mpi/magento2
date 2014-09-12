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

    protected function setUp()
    {
        $this->_segmentHelper = $this->getMock(
            'Magento\CustomerSegment\Helper\Data',
            array('isEnabled', 'addSegmentFieldsToForm'),
            array(),
            '',
            false
        );
        $coreRegistry = $this->getMock('Magento\Framework\Registry', array(), array(), '', false);
        $this->_model = new \Magento\CustomerSegment\Model\Observer(
            $this->getMock('Magento\Framework\StoreManagerInterface', array(), array(), '', false),
            $this->getMock('Magento\Customer\Model\Session', array(), array(), '', false),
            $this->getMock('Magento\CustomerSegment\Model\Customer', array(), array(), '', false),
            $this->getMock('Magento\Backend\Model\Config\Source\Yesno', array(), array(), '', false),
            $this->_segmentHelper,
            $coreRegistry
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
            array(),
            array(),
            '',
            false
        );

        $layout = $this->getMock('Magento\Framework\View\Layout', array('createBlock'), array(), '', false);
        $layout->expects(
            $this->once()
        )->method(
            'createBlock'
        )->with(
            'Magento\Backend\Block\Widget\Form\Element\Dependence'
        )->will(
            $this->returnValue($formDependency)
        );

        $factoryElement = $this->getMock('Magento\Framework\Data\Form\Element\Factory', array(), array(), '', false);
        $collectionFactory = $this->getMock(
            'Magento\Framework\Data\Form\Element\CollectionFactory',
            array('create'),
            array(),
            '',
            false
        );
        $formKey = $this->getMock('Magento\Framework\Data\Form\FormKey', array(), array(), '', false);
        $form = new \Magento\Framework\Data\Form($factoryElement, $collectionFactory, $formKey);
        $model = new \Magento\Framework\Object();
        $block = new \Magento\Framework\Object(array('layout' => $layout));

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
                array(
                    'event' => new \Magento\Framework\Object(
                            array('form' => $form, 'model' => $model, 'block' => $block)
                        )
                )
            )
        );
    }

    public function testAddFieldsToTargetRuleFormDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $layout = $this->getMock('Magento\Framework\View\Layout', array('createBlock'), array(), '', false);
        $layout->expects($this->never())->method('createBlock');

        $factoryElement = $this->getMock('Magento\Framework\Data\Form\Element\Factory', array(), array(), '', false);
        $collectionFactory = $this->getMock(
            'Magento\Framework\Data\Form\Element\CollectionFactory',
            array('create'),
            array(),
            '',
            false
        );
        $formKey = $this->getMock('Magento\Framework\Data\Form\FormKey', array(), array(), '', false);
        $form = new \Magento\Framework\Data\Form($factoryElement, $collectionFactory, $formKey);
        $model = new \Magento\Framework\Object();
        $block = new \Magento\Framework\Object(array('layout' => $layout));

        $this->_segmentHelper->expects($this->never())->method('addSegmentFieldsToForm');

        $this->_model->addFieldsToTargetRuleForm(
            new \Magento\Framework\Event\Observer(
                array(
                    'event' => new \Magento\Framework\Object(
                            array('form' => $form, 'model' => $model, 'block' => $block)
                        )
                )
            )
        );
    }
}
