<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test front name prefix
     */
    const TEST_FRONT_NAME = 'test_front_name';

    /**
     * @var Mage_DesignEditor_Helper_Data
     */
    protected $_model;

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testGetFrontName()
    {
        $frontNameNode = new Mage_Core_Model_Config_Element('<test>' . self::TEST_FRONT_NAME . '</test>');

        $configurationMock = $this->getMock('Mage_Core_Model_Config', array('getNode'), array(), '', false);
        $configurationMock->expects($this->once())
            ->method('getNode')
            ->with(Mage_DesignEditor_Helper_Data::XML_PATH_FRONT_NAME)
            ->will($this->returnValue($frontNameNode));

        $this->_model = new Mage_DesignEditor_Helper_Data($configurationMock);
        $this->assertEquals(self::TEST_FRONT_NAME, $this->_model->getFrontName());
    }
}
