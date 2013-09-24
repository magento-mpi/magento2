<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Acl\Resource;

class ProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Webapi\Model\Acl\Resource\Provider
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected  $_configReaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_treeBuilderMock;

    protected function setUp()
    {
        $this->_configReaderMock = $this->getMock(
            'Magento\Webapi\Model\Acl\Resource\Config\Reader\Filesystem', array(), array(), '', false
        );
        $this->_configScopeMock = $this->getMock('Magento\Config\ScopeInterface');
        $this->_treeBuilderMock =
            $this->getMock('Magento\Acl\Resource\TreeBuilder', array(), array(), '', false);
        $this->_model = new \Magento\Webapi\Model\Acl\Resource\Provider(
            $this->_configReaderMock,
            $this->_configScopeMock,
            $this->_treeBuilderMock
        );
    }

    public function testGetAclVirtualResources()
    {
        $aclResourceConfig['config']['mapping'] = array('ExpectedValue');
        $scope = 'scopeName';
        $this->_configScopeMock->expects($this->once())->method('getCurrentScope')->will($this->returnValue($scope));
        $this->_configReaderMock->expects($this->once())
            ->method('read')->with($scope)->will($this->returnValue($aclResourceConfig));
        $this->assertEquals($aclResourceConfig['config']['mapping'], $this->_model->getAclVirtualResources());
    }
}
