<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\Url;

class ScopeResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_object;

    public function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_storeManagerMock = $this->getMockBuilder('Magento\Framework\StoreManagerInterface')->getMock();
        $this->_object = $objectManager->getObject(
            'Magento\Core\Model\Url\ScopeResolver',
            array('storeManager' => $this->_storeManagerMock)
        );
    }

    /**
     * @dataProvider getScopeDataProvider
     * @param int|null$scopeId
     */
    public function testGetScope($scopeId)
    {
        $scopeMock = $this->getMockBuilder('\Magento\Framework\Url\ScopeInterface')->getMock();
        $this->_storeManagerMock->expects(
            $this->at(0)
        )->method(
            'getStore'
        )->with(
            $scopeId
        )->will(
            $this->returnValue($scopeMock)
        );
        $this->_object->getScope($scopeId);
    }

    /**
     * @expectedException \Magento\Framework\Exception
     * @expectedExceptionMessage Invalid scope object
     */
    public function testGetScopeException()
    {
        $this->_object->getScope();
    }

    /**
     * @return array
     */
    public function getScopeDataProvider()
    {
        return array(array(null), array(1));
    }

    public function testGetScopes()
    {
        $this->_storeManagerMock->expects($this->once())->method('getStores');
        $this->_object->getScopes();
    }
}
