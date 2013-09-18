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
    protected $_readerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    protected function setUp()
    {
        $this->_readerMock = $this->getMock('Magento_Index_Model_Indexer_Config_Reader', array(), array(), '', false);
        $this->_configScopeMock = $this->getMock('Magento_Config_ScopeInterface');
        $this->_cacheMock = $this->getMock('Magento_Config_CacheInterface');
        $this->_model = new Magento_Index_Model_Indexer_Config(
            $this->_readerMock,
            $this->_configScopeMock,
            $this->_cacheMock
        );
    }

    /**
     * @covers Magento_Index_Model_Indexer_Config::getIndexer
     */
    public function testGetIndexer()
    {
        $indexerConfig = array('indexerName' => 'indexerConfig');
        $this->_configScopeMock->expects($this->once())
            ->method('getCurrentScope')
            ->will($this->returnValue('global'));
        $this->_cacheMock->expects($this->once())
            ->method('load')->with('global::indexerConfigCache')
            ->will($this->returnValue(serialize($indexerConfig)));
        $this->assertEquals('indexerConfig', $this->_model->getIndexer('indexerName'));
    }

    /**
     * @covers Magento_Index_Model_Indexer_Config::getAll
     */
    public function testGetAll()
    {
        $indexerConfig = array('indexerName' => 'indexerConfig');
        $this->_configScopeMock->expects($this->once())->method('getCurrentScope')
            ->will($this->returnValue('global'));
        $this->_cacheMock->expects($this->once())->method('load')->with('global::indexerConfigCache')
            ->will($this->returnValue(serialize($indexerConfig)));
        $this->assertEquals($indexerConfig, $this->_model->getAll());
    }
}
