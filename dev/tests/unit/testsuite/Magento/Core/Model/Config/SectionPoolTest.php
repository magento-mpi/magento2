<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config;

class SectionPoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config\SectionPool
     */
    protected $_model;

    /**
     * @var \Magento\Core\Model\Config\Section\ReaderPool
     */
    protected $_readerPoolMock;

    /**
     * @var \Magento\Core\Model\Config\DataFactory
     */
    protected $_dataFactoryMock;

    /**
     * @var \Magento\Cache\FrontendInterface
     */
    protected $_cacheMock;

    /**
     * @var string
     */
    protected $_cacheKey;

    /**
     * @var string
     */
    protected $_scopeType;

    /**
     * @var string
     */
    protected $_scopeCode;

    /**
     * @var string
     */
    protected $_configData;

    protected function setUp()
    {
        $this->_readerPoolMock = $this->getMock(
            'Magento\Core\Model\Config\Section\ReaderPool', array(), array(), '', false
        );
        $this->_dataFactoryMock = $this->getMock(
            'Magento\Core\Model\Config\DataFactory', array('create'), array(), '', false
        );
        $this->_cacheMock = $this->getMock('Magento\Cache\FrontendInterface');
        $this->_cacheKey = 'customCacheId';

        $this->_scopeType = 'scopeType';
        $this->_scopeCode = 'scopeCode';
        $this->_configData = array('key' => 'value');

        $this->_model = new \Magento\Core\Model\Config\SectionPool(
            $this->_readerPoolMock,
            $this->_dataFactoryMock,
            $this->_cacheMock,
            $this->_cacheKey
        );
    }

    /**
     * @covers \Magento\Core\Model\Config\SectionPool::getScope
     */
    public function testGetSectionCached()
    {
        $this->_cacheMock->expects($this->once())
            ->method('load')
            ->with($this->_cacheKey . '|' . $this->_scopeType . '|' . $this->_scopeCode)
            ->will($this->returnValue(serialize($this->_configData)));

        $this->_dataFactoryMock->expects($this->once())
            ->method('create')
            ->with(array('data' => $this->_configData))
            ->will($this->returnValue(new \Magento\Core\Model\Config\TestConfigClass()));

        $this->assertInstanceOf('Magento\Core\Model\Config\TestConfigClass',
            $this->_model->getScope($this->_scopeType, $this->_scopeCode)
        );
    }

    /**
     * @covers \Magento\Core\Model\Config\SectionPool::getScope
     */
    public function testGetSectionNotCachedCertainScope()
    {
        $this->_cacheMock->expects($this->once())
            ->method('load')
            ->with($this->_cacheKey . '|' . $this->_scopeType . '|' . $this->_scopeCode)
            ->will($this->returnValue(false));

        $readerMock = $this->getMock('Magento\Core\Model\Config\TestReaderClass');

        $readerMock->expects($this->once())
            ->method('read')
            ->with($this->_scopeCode)
            ->will($this->returnValue($this->_configData));

        $this->_readerPoolMock->expects($this->once())
            ->method('getReader')
            ->with($this->_scopeType)
            ->will($this->returnValue($readerMock));

        $this->_cacheMock->expects($this->once())
            ->method('save')
            ->with(
                serialize($this->_configData),
                $this->_cacheKey . '|' . $this->_scopeType . '|' . $this->_scopeCode,
                array(\Magento\Core\Model\Config\SectionPool::CACHE_TAG));

        $this->_dataFactoryMock->expects($this->once())
            ->method('create')
            ->with(array('data' => $this->_configData))
            ->will($this->returnValue(new \Magento\Core\Model\Config\TestConfigClass()));

        $this->assertInstanceOf(
            'Magento\Core\Model\Config\TestConfigClass',
            $this->_model->getScope($this->_scopeType, $this->_scopeCode)
        );
    }

    /**
     * @covers \Magento\Core\Model\Config\SectionPool::getScope
     */
    public function testGetSectionNotCachedDefaultScope()
    {
        $this->_cacheMock->expects($this->once())
            ->method('load')
            ->with($this->_cacheKey . '|' . 'default' . '|' . $this->_scopeCode)
            ->will($this->returnValue(false));

        $readerMock = $this->getMock('Magento\Core\Model\Config\TestReaderClass');

        $readerMock->expects($this->once())
            ->method('read')
            ->with('primary')
            ->will($this->returnValue($this->_configData));

        $this->_readerPoolMock->expects($this->once())
            ->method('getReader')
            ->with('default')
            ->will($this->returnValue($readerMock));

        $this->_cacheMock->expects($this->once())
            ->method('save')
            ->with(
                serialize($this->_configData),
                $this->_cacheKey . '|' . 'default' . '|' . $this->_scopeCode,
                array(\Magento\Core\Model\Config\SectionPool::CACHE_TAG));

        $this->_dataFactoryMock->expects($this->once())
            ->method('create')
            ->with(array('data' => $this->_configData))
            ->will($this->returnValue(new \Magento\Core\Model\Config\TestConfigClass()));

        $this->assertInstanceOf(
            'Magento\Core\Model\Config\TestConfigClass',
            $this->_model->getScope('default', $this->_scopeCode)
        );
    }

    /**
     * @covers \Magento\Core\Model\Config\SectionPool::getScope
     */
    public function testGetSectionMemoryCache()
    {
        $this->_cacheMock->expects($this->once())
            ->method('load')
            ->with($this->_cacheKey . '|' . $this->_scopeType . '|' . $this->_scopeCode)
            ->will($this->returnValue(serialize($this->_configData)));

        $this->_dataFactoryMock->expects($this->once())
            ->method('create')
            ->with(array('data' => $this->_configData))
            ->will($this->returnValue(new \Magento\Core\Model\Config\TestConfigClass()));

        $this->_model->getScope($this->_scopeType, $this->_scopeCode);
        $this->_model->getScope($this->_scopeType, $this->_scopeCode);
    }

    /**
     * @covers \Magento\Core\Model\Config\SectionPool::clean
     */
    public function testClean()
    {
        $this->_cacheMock->expects($this->once())
            ->method('clean')
            ->with(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(\Magento\Core\Model\Config\SectionPool::CACHE_TAG));

        $this->_model->clean();
    }
}
