<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Cache\Type;

class FrontendPoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Cache\Type\FrontendPool
     */
    protected $_model;

    /**
     * @var \Magento\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var \Magento\App\Arguments|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_arguments;

    /**
     * @var \Magento\App\Cache\Frontend\Pool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cachePool;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\ObjectManager', array(), array(), '', false);
        $this->_arguments = $this->getMock('Magento\App\Arguments', array(), array(), '', false);
        $this->_cachePool = $this->getMock('Magento\App\Cache\Frontend\Pool', array(), array(), '', false);
        $this->_model = new FrontendPool(
            $this->_objectManager,
            $this->_arguments,
            $this->_cachePool,
            array('fixture_cache_type' => 'fixture_frontend_id')
        );
    }

    /**
     * @param string|null $fixtureFrontendId
     * @param string $inputCacheType
     * @param string $expectedFrontendId
     *
     * @dataProvider getDataProvider
     */
    public function testGet($fixtureFrontendId, $inputCacheType, $expectedFrontendId)
    {
        $this->_arguments->expects(
            $this->once()
        )->method(
            'getCacheTypeFrontendId'
        )->with(
            $inputCacheType
        )->will(
            $this->returnValue($fixtureFrontendId)
        );

        $cacheFrontend = $this->getMock('Magento\Cache\FrontendInterface');
        $this->_cachePool->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            $expectedFrontendId
        )->will(
            $this->returnValue($cacheFrontend)
        );

        $accessProxy = $this->getMock('Magento\App\Cache\Type\AccessProxy', array(), array(), '', false);
        $this->_objectManager->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            'Magento\App\Cache\Type\AccessProxy',
            $this->identicalTo(array('frontend' => $cacheFrontend, 'identifier' => $inputCacheType))
        )->will(
            $this->returnValue($accessProxy)
        );

        $this->assertSame($accessProxy, $this->_model->get($inputCacheType));
        // Result has to be cached in memory
        $this->assertSame($accessProxy, $this->_model->get($inputCacheType));
    }

    public function getDataProvider()
    {
        return array(
            'retrieval from config' => array('configured_frontend_id', 'fixture_cache_type', 'configured_frontend_id'),
            'retrieval from map' => array(null, 'fixture_cache_type', 'fixture_frontend_id'),
            'fallback to default id' => array(
                null,
                'unknown_cache_type',
                \Magento\App\Cache\Frontend\Pool::DEFAULT_FRONTEND_ID
            )
        );
    }
}
