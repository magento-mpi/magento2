<?php
/**
 * Test class for Enterprise_PageCache_Model_Metadata
 *
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_PageCache_Model_MetadataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fpcCacheMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestIdtfMock;

    /**
     * @var Enterprise_PageCache_Model_Metadata
     */
    protected $_model;

    /**
     * Test meta data
     * @var array
     */
    protected $_data = array(
        'some' => 'data',
    );


    protected function setUp()
    {
        $this->_fpcCacheMock = $this->getMock('Enterprise_PageCache_Model_Cache', array(), array(), '', false);
        $this->_requestIdtfMock = $this->getMock('Enterprise_PageCache_Model_Request_Identifier',
            array(), array(), '', false
        );

        $this->_requestIdtfMock->expects($this->atLeastOnce())
            ->method('getRequestCacheId')
            ->will($this->returnValue('test_id'));

        $cache = serialize($this->_data);
        $this->_fpcCacheMock->expects($this->once())
            ->method('load')
            ->with('test_id' . Enterprise_PageCache_Model_MetadataInterface::METADATA_CACHE_SUFFIX)
            ->will($this->returnValue($cache));

        $this->_model = new Enterprise_PageCache_Model_Metadata($this->_fpcCacheMock, $this->_requestIdtfMock);
    }

    public function testGetSetMetadata()
    {
        $this->assertEquals('data', $this->_model->getMetadata('some'));
        $this->assertNull($this->_model->getMetadata('not-existing-key'));

        $this->_model->setMetadata('not-existing-key', 'new-data');
        $this->assertEquals('new-data', $this->_model->getMetadata('not-existing-key'));
    }

    public function testSaveMetadata()
    {
        $tags = array('some_tag');
        $cacheId = 'test_id' . Enterprise_PageCache_Model_MetadataInterface::METADATA_CACHE_SUFFIX;
        $this->_fpcCacheMock->expects($this->once())
            ->method('save')
            ->with(serialize($this->_data), $cacheId, $tags);

        $this->_model->saveMetadata($tags);
    }
}
