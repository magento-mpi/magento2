<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Acl_Resource_ProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Webapi_Model_Acl_Resource_Provider
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
        $this->_configReaderMock = $this->getMock(
            'Magento_Webapi_Model_Acl_Resource_Config_Reader_Filesystem', array(), array(), '', false
        );
        $this->_configScopeMock = $this->getMock('Magento_Config_ScopeInterface');
        $this->_treeBuilderMock =
            $this->getMock('Magento_Acl_Resource_TreeBuilder', array(), array(), '', false);
        $this->_model = new Magento_Webapi_Model_Acl_Resource_Provider(
            $this->_configReaderMock,
            $this->_configScopeMock,
            $this->_treeBuilderMock
        );
    }

    public function testGetAclVirtualResources()
    {
        $aclResourceConfig['config']['mapping'] = array('ExpectedValue');
        $this->_configReaderMock->expects($this->once())
            ->method('read')->with(null)->will($this->returnValue($aclResourceConfig));
        $this->assertEquals($aclResourceConfig['config']['mapping'], $this->_model->getAclVirtualResources());
    }
}
