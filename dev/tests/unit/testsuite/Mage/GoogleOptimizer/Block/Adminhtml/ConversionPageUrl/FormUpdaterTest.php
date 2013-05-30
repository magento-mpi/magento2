<?php
/**
 * Test class for Mage_GoogleOptimizer_Block_Adminhtml_ConversionPageUrl_FormUpdater
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_GoogleOptimizer
 * @subpackage  unit_tests
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_GoogleOptimizer_Block_Adminhtml_ConversionPageUrl_FormUpdaterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_GoogleOptimizer_Block_Adminhtml_ConversionPageUrl_FormUpdater
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fieldset;

    public function setUp()
    {
        $this->_storeManagerMock = $this->getMock('Mage_Core_Model_StoreManagerInterface', array(), array(), '', false);
        $this->_helperMock = $this->getMock('Mage_GoogleOptimizer_Helper_Data', array(), array(), '', false);
        $this->_fieldset = $this->getMock('Varien_Data_Form_Element_Fieldset', array(), array(), '', false);
        $this->_model = new Mage_GoogleOptimizer_Block_Adminhtml_ConversionPageUrl_FormUpdater(
            $this->_storeManagerMock,
            $this->_helperMock
        );
    }

    public function testUpdateIfStoreIdNull()
    {
        $this->_storeManagerMock->expects($this->once())->method('hasSingleStore')->will($this->returnValue(false));
        $this->_fieldset->expects($this->once())->method('addField')->with('conversion_page_url', 'note');
        $this->_model->update(0, $this->_fieldset);
    }

    public function testUpdateIfStoreNotNull()
    {
        $this->_storeManagerMock->expects($this->never())->method('hasSingleStore');
        $this->_fieldset->expects($this->once())->method('addField')->with('conversion_page_url', 'text');
        $this->_model->update(1, $this->_fieldset);
    }

    public function testUpdateIfStoreHasOneStoreView()
    {
        $this->_storeManagerMock->expects($this->once())->method('hasSingleStore')->will($this->returnValue(true));
        $this->_fieldset->expects($this->once())->method('addField')->with('conversion_page_url', 'text');
        $this->_model->update(0, $this->_fieldset);
    }
}
