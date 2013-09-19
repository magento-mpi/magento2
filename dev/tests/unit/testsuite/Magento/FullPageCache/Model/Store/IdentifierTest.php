<?php
/**
 * Test class for \Magento\FullPageCache\Model\Store\Identifier
 *
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model\Store;

class IdentifierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fpcCacheMock;

    /**
     * @var \Magento\FullPageCache\Model\Store\Identifier
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_requestId = 'request_id';

    protected function setUp()
    {
        $this->_fpcCacheMock = $this->getMock('Magento\FullPageCache\Model\Cache', array(), array(), '', false);
        $this->_model = new \Magento\FullPageCache\Model\Store\Identifier($this->_fpcCacheMock);
    }

    public function testGetStoreId()
    {
        $this->_fpcCacheMock->expects($this->once())
            ->method('load')
            ->with(\Magento\FullPageCache\Model\Store\Identifier::CACHE_ID . '_' . $this->_requestId)
            ->will($this->returnValue('10'));
        $this->assertEquals(10, $this->_model->getStoreId($this->_requestId));
    }

    public function testSave()
    {
        $storeId = 10;
        $requestId = \Magento\FullPageCache\Model\Store\Identifier::CACHE_ID . '_' . $this->_requestId;
        $tags = array('some_tags');
        $this->_fpcCacheMock->expects($this->once())
            ->method('save')
            ->with($storeId, $requestId, $tags)
            ->will($this->returnValue('10'));
        $this->_model->save($storeId, $this->_requestId, $tags);
    }
}
