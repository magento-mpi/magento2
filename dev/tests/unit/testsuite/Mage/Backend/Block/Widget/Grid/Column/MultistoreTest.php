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
    protected $_appMock;

    public function setUp()
    {
        $this->_appMock = $this->getMock('Mage_Core_Model_App');
        $this->_model = new Mage_Backend_Block_Widget_Grid_Column_Multistore(array('app' => $this->_appMock));
    }

    public function testIsDisplayedReturnsTrueInMultiStoreMode()
    {
        $this->_appMock->expects($this->once())->method('isSingleStoreMode')->will($this->returnValue(false));
        $this->assertTrue($this->_model->isDisplayed());
    }

    public function testIsDisplayedReturnsFalseInSingleStoreMode()
    {
        $this->_appMock->expects($this->once())->method('isSingleStoreMode')->will($this->returnValue(true));
        $this->assertFalse($this->_model->isDisplayed());
    }
}
