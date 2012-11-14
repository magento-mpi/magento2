<?php
/**
 * Test for Mage_Core_Model_Acl_Loader_Resource_ResourceAbstract
 *
 * @copyright {}
 */
class Mage_Core_Model_Acl_Loader_Resource_ResourceAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for Mage_Core_Model_Acl_Loader_Resource_ResourceAbstract::populateAcl
     */
    public function testPopulateAclOnValidObjects()
    {
        /** @var $aclResource Magento_Acl_Resource */
        $aclResource = $this->getMock('Magento_Acl_Resource', array(), array(), '', false);

        /** @var $acl Magento_Acl */
        $acl = $this->getMock('Magento_Acl', array('addResource'), array(), '', false);
        $acl->expects($this->exactly(3))->method('addResource');
        $acl->expects($this->at(0))->method('addResource')->with($aclResource, null)->will($this->returnSelf());
        $acl->expects($this->at(1))->method('addResource')->with($aclResource, $aclResource)->will($this->returnSelf());
        $acl->expects($this->at(2))->method('addResource')->with($aclResource, $aclResource)->will($this->returnSelf());

        /** @var $factoryObject Mage_Core_Model_Config */
        $factoryObject = $this->getMock('Magento_Acl_ResourceFactory', array('createResource'), array(), '', false);
        $factoryObject->expects($this->any())->method('createResource')->will($this->returnValue($aclResource));

        /** @var $configObject Mage_Core_Model_Acl_Config_ConfigInterface */
        $configObject = $this->getMock('Mage_Core_Model_Acl_Config_ConfigInterface',
            array('getAclResources'), array(), '', false);
        $configObject->expects($this->once())->method('getAclResources')
            ->will($this->returnCallback(array($this, 'getResourceNodeList')));

        /** @var $loaderResource Mage_Core_Model_Acl_Loader_Resource_ResourceAbstract */
        $loaderResource = $this->getMockForAbstractClass('Mage_Core_Model_Acl_Loader_Resource_ResourceAbstract',
            array($configObject, $factoryObject));

        $loaderResource->populateAcl($acl);
    }

    /**
     * Get Resources DOMNodeList from fixture
     *
     * @return DOMNodeList
     */
    public function getResourceNodeList()
    {
        $aclResources = new DOMDocument();
        $aclResources->load(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
            . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files'
            . DIRECTORY_SEPARATOR . 'acl_resources.xml');
        $xpath = new DOMXPath($aclResources);
        return $xpath->query('/config/resources/*');
    }
}
