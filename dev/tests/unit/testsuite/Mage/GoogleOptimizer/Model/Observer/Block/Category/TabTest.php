<?php
/**
 * Google Optimizer Observer Category Tab
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_Block_Category_TabTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layoutMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventMock;

    /**
     * @var Mage_GoogleOptimizer_Model_Observer_Block_Category_Tab
     */
    protected $_observer;

    public function setUp()
    {
        $this->_layoutMock = $this->getMock('Mage_Core_Model_Layout', array(), array(), '', false);
        $this->_helperMock = $this->getMock('Mage_GoogleOptimizer_Helper_Data', array(), array(), '', false);
        $this->_eventMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_observer = $objectManagerHelper->getObject(
            'Mage_GoogleOptimizer_Model_Observer_Block_Category_Tab', array(
            'helper' => $this->_helperMock, 'layout' => $this->_layoutMock
        ));
    }

    public function testAddGoogleExperimentTabSuccess()
    {
        $tabs = $this->getMock('Mage_Adminhtml_Block_Catalog_Category_Tabs', array(),  array(), '', false);
        $event = $this->getMock('Varien_Event', array('getTabs'), array(), '', false);
        $event->expects($this->once())->method('getTabs')->will($this->returnValue($tabs));
        $tabs->expects($this->once())->method('addTab');
        $this->_eventMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));
        $this->_helperMock->expects($this->once())->method('__');

        $block = $this->getMock('Mage_Core_Block', array('toHtml'));


        $this->_layoutMock->expects($this->once())->method('createBlock')->with(
            'Mage_GoogleOptimizer_Block_Adminhtml_Catalog_Category_Edit_Tab_Googleoptimizer',
            'google-experiment-form'
        )->will($this->returnValue($block));

        $block->expects($this->once())->method('toHtml')->will($this->returnValue('sdsdf'));

        $this->_observer->addGoogleExperimentTab($this->_eventMock);
    }

    public function testAddGoogleExperimentTabFail()
    {
        $tabs = $this->getMock('Mage_Adminhtml_Block_Catalog_Category_Tabs', array(),  array(), '', false);
        $event = $this->getMock('Varien_Event', array('getTabs'), array(), '', false);

        $event->expects($this->once())->method('getTabs')->will($this->returnValue($tabs));
        $this->_eventMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(false));

        $this->_helperMock->expects($this->never())->method('__');
        $tabs->expects($this->never())->method('addTab');
        $this->_layoutMock->expects($this->never())->method('createBlock');

        $this->_observer->addGoogleExperimentTab($this->_eventMock);
    }
}
