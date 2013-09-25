<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CatalogSearch_Model_Resource_EngineProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var $_model Magento_CatalogSearch_Model_Resource_EngineProvider
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_CatalogSearch_Model_Resource_EngineFactory
     */
    protected $_engineFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Model_Store_Config
     */
    protected $_storeConfigMock;

    protected function setUp()
    {
        $this->_engineFactoryMock = $this->getMock('Magento_CatalogSearch_Model_Resource_EngineFactory',
            array('create'), array(), '', false);
        $this->_storeConfigMock = $this->getMock('Magento_Core_Model_Store_Config',
            array('getConfig'), array(), '', false);

        $this->_model = new Magento_CatalogSearch_Model_Resource_EngineProvider($this->_engineFactoryMock,
            $this->_storeConfigMock);
    }

    public function testGetPositive()
    {
        $engineMock = $this->getMock('Magento_CatalogSearch_Model_Resource_Fulltext_Engine',
            array('test'), array(), '', false);
        $engineMock->expects($this->once())
            ->method('test')
            ->will($this->returnValue(true));

        $this->_storeConfigMock->expects($this->once())
            ->method('getConfig')
            ->with('catalog/search/engine')
            ->will($this->returnValue('Magento_CatalogSearch_Model_Resource_Fulltext_Engine'));

        $this->_engineFactoryMock->expects($this->once())
            ->method('create')
            ->with('Magento_CatalogSearch_Model_Resource_Fulltext_Engine')
            ->will($this->returnValue($engineMock));

        $this->assertEquals($engineMock, $this->_model->get());
    }

    public function testGetNegative()
    {
        $engineMock = $this->getMock('Magento_CatalogSearch_Model_Resource_Fulltext_Engine',
            array('test'), array(), '', false);
        $engineMock->expects($this->never())
            ->method('test');

        $this->_storeConfigMock->expects($this->once())
            ->method('getConfig')
            ->with('catalog/search/engine')
            ->will($this->returnValue(''));

        $this->_engineFactoryMock->expects($this->once())
            ->method('create')
            ->with('Magento_CatalogSearch_Model_Resource_Fulltext_Engine')
            ->will($this->returnValue($engineMock));

        $this->assertEquals($engineMock, $this->_model->get());
    }
}
