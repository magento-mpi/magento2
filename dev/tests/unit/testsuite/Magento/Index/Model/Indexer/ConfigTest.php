<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Index_Model_Indexer_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Index_Model_Indexer_Config
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configDataMock;

    protected function setUp()
    {
        $this->_configDataMock = $this->getMock('Magento_Index_Model_Indexer_Config_Data', array(), array(), '', false);
        $this->_model = new Magento_Index_Model_Indexer_Config($this->_configDataMock);
    }

    /**
     * @covers Magento_Index_Model_Indexer_Config::getIndexer
     */
    public function testGetIndexer()
    {
        $indexerName = 'indexer';
        $indexerConfig = array('indexerName' => 'indexerConfig');
        $this->_configDataMock->expects($this->once())
            ->method('get')
            ->with($indexerName, array())
            ->will($this->returnValue($indexerConfig));
        $this->assertEquals($indexerConfig, $this->_model->getIndexer($indexerName));
    }

    /**
     * @covers Magento_Index_Model_Indexer_Config::getAll
     */
    public function testGetAll()
    {
        $indexerConfig = array('indexerName' => 'indexerConfig', 'anotherIndexer' => 'anotherConfig');
        $this->_configDataMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($indexerConfig));
        $this->assertEquals($indexerConfig, $this->_model->getAll());
    }
}
