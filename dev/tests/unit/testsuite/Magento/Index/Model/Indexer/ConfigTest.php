<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Model\Indexer;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Index\Model\Indexer\Config
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_readerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    protected function setUp()
    {
        $this->_readerMock = $this->getMock('Magento\Index\Model\Indexer\Config\Reader', array(), array(), '', false);
        $this->_configScopeMock = $this->getMock('Magento\Config\ScopeInterface');
        $this->_cacheMock = $this->getMock('Magento\Config\CacheInterface');
        $this->_model = new \Magento\Index\Model\Indexer\Config(
            $this->_readerMock,
            $this->_configScopeMock,
            $this->_cacheMock
        );
    }

    /**
     * @covers \Magento\Index\Model\Indexer\Config::getIndexer
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
     * @covers \Magento\Index\Model\Indexer\Config::getAll
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
