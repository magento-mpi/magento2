<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Acl\Resource;

class ProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Acl\Resource\Provider
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configReaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_treeBuilderMock;

    protected function setUp()
    {
        $this->_configReaderMock = $this->getMock('Magento\Framework\Config\ReaderInterface');
        $this->_treeBuilderMock = $this->getMock('Magento\Framework\Acl\Resource\TreeBuilder', array(), array(), '', false);
        $this->_model = new \Magento\Framework\Acl\Resource\Provider($this->_configReaderMock, $this->_treeBuilderMock);
    }

    public function testGetIfAclResourcesExist()
    {
        $aclResourceConfig['config']['acl']['resources'] = array('ExpectedValue');
        $this->_configReaderMock->expects($this->once())->method('read')->will($this->returnValue($aclResourceConfig));
        $this->_treeBuilderMock->expects($this->once())->method('build')->will($this->returnValue('ExpectedResult'));
        $this->assertEquals('ExpectedResult', $this->_model->getAclResources());
    }

    public function testGetIfAclResourcesEmpty()
    {
        $this->_configReaderMock->expects($this->once())->method('read')->will($this->returnValue(array()));
        $this->_treeBuilderMock->expects($this->never())->method('build');
        $this->assertEquals(array(), $this->_model->getAclResources());
    }
}
