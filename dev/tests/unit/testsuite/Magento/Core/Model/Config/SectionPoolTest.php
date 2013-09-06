<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_SectionPoolTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config_SectionPool
     */
    protected $_model;

    /**
     * @var Magento_Core_Model_Config_Section_ReaderPool
     */
    protected $_readerPoolMock;

    /**
     * @var Magento_Core_Model_Config_DataFactory
     */
    protected $_dataFactoryMock;

    /**
     * @var Magento_Cache_FrontendInterface
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
            'Magento_Core_Model_Config_Section_ReaderPool', array(), array(), '', false
        );
        $this->_dataFactoryMock = $this->getMock(
            'Magento_Core_Model_Config_DataFactory', array('create'), array(), '', false
        );
        $this->_cacheMock = $this->getMock('Magento_Cache_FrontendInterface');
        $this->_cacheKey = 'customCacheId';

        $this->_scopeType = 'scopeType';
        $this->_scopeCode = 'scopeCode';
        $this->_configData = array('key' => 'value');

        $this->_model = new Magento_Core_Model_Config_SectionPool(
            $this->_readerPoolMock,
            $this->_dataFactoryMock,
            $this->_cacheMock,
            $this->_cacheKey
        );
    }

    /**
     * @covers Magento_Core_Model_Config_SectionPool::getSection
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
            ->will($this->returnValue(new TestConfigClass()));

        $this->assertInstanceOf('TestConfigClass', $this->_model->getSection($this->_scopeType, $this->_scopeCode));
    }

    /**
     * @covers Magento_Core_Model_Config_SectionPool::getSection
     */
    public function testGetSectionNotCachedCertainScope()
    {
        $this->_cacheMock->expects($this->once())
            ->method('load')
            ->with($this->_cacheKey . '|' . $this->_scopeType . '|' . $this->_scopeCode)
            ->will($this->returnValue(false));

        $readerMock = $this->getMock('TestReaderClass');

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
                array(Magento_Core_Model_Config_SectionPool::CACHE_TAG));

        $this->_dataFactoryMock->expects($this->once())
            ->method('create')
            ->with(array('data' => $this->_configData))
            ->will($this->returnValue(new TestConfigClass()));

        $this->assertInstanceOf(
            'TestConfigClass',
            $this->_model->getSection($this->_scopeType, $this->_scopeCode)
        );
    }

    /**
     * @covers Magento_Core_Model_Config_SectionPool::getSection
     */
    public function testGetSectionNotCachedDefaultScope()
    {
        $this->_cacheMock->expects($this->once())
            ->method('load')
            ->with($this->_cacheKey . '|' . 'default' . '|' . $this->_scopeCode)
            ->will($this->returnValue(false));

        $readerMock = $this->getMock('TestReaderClass');

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
                array(Magento_Core_Model_Config_SectionPool::CACHE_TAG));

        $this->_dataFactoryMock->expects($this->once())
            ->method('create')
            ->with(array('data' => $this->_configData))
            ->will($this->returnValue(new TestConfigClass()));

        $this->assertInstanceOf(
            'TestConfigClass',
            $this->_model->getSection('default', $this->_scopeCode)
        );
    }

    /**
     * @covers Magento_Core_Model_Config_SectionPool::getSection
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
            ->will($this->returnValue(new TestConfigClass()));

        $this->_model->getSection($this->_scopeType, $this->_scopeCode);
        $this->_model->getSection($this->_scopeType, $this->_scopeCode);
    }

    /**
     * @covers Magento_Core_Model_Config_SectionPool::clean
     */
    public function testClean()
    {
        $this->_cacheMock->expects($this->once())
            ->method('clean')
            ->with(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(Magento_Core_Model_Config_SectionPool::CACHE_TAG));

        $this->_model->clean();
    }
}

class TestConfigClass
{
}

class TestReaderClass
{
    public function read($scope = 'primary')
    {
        return $scope;
    }
}