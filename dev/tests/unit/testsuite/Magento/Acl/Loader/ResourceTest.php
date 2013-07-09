<?php
/**
 * Test for Magento_Acl_Loader_Resource
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Acl_Loader_ResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for Magento_Acl_Loader_Resource::populateAcl
     */
    public function testPopulateAclOnValidObjects()
    {
        /** @var $aclResource Magento_Acl_Resource */
        $aclResource = $this->getMock('Magento_Acl_Resource', array(), array(), '', false);

        /** @var $acl Magento_Acl */
        $acl = $this->getMock('Magento_Acl', array('addResource'), array(), '', false);
        $acl->expects($this->exactly(2))->method('addResource');
        $acl->expects($this->at(0))->method('addResource')->with($aclResource, null)->will($this->returnSelf());
        $acl->expects($this->at(1))->method('addResource')->with($aclResource, $aclResource)->will($this->returnSelf());

        /** @var $factoryObject Mage_Core_Model_Config */
        $factoryObject = $this->getMock('Magento_Acl_ResourceFactory', array('createResource'), array(), '', false);
        $factoryObject->expects($this->any())->method('createResource')->will($this->returnValue($aclResource));

        /** @var $configObject Magento_Acl_Resource_ConfigInterface */
        $configObject = $this->getMock('Magento_Acl_Loader_Resource_ConfigReaderInterface');
        $configObject->expects($this->once())->method('getAclResources')
            ->will($this->returnValue(
                include realpath(__DIR__ . '/../_files/loader/resource/configReader/xml/result.php')
            ));

        /** @var $loaderResource Magento_Acl_Loader_Resource */
        $loaderResource = new Magento_Acl_Loader_Resource($configObject, $factoryObject);

        $loaderResource->populateAcl($acl);
    }
}
