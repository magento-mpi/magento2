<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Mage_Core_Model_Acl_Loader_Resource_ResourceAbstract
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
        $loaderResource = $this->getMockForAbstractClass('Mage_Core_Model_Acl_Loader_Resource_ResourceAbstract');

        $factory = new ReflectionProperty($loaderResource, '_resourceFactory');
        $factory->setAccessible(true);
        $factory->setValue($loaderResource, $factoryObject);

        $config = new ReflectionProperty($loaderResource, '_config');
        $config->setAccessible(true);
        $config->setValue($loaderResource, $configObject);

        $loaderResource->populateAcl($acl);
    }

    /**
     * Test for Mage_Core_Model_Acl_Loader_Resource_ResourceAbstract::populateAcl
     *
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Config loader is not defined
     */
    public function testPopulateAclOnInvalidConfig()
    {
        /** @var $loaderResource Mage_Core_Model_Acl_Loader_Resource_ResourceAbstract */
        $loaderResource = $this->getMockForAbstractClass('Mage_Core_Model_Acl_Loader_Resource_ResourceAbstract');

        /** @var $configObject Varien_Object */
        $configObject = $this->getMock('Varien_Object', array(), array(), '', false);

        /** @var $acl Magento_Acl */
        $acl = $this->getMock('Magento_Acl', array(), array(), '', false);

        $config = new ReflectionProperty($loaderResource, '_config');
        $config->setAccessible(true);
        $config->setValue($loaderResource, $configObject);

        $loaderResource->populateAcl($acl);
    }

    /**
     * Test for Mage_Core_Model_Acl_Loader_Resource_ResourceAbstract::populateAcl
     *
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Resource Factory is not defined
     */
    public function testPopulateAclOnInvalidFactory()
    {
        /** @var $loaderResource Mage_Core_Model_Acl_Loader_Resource_ResourceAbstract */
        $loaderResource = $this->getMockForAbstractClass('Mage_Core_Model_Acl_Loader_Resource_ResourceAbstract');

        /** @var $configObject Mage_Core_Model_Acl_Config_ConfigInterface */
        $configObject = $this->getMock('Mage_Core_Model_Acl_Config_ConfigInterface', array(), array(), '', false);

        /** @var $factoryObject Varien_Object */
        $factoryObject = $this->getMock('Varien_Object', array('createResource'), array(), '', false);

        /** @var $acl Magento_Acl */
        $acl = $this->getMock('Magento_Acl', array(), array(), '', false);

        $config = new ReflectionProperty($loaderResource, '_config');
        $config->setAccessible(true);
        $config->setValue($loaderResource, $configObject);

        $factory = new ReflectionProperty($loaderResource, '_resourceFactory');
        $factory->setAccessible(true);
        $factory->setValue($loaderResource, $factoryObject);

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
