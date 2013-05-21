<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Block_Adminhtml_Catalog_Product_Edit_Tab_GoogleoptimizerTest
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
        $formMock = $this->getMock('Varien_Data_Form', array('setParent', 'setBaseUrl'), array(), '', false);
        $urlBuilderMock = $this->getMock('Mage_Core_Model_UrlInterface', array(), array(), '', false);

        $formMock->expects($this->once())->method('setParent');
        $formMock->expects($this->once())->method('setBaseUrl');
        $urlBuilderMock->expects($this->once())->method('getBaseUrl');
        $this->_contextMock->expects($this->once())->method('getUrlBuilder')->will($this->returnValue($urlBuilderMock));

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $objectManagerHelper->getObject(
            'Mage_GoogleOptimizer_Block_Adminhtml_Catalog_Product_Edit_Tab_Googleoptimizer', array(
            'context' => $this->_contextMock,
            'helperData' => $this->_helperMock,
            'registry' => $this->_registryMock,
            'form' => $formMock
        ));
    }

    public function testGetTabLabel()
    {
        $this->_helperMock->expects($this->once())->method('__')->will($this->returnArgument(0));
        $this->assertEquals('Product View Optimization', $this->_block->getTabLabel());
    }

    public function testGetTabTitle()
    {
        $this->_helperMock->expects($this->once())->method('__')
            ->will($this->returnArgument(0));
        $this->assertEquals('Product View Optimization', $this->_block->getTabTitle());
    }

    public function testCanShowTab()
    {
        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')
            ->will($this->returnValue(true));
        $product = $this->getMock('Mage_Catalog_Model_Product', array('getStoreId'), array(), '', false);
        $this->_registryMock->expects($this->once())->method('registry')->with('product')
            ->will($this->returnValue($product));
        $product->expects($this->once())->method('getStoreId');
        $this->assertEquals(true, $this->_block->canShowTab());
    }

    public function testIsHidden()
    {
        $this->assertEquals(false, $this->_block->isHidden());
    }
}
