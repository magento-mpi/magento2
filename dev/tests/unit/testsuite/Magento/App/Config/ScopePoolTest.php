<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\App\Config;

class ScopePoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Config\Scope\Reader|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_reader;

    /**
     * @var \Magento\App\Config\DataFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dataFactory;
    /**
     * @var \Magento\Cache\FrontendInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cache;

    /**
     * @var \Magento\App\Config\ScopePool
     */
    protected $_object;

    public function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_reader = $this->getMockBuilder('\Magento\App\Config\Scope\Reader')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_dataFactory = $this->getMockBuilder('\Magento\App\Config\DataFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_cache = $this->getMock('\Magento\Cache\FrontendInterface');
        $this->_object = $helper->getObject('\Magento\App\Config\ScopePool', array(
            'reader' => $this->_reader,
            'dataFactory' => $this->_dataFactory,
            'cache' => $this->_cache,
            'cacheId' => 'test_cache_id'
        ));
    }

    /**
     * @dataProvider getScopeByCodeDataProvider
     *
     * @param string $scope
     * @param array $data
     * @param string|null $cachedData
     */
    public function testGetScopeByCode($scope, array $data, $cachedData)
    {
        $cacheKey = "test_cache_id|$scope";

        $this->_cache->expects($this->once())
            ->method('load')
            ->with($cacheKey)
            ->will($this->returnValue($cachedData));

        if (!$cachedData) {
            $this->_reader->expects($this->once())
                ->method('read')
                ->with('testScope')
                ->will($this->returnValue($data));
            $this->_cache->expects($this->once())
                ->method('save')
                ->with(serialize($data), $cacheKey, array(\Magento\App\Config\ScopePool::CACHE_TAG));
        }

        $configData = $this->getMockBuilder('\Magento\App\Config\Data')->disableOriginalConstructor()->getMock();
        $this->_dataFactory->expects($this->once())
            ->method('create')
            ->with(array('data' => $data))
            ->will($this->returnValue($configData));
        $this->assertInstanceOf('\Magento\App\Config\Data', $this->_object->getScopeByCode($scope));

        // second call to check caching
        $this->assertInstanceOf('\Magento\App\Config\Data', $this->_object->getScopeByCode($scope));
    }

    public function getScopeByCodeDataProvider()
    {
        return array(
            array('testScope', array('key' => 'value'), null),
            array('testScope', array('key' => 'value'), serialize(array('key' => 'value'))),
        );
    }

    public function testClean()
    {
        $this->_cache->expects($this->once())
            ->method('clean')
            ->with(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(\Magento\App\Config\ScopePool::CACHE_TAG));
        $this->_object->clean('testScope');
    }
}
