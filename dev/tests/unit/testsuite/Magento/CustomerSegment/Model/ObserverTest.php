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
            'Magento\CustomerSegment\Helper\Data', array('isEnabled', 'addSegmentFieldsToForm'), array(), '', false
        );
        $coreRegistry = $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false);
        $this->_model = new \Magento\CustomerSegment\Model\Observer($this->_segmentHelper, $coreRegistry);
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
            'Magento\Backend\Block\Widget\Form\Element\Dependence', array(), array(), '', false
        );

        $layout = $this->getMock('Magento\Core\Model\Layout', array('createBlock'), array(), '', false);
        $layout
            ->expects($this->once())
            ->method('createBlock')
            ->with('Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->will($this->returnValue($formDependency))
        ;

        $factoryElement = $this->getMock('Magento\Data\Form\Element\Factory', array(), array(), '', false);
        $collectionFactory = $this->getMock('Magento\Data\Form\Element\CollectionFactory', array('create'),
            array(), '', false);
        $form = new \Magento\Data\Form($factoryElement, $collectionFactory);
        $model = new \Magento\Object();
        $block = new \Magento\Object(array('layout' => $layout));

        $this->_segmentHelper
            ->expects($this->once())->method('addSegmentFieldsToForm')->with($form, $model, $formDependency);

        $this->_model->addFieldsToTargetRuleForm(new \Magento\Event\Observer(array(
            'event' => new \Magento\Object(array('form' => $form, 'model' => $model, 'block' => $block)),
        )));
    }

    public function testAddFieldsToTargetRuleFormDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $layout = $this->getMock('Magento\Core\Model\Layout', array('createBlock'), array(), '', false);
        $layout->expects($this->never())->method('createBlock');

        $factoryElement = $this->getMock('Magento\Data\Form\Element\Factory', array(), array(), '', false);
        $collectionFactory = $this->getMock('Magento\Data\Form\Element\CollectionFactory', array('create'),
            array(), '', false);
        $form = new \Magento\Data\Form($factoryElement, $collectionFactory);
        $model = new \Magento\Object();
        $block = new \Magento\Object(array('layout' => $layout));

        $this->_segmentHelper->expects($this->never())->method('addSegmentFieldsToForm');

        $this->_model->addFieldsToTargetRuleForm(new \Magento\Event\Observer(array(
            'event' => new \Magento\Object(array('form' => $form, 'model' => $model, 'block' => $block)),
        )));
    }
}
