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

class Mage_Backend_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_configMock;

    public function setUp()
    {
        $this->_configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_helper = new Mage_Backend_Helper_Data(array('config' => $this->_configMock));
    }

    public function testGetAreaFrontName()
    {
        $this->_configMock->expects($this->once())->method('getAreas')
            ->will($this->returnValue(array(
                'adminhtml' => array(
                    'frontName' => 'area_front_name'
                )
            )
        ));

        $this->_helper->getAreaFrontName();
        $this->_helper->getAreaFrontName();
    }

    public function testGetAreaFrontNameIfAreaIsNotExist()
    {
        $this->_configMock->expects($this->once())->method('getAreas')
            ->will($this->returnValue(array(
                'another_one_area' => array(
                    'frontName' => 'area_front_name'
                )
            )
        ));


        $this->_helper->getAreaFrontName();

        $this->assertEmpty($this->_helper->getAreaFrontName());
    }
}
