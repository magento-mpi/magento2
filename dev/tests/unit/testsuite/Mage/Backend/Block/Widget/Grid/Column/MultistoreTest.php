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
        $this->_appMock = $this->getMock('Mage_Core_Model_App', array(), array(), '', false);

        $arguments = array(
            'app' => $this->_appMock,
        );

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getBlock('Mage_Backend_Block_Widget_Grid_Column_Multistore', $arguments);
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_appMock);
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
