<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_model;

    /**
     * @param mixed $data
     * @param array $map
     * @dataProvider constructorDataProvider
     */
    public function testConstructor($data, $map)
    {
        //TODO: We should not use mocks in integration tests
        /** @var Magento_ObjectManager_Zend|PHPUnit_Framework_MockObject_MockObject $objectManagerMock */
        $objectManagerMock = $this->getMock('Magento_ObjectManager_Zend', array('create', 'get'), array(), '', false);
        $objectManagerMock->expects($this->any())
            ->method('create')
            ->will($this->returnValueMap(array(
                $map,
                array('Mage_Core_Model_Config_Base', array(), true,  new Mage_Core_Model_Config_Base())
            )));

        $this->_model = new Mage_Core_Model_Config($objectManagerMock, $data);
    }

    /**
     * @return array
     */
    public function constructorDataProvider()
    {
        $simpleXml = new Varien_Simplexml_Element('<body></body>');
        return array(
            array(
                'data' => null,
                'map' => array('Mage_Core_Model_Config_Options', array('data' => array(null)), true,
                    new Mage_Core_Model_Config_Options())
            ),
            array(
                'data' => array(),
                'map' => array('Mage_Core_Model_Config_Options', array('data' => array()), true,
                    new Mage_Core_Model_Config_Options())
            ),
            array('data' => $simpleXml,
                'map' => array('Mage_Core_Model_Config_Options', array('data' => array($simpleXml)), true,
                    new Mage_Core_Model_Config_Options())),
        );
    }
}
