<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Acl_Resource_ProviderTest extends  PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Acl_Resource_Provider
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected  $_configReaderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_treeBuilderMock;

    protected function setUp()
    {
        $this->_configReaderMock = $this->getMock('Magento_Config_ReaderInterface');
        $this->_configScopeMock = $this->getMock('Magento_Config_ScopeInterface');
        $this->_treeBuilderMock =
            $this->getMock('Magento_Acl_Resource_TreeBuilder', array(), array(), '', false);
        $this->_model = new Magento_Acl_Resource_Provider(
            $this->_configReaderMock,
            $this->_configScopeMock,
            $this->_treeBuilderMock
        );
    }

    public function testGetIfAclResourcesExist()
    {
        $aclResourceConfig['config']['acl']['resources'] = array('ExpectedValue');
        $scope = 'scopeName';
        $this->_configScopeMock->expects($this->once())->method('getCurrentScope')->will($this->returnValue($scope));
        $this->_configReaderMock->expects($this->once())
            ->method('read')->with($scope)->will($this->returnValue($aclResourceConfig));
        $this->_treeBuilderMock->expects($this->once())
            ->method('build')->will($this->returnValue('ExpectedResult'));
        $this->assertEquals('ExpectedResult', $this->_model->getAclResources());
    }

    public function testGetIfAclResourcesEmpty()
    {
        $scope = 'scopeName';
        $this->_configScopeMock->expects($this->once())->method('getCurrentScope')->will($this->returnValue($scope));
        $this->_configReaderMock->expects($this->once())
            ->method('read')->with($scope)->will($this->returnValue(array()));
        $this->_treeBuilderMock->expects($this->never())->method('build');
        $this->assertEquals(array(), $this->_model->getAclResources());
    }
}