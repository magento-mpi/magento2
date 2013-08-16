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

        /** @var $factoryObject Magento_Core_Model_Config */
        $factoryObject = $this->getMock('Magento_Acl_ResourceFactory', array('createResource'), array(), '', false);
        $factoryObject->expects($this->any())->method('createResource')->will($this->returnValue($aclResource));

        /** @var $resourceProvider Magento_Acl_Resource_ProviderInterface */
        $resourceProvider = $this->getMock('Magento_Acl_Resource_ProviderInterface');
        $resourceProvider->expects($this->once())
            ->method('getAclResources')
            ->will($this->returnValue(array(
                array(
                    'id' => 'parent_resource::id',
                    'title' => 'Parent Resource Title',
                    'sortOrder' => 10,
                    'children' => array(
                        array(
                            'id' => 'child_resource::id',
                            'title' => 'Child Resource Title',
                            'sortOrder' => 10,
                            'children' => array(),
                        ),
                    ),
                ),
            )));

        /** @var $loaderResource Magento_Acl_Loader_Resource */
        $loaderResource = new Magento_Acl_Loader_Resource($resourceProvider, $factoryObject);

        $loaderResource->populateAcl($acl);
    }
}
