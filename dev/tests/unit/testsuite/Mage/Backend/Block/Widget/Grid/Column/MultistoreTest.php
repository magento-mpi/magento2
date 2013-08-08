<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Block_Widget_Grid_Column_MultistoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Block_Widget_Grid_Column_Multistore
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    public function setUp()
    {
        $this->_storeManagerMock = $this->getMock('Magento_Core_Model_StoreManagerInterface');

        $arguments = array(
            'storeManager' => $this->_storeManagerMock,
            'urlBuilder' => $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false)
        );

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Mage_Backend_Block_Widget_Grid_Column_Multistore', $arguments);
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_storeManagerMock);
    }

    public function testIsDisplayedReturnsTrueInMultiStoreMode()
    {
        $this->_storeManagerMock->expects($this->once())->method('isSingleStoreMode')->will($this->returnValue(false));
        $this->assertTrue($this->_model->isDisplayed());
    }

    public function testIsDisplayedReturnsFalseInSingleStoreMode()
    {
        $this->_storeManagerMock->expects($this->once())->method('isSingleStoreMode')->will($this->returnValue(true));
        $this->assertFalse($this->_model->isDisplayed());
    }
}
