<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Block_Adminhtml_Cms_Page_Edit_Tab_GoogleoptimizerTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_contextMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_registryMock;

    /**
     * @var Mage_GoogleOptimizer_Block_Adminhtml_Catalog_Product_Edit_Tab_Googleoptimizer
     */
    protected $_block;

    public function setUp()
    {
        $this->_contextMock = $this->getMock('Mage_Core_Block_Template_Context', array(), array(), '', false);
        $this->_helperMock = $this->getMock('Mage_GoogleOptimizer_Helper_Data', array(), array(), '', false);
        $this->_registryMock = $this->getMock('Mage_Core_Model_Registry', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $objectManagerHelper->getObject(
            'Mage_GoogleOptimizer_Block_Adminhtml_Cms_Page_Edit_Tab_Googleoptimizer', array(
            'context' => $this->_contextMock, 'helperData' => $this->_helperMock, 'registry' => $this->_registryMock
        ));
    }

    public function testGetTabLabel()
    {
        $this->_helperMock->expects($this->once())->method('__')
            ->will($this->returnArgument(0));
        $this->assertEquals('Page View Optimization', $this->_block->getTabLabel());
    }

    public function testGetTabTitle()
    {
        $this->_helperMock->expects($this->once())->method('__')
            ->will($this->returnArgument(0));
        $this->assertEquals('Page View Optimization', $this->_block->getTabTitle());
    }

    public function testCanShowTab()
    {
        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')
            ->will($this->returnValue(true));
        $this->assertEquals(true, $this->_block->canShowTab());
    }

    public function testIsHidden()
    {
        $this->assertEquals(false, $this->_block->isHidden());
    }
}
