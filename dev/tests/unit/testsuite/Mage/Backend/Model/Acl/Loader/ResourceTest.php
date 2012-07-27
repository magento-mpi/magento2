<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Acl_Loader_ResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Acl_Loader_Resource
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectFactoryMock;

    public function setUp()
    {
        $this->_configMock = $this->getMock('Mage_Backend_Model_Acl_Config', array('getAclResources'));
        $this->_objectFactoryMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_model = new Mage_Backend_Model_Acl_Loader_Resource(array(
            'config' => $this->_configMock,
            'objectFactory' => $this->_objectFactoryMock
        ));
    }

    public function testPopulateAclAddsResourcesToAcl()
    {
        $this->_objectFactoryMock->expects($this->any())
            ->method('getModelInstance')
            ->with('Magento_Acl_Resource', $this->anything())
            ->will($this->returnCallback(function($class, $id) {
                return new $class($id);
            }));

        $resourcesDocument = new DOMDocument();
        $resourcesDocument->load(realpath(__DIR__)  .  '/../../_files/acl.xml');
        $xpath = new DOMXPath($resourcesDocument);

        $this->_configMock->expects($this->once())
            ->method('getAclResources')
            ->will($this->returnValue($xpath->query('/config/acl/resources/*')));

        $aclMock = $this->getMock('Magento_Acl');

        $aclMock->expects($this->exactly(5))
            ->method('addResource')
            ->with($this->isInstanceOf('Magento_Acl_Resource'));

        $this->_model->populateAcl($aclMock);
    }
}
