<?php
/**
 * Google Optimizer Observer Category Tab
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleOptimizer_Model_Observer_Block_Category_TabTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layoutMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_tabsMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventObserverMock;

    /**
     * @var Magento_GoogleOptimizer_Model_Observer_Block_Category_Tab
     */
    protected $_modelObserver;

    /**
     * @var Magento_GoogleOptimizer_Model_Observer_Block_Category_Tab
     */
    protected $_observer;

    public function setUp()
    {
        $this->_helperMock = $this->getMock('Magento_GoogleOptimizer_Helper_Data', array(), array(), '', false);
        $this->_layoutMock = $this->getMock('Magento_Core_Model_Layout', array(), array(), '', false);
        $this->_tabsMock = $this->getMock('Magento_Adminhtml_Block_Catalog_Category_Tabs', array(), array(), '', false);
        $this->_eventObserverMock = $this->getMock('Magento_Event_Observer', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_modelObserver = $objectManagerHelper->getObject(
            'Magento_GoogleOptimizer_Model_Observer_Block_Category_Tab',
            array(
                'helper' => $this->_helperMock,
                'layout' => $this->_layoutMock,
            )
        );
    }

    public function testAddGoogleExperimentTabSuccess()
    {
        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));

        $block = $this->getMock('Magento_Core_Block', array(), array(), '', false);
        $block->expects($this->once())->method('toHtml')->will($this->returnValue('generated html'));

        $this->_layoutMock->expects($this->once())->method('createBlock')->with(
            'Magento_GoogleOptimizer_Block_Adminhtml_Catalog_Category_Edit_Tab_Googleoptimizer',
            'google-experiment-form'
        )->will($this->returnValue($block));

        $event = $this->getMock('Magento_Event', array('getTabs'), array(), '', false);
        $event->expects($this->once())->method('getTabs')->will($this->returnValue($this->_tabsMock));
        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $this->_tabsMock->expects($this->once())->method('addTab')->with('google-experiment-tab', array(
            'label' => new Magento_Phrase('Category View Optimization'),
            'content' => 'generated html',
        ));

        $this->_modelObserver->addGoogleExperimentTab($this->_eventObserverMock);
    }

    public function testAddGoogleExperimentTabFail()
    {
        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(false));
        $this->_layoutMock->expects($this->never())->method('createBlock');
        $this->_tabsMock->expects($this->never())->method('addTab');
        $this->_eventObserverMock->expects($this->never())->method('getEvent');

        $this->_modelObserver->addGoogleExperimentTab($this->_eventObserverMock);
    }
}
