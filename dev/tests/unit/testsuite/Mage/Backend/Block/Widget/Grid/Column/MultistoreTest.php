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

class Mage_Backend_Block_Widget_Grid_Column_MultistoreTest extends Magento_Test_TestCase_ObjectManagerAbstract
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
            'application' => $this->_appMock,
            'urlBuilder' => $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false)
        );
        $arguments = $this->_getConstructArguments(self::BLOCK_ENTITY,
            'Mage_Backend_Block_Widget_Grid_Column_Multistore', $arguments
        );
        $this->_model = $this->_getInstanceViaConstructor('Mage_Backend_Block_Widget_Grid_Column_Multistore',
            $arguments
        );
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
