<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_LoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Model_Config_Loader
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configValueFactory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configCollection;

    protected function setUp()
    {
        $this->_configValueFactory = $this->getMock(
            'Magento_Core_Model_Config_ValueFactory', array('create', 'getCollection'), array(), '', false
        );
        $this->_model = new Magento_Backend_Model_Config_Loader($this->_configValueFactory);

        $this->_configCollection = $this->getMock(
            'Magento_Core_Model_Resource_Config_Data_Collection', array(), array(), '', false
        );
        $this->_configCollection->expects($this->once())->method('addScopeFilter')->with('scope', 'scopeId', 'section')
            ->will($this->returnSelf());

        $configDataMock = $this->getMock('Magento_Core_Model_Config_Value', array(), array(), '', false);
        $this->_configValueFactory->expects($this->once())->method('create')->will($this->returnValue($configDataMock));
        $configDataMock->expects($this->any())->method('getCollection')
            ->will($this->returnValue($this->_configCollection));

        $this->_configCollection->expects($this->once())->method('getItems')->will($this->returnValue(
            array(
                new \Magento\Object(array('path' => 'section', 'value' => 10, 'config_id' => 20))
            )
        ));
    }

    protected function tearDown()
    {
        unset($this->_configValueFactory);
        unset($this->_model);
        unset($this->_configCollection);
    }

    public function testGetConfigByPathInFullMode()
    {
        $expected = array('section' => array('path' => 'section', 'value' => 10, 'config_id' => 20));
        $this->assertEquals($expected, $this->_model->getConfigByPath('section', 'scope', 'scopeId', true));
    }

    public function testGetConfigByPath()
    {
        $expected = array('section' => 10);
        $this->assertEquals($expected, $this->_model->getConfigByPath('section', 'scope', 'scopeId', false));
    }
}

