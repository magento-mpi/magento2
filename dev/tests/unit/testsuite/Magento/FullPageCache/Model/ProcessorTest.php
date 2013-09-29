<?php
/**
 * Test class for \Magento\FullPageCache\Model\Processor
 *
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\FullPageCache\Model\Processor
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_restrictionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fpcCacheMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_subProcFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_plcFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cntrFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_environmentMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestIdtfMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_designInfoMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_metadataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeIdentifier;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheTypeList;

    protected function setUp()
    {
        $this->_restrictionMock = $this->getMock('Magento\FullPageCache\Model\Processor\RestrictionInterface',
            array(), array(), '', false
        );
        $this->_fpcCacheMock = $this->getMock('Magento\FullPageCache\Model\Cache', array(), array(), '', false);

        $this->_subProcFactoryMock = $this->getMock('Magento\FullPageCache\Model\Cache\SubProcessorFactory',
            array(), array(), '', false
        );
        $this->_plcFactoryMock = $this->getMock('Magento\FullPageCache\Model\Container\PlaceholderFactory',
            array(), array(), '', false
        );
        $this->_cntrFactoryMock = $this->getMock('Magento\FullPageCache\Model\ContainerFactory',
            array(), array(), '', false
        );
        $this->_environmentMock = $this->getMock('Magento\FullPageCache\Model\Environment',
            array(), array(), '', false
        );
        $this->_requestIdtfMock = $this->getMock('Magento\FullPageCache\Model\Request\Identifier',
            array(), array(), '', false
        );
        $this->_designInfoMock = $this->getMock('Magento\FullPageCache\Model\DesignPackage\Info',
            array(), array(), '', false
        );
        $this->_metadataMock = $this->getMock('Magento\FullPageCache\Model\Metadata', array(), array(), '', false);
        $this->_storeIdentifier = $this->getMock('Magento\FullPageCache\Model\Store\Identifier', array(),
            array(), '', false
        );
        $this->_storeManager = $this->getMock('Magento\Core\Model\StoreManagerInterface');
        $this->_cacheTypeList = $this->getMock('Magento\Core\Model\Cache\TypeListInterface');

        $coreRegistry = $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false);
        $coreStoreConfig = $this->getMock('Magento\Core\Model\Store\Config', array(), array(), '', false);
        $coreConfig = $this->getMock('Magento\Core\Model\Config', array(), array(), '', false);

        $this->_model = new  \Magento\FullPageCache\Model\Processor(
            $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false),
            $this->_restrictionMock,
            $this->_fpcCacheMock,
            $this->_subProcFactoryMock,
            $this->_plcFactoryMock,
            $this->_cntrFactoryMock,
            $this->_environmentMock,
            $this->_requestIdtfMock,
            $this->_designInfoMock,
            $this->_metadataMock,
            $this->_storeIdentifier,
            $this->_storeManager,
            $coreRegistry,
            $this->_cacheTypeList,
            $coreStoreConfig,
            $coreConfig
        );
    }

    public function testGetRequestId()
    {
        $this->_requestIdtfMock->expects($this->once())
            ->method('getRequestId')->will($this->returnValue('test_id'));

        $this->assertEquals('test_id', $this->_model->getRequestId());
    }

    public function testGetRequestCacheId()
    {
        $this->_requestIdtfMock->expects($this->once())
            ->method('getRequestCacheId')->will($this->returnValue('test_cache_id'));

        $this->assertEquals('test_cache_id', $this->_model->getRequestCacheId());
    }

    public function testisAllowed()
    {
        $this->_requestIdtfMock->expects($this->once())
            ->method('getRequestId')->will($this->returnValue('test_id'));

        $this->_restrictionMock->expects($this->once())->method('isAllowed')
            ->with('test_id')->will($this->returnValue(true));


        $this->assertTrue($this->_model->isAllowed());
    }

    public function testGetRecentlyViewedCountCacheIdWithoutCookie()
    {
        $this->_environmentMock->expects($this->once())
            ->method('hasCookie')
            ->with(\Magento\Core\Model\Store::COOKIE_NAME)
            ->will($this->returnValue(false));
        $expected = 'recently_viewed_count';

        $this->assertEquals($expected, $this->_model->getRecentlyViewedCountCacheId());
    }

    public function testGetRecentlyViewedCountCacheIdWithCookie()
    {
        $this->_environmentMock->expects($this->once())
            ->method('hasCookie')
            ->with(\Magento\Core\Model\Store::COOKIE_NAME)
            ->will($this->returnValue(true));

        $this->_environmentMock->expects($this->once())
            ->method('getCookie')
            ->with(\Magento\Core\Model\Store::COOKIE_NAME)
            ->will($this->returnValue('100'));

        $expected = 'recently_viewed_count_100';

        $this->assertEquals($expected, $this->_model->getRecentlyViewedCountCacheId());
    }

    public function testGetSessionInfoCacheIdWithoutCookie()
    {
        $this->_environmentMock->expects($this->once())
            ->method('hasCookie')
            ->with(\Magento\Core\Model\Store::COOKIE_NAME)
            ->will($this->returnValue(false));
        $expected = 'full_page_cache_session_info';

        $this->assertEquals($expected, $this->_model->getSessionInfoCacheId());
    }

    public function testGetSessionInfoCacheIdWithCookie()
    {
        $this->_environmentMock->expects($this->once())
            ->method('hasCookie')
            ->with(\Magento\Core\Model\Store::COOKIE_NAME)
            ->will($this->returnValue(true));

        $this->_environmentMock->expects($this->once())
            ->method('getCookie')
            ->with(\Magento\Core\Model\Store::COOKIE_NAME)
            ->will($this->returnValue('100'));

        $expected = 'full_page_cache_session_info_100';

        $this->assertEquals($expected, $this->_model->getSessionInfoCacheId());
    }

    public function testAddGetRequestTag()
    {
        $tags = array(\Magento\FullPageCache\Model\Processor::CACHE_TAG);
        $this->assertEquals($tags, $this->_model->getRequestTags());

        $this->_model->addRequestTag('some_tag');
        $tags[] = 'some_tag';
        $this->assertEquals($tags, $this->_model->getRequestTags());
    }

    public function testSetMetadata()
    {
        $testKey = 'test_key';
        $testValue = 'test_value';
        $this->_metadataMock->expects($this->once())->method('setMetadata')->with($testKey, $testValue);

        $this->_model->setMetadata($testKey, $testValue);
    }

    public function testGetMetadata()
    {
        $testKey = 'test_key';
        $testValue = 'test_value';

        $this->_metadataMock->expects($this->once())
            ->method('getMetadata')->with($testKey)->will($this->returnValue($testValue));

        $this->assertEquals($testValue, $this->_model->getMetadata($testKey));
    }

    public function testGetSetSubprocessor()
    {
        $this->assertNull($this->_model->getSubprocessor());
        $subProcessor = $this->getMock('Magento\FullPageCache\Model\Cache\SubProcessorInterface');
        $this->_model->setSubprocessor($subProcessor);
        $this->assertEquals($subProcessor, $this->_model->getSubprocessor());
    }

}
