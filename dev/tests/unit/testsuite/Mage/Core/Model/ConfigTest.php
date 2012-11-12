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
     * @dataProvider constructorDataProvider
     * @param array|Varien_Simplexml_Element $data
     */
    public function testConstructor($data)
    {
        /** @var $objectManagerMock Magento_ObjectManager_Zend */
        $objectManagerMock = $this->getMock('Magento_ObjectManager_Zend', array('create', 'get'), array(), '', false);
        $objectManagerMock->expects($this->any())
            ->method('create')
            ->will($this->returnCallback(array($this, 'getInstance')));

        $this->_model = new Mage_Core_Model_Config($objectManagerMock, $data);
        $this->assertInstanceOf('Mage_Core_Model_Config_Options', $this->_model->getOptions());
    }

    public function constructorDataProvider()
    {
        return array(
            array('data' => null),
            array('data' => array()),
            array('data' => new Varien_Simplexml_Element('<body></body>')),
        );
    }

    /**
     * Callback to use instead Magento_ObjectManager_Zend::create
     *
     * @param string $className
     * @param array $params
     * @return string
     */
    public function getInstance($className, $params = array())
    {
        return new $className($params);
    }
}