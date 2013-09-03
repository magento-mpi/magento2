<?php
/**
 * Test class for Magento_FullPageCache_Model_Processor
 *
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_FullPageCache_Model_ProcessorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_FullPageCache_Model_Processor
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_restrictionMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fpcCacheMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_subProcFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_plcFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cntrFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_environmentMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestIdtfMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_designInfoMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_metadataMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeIdentifier;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManager;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheTypeList;

    protected function setUp()
    {
        $this->_restrictionMock = $this->getMock('Magento_FullPageCache_Model_Processor_RestrictionInterface',
            array(), array(), '', false
        );
        $this->_fpcCacheMock = $this->getMock('Magento_FullPageCache_Model_Cache', array(), array(), '', false);

        $this->_subProcFactoryMock = $this->getMock('Magento_FullPageCache_Model_Cache_SubProcessorFactory',
            array(), array(), '', false
        );
        $this->_plcFactoryMock = $this->getMock('Magento_FullPageCache_Model_Container_PlaceholderFactory',
            array(), array(), '', false
        );
        $this->_cntrFactoryMock = $this->getMock('Magento_FullPageCache_Model_ContainerFactory',
            array(), array(), '', false
        );
        $this->_environmentMock = $this->getMock('Magento_FullPageCache_Model_Environment',
            array(), array(), '', false
        );
        $this->_requestIdtfMock = $this->getMock('Magento_FullPageCache_Model_Request_Identifier',
            array(), array(), '', false
        );
        $this->_designInfoMock = $this->getMock('Magento_FullPageCache_Model_DesignPackage_Info',
            array(), array(), '', false
        );
        $this->_metadataMock = $this->getMock('Magento_FullPageCache_Model_Metadata', array(), array(), '', false);
        $this->_storeIdentifier = $this->getMock('Magento_FullPageCache_Model_Store_Identifier', array(),
            array(), '', false
        );
        $this->_storeManager = $this->getMock('Magento_Core_Model_StoreManagerInterface');
        $this->_cacheTypeList = $this->getMock('Magento_Core_Model_Cache_TypeListInterface');

        $this->_model = new  Magento_FullPageCache_Model_Processor(
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
            $this->_cacheTypeList
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
            ->with(Magento_Core_Model_Store::COOKIE_NAME)
            ->will($this->returnValue(false));
        $expected = 'recently_viewed_count';

        $this->assertEquals($expected, $this->_model->getRecentlyViewedCountCacheId());
    }

    public function testGetRecentlyViewedCountCacheIdWithCookie()
    {
        $this->_environmentMock->expects($this->once())
            ->method('hasCookie')
            ->with(Magento_Core_Model_Store::COOKIE_NAME)
            ->will($this->returnValue(true));

        $this->_environmentMock->expects($this->once())
            ->method('getCookie')
            ->with(Magento_Core_Model_Store::COOKIE_NAME)
            ->will($this->returnValue('100'));

        $expected = 'recently_viewed_count_100';

        $this->assertEquals($expected, $this->_model->getRecentlyViewedCountCacheId());
    }

    public function testGetSessionInfoCacheIdWithoutCookie()
    {
        $this->_environmentMock->expects($this->once())
            ->method('hasCookie')
            ->with(Magento_Core_Model_Store::COOKIE_NAME)
            ->will($this->returnValue(false));
        $expected = 'full_page_cache_session_info';

        $this->assertEquals($expected, $this->_model->getSessionInfoCacheId());
    }

    public function testGetSessionInfoCacheIdWithCookie()
    {
        $this->_environmentMock->expects($this->once())
            ->method('hasCookie')
            ->with(Magento_Core_Model_Store::COOKIE_NAME)
            ->will($this->returnValue(true));

        $this->_environmentMock->expects($this->once())
            ->method('getCookie')
            ->with(Magento_Core_Model_Store::COOKIE_NAME)
            ->will($this->returnValue('100'));

        $expected = 'full_page_cache_session_info_100';

        $this->assertEquals($expected, $this->_model->getSessionInfoCacheId());
    }

    public function testAddGetRequestTag()
    {
        $tags = array(Magento_FullPageCache_Model_Processor::CACHE_TAG);
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
        $subProcessor = $this->getMock('Magento_FullPageCache_Model_Cache_SubProcessorInterface');
        $this->_model->setSubprocessor($subProcessor);
        $this->assertEquals($subProcessor, $this->_model->getSubprocessor());
    }

}
