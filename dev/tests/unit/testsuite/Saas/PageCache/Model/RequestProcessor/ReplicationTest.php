<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PageCache_Model_RequestProcessor_ReplicationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_PageCache_Model_RequestProcessor_Maintenance|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_metadataMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fpcCacheMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheHelperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Zend_Controller_Request_Http', array(), array(), '', false);
        $this->_responseMock = $this->getMock('Zend_Controller_Response_Http', array(), array(), '', false);
        $this->_fpcCacheMock = $this->getMock('Saas_Saas_Model_Cache', array(), array(), '', false);
        $this->_metadataMock = $this->getMock('Enterprise_PageCache_Model_Metadata', array(), array(), '', false);
        $this->_cacheHelperMock = $this->getMock('Saas_Search_Helper_Cache', array(), array(), '', false);

        $this->_model = $this->getMock('Saas_PageCache_Model_RequestProcessor_Replication',
            array('_isReplicationCompleted'),
            array($this->_fpcCacheMock, $this->_metadataMock, $this->_cacheHelperMock)
        );
    }

    public function testExtractContentWhenReplicationIsCompletedAndWithCategoryMetadata()
    {
        $content = 'test_content';
        $this->_model->expects($this->once())->method('_isReplicationCompleted')->will($this->returnValue(true));

        $this->_metadataMock->expects($this->once())->method('getMetadata')
            ->with(Enterprise_PageCache_Model_Processor_Category::METADATA_CATEGORY_ID)
            ->will($this->returnValue('some-data'));

        $this->_fpcCacheMock->expects($this->once())
            ->method('invalidateType')->with(Enterprise_PageCache_Model_Cache_Type::TYPE_IDENTIFIER);

        $this->assertEquals(
            $content,
            $this->_model->extractContent($this->_requestMock, $this->_responseMock, $content)
        );
    }

    public function testExtractContentWhenReplicationIsCompletedAndWithoutCategoryMetadata()
    {
        $content = 'test_content';
        $this->_model->expects($this->never())->method('_isReplicationCompleted');

        $this->_metadataMock->expects($this->once())->method('getMetadata')
            ->with(Enterprise_PageCache_Model_Processor_Category::METADATA_CATEGORY_ID)
            ->will($this->returnValue(false));

        $this->_fpcCacheMock->expects($this->never())
            ->method('invalidateType');

        $this->assertEquals(
            $content,
            $this->_model->extractContent($this->_requestMock, $this->_responseMock, $content)
        );
    }

    public function testExtractContentWhenReplicationIsNotCompleted()
    {
        $content = 'test_content';

        $this->_metadataMock->expects($this->once())->method('getMetadata')
            ->with(Enterprise_PageCache_Model_Processor_Category::METADATA_CATEGORY_ID)
            ->will($this->returnValue(true));

        $this->_model->expects($this->once())->method('_isReplicationCompleted')->will($this->returnValue(false));

        $this->_fpcCacheMock->expects($this->never())
            ->method('invalidateType');

        $this->assertEquals(
            $content,
            $this->_model->extractContent($this->_requestMock, $this->_responseMock, $content)
        );
    }
}
