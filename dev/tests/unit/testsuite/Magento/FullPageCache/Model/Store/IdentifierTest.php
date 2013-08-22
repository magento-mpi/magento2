<?php
/**
 * Test class for Magento_FullPageCache_Model_Store_Identifier
 *
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_FullPageCache_Model_Store_IdentifierTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fpcCacheMock;

    /**
     * @var Magento_FullPageCache_Model_Store_Identifier
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_requestId = 'request_id';

    protected function setUp()
    {
        $this->_fpcCacheMock = $this->getMock('Magento_FullPageCache_Model_Cache', array(), array(), '', false);
        $this->_model = new Magento_FullPageCache_Model_Store_Identifier($this->_fpcCacheMock);
    }

    public function testGetStoreId()
    {
        $this->_fpcCacheMock->expects($this->once())
            ->method('load')
            ->with(Magento_FullPageCache_Model_Store_Identifier::CACHE_ID . '_' . $this->_requestId)
            ->will($this->returnValue('10'));
        $this->assertEquals(10, $this->_model->getStoreId($this->_requestId));
    }

    public function testSave()
    {
        $storeId = 10;
        $requestId = Magento_FullPageCache_Model_Store_Identifier::CACHE_ID . '_' . $this->_requestId;
        $tags = array('some_tags');
        $this->_fpcCacheMock->expects($this->once())
            ->method('save')
            ->with($storeId, $requestId, $tags)
            ->will($this->returnValue('10'));
        $this->_model->save($storeId, $this->_requestId, $tags);
    }
}
