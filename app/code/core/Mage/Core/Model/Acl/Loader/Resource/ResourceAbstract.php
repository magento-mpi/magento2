<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstraction of ACL Resource Loader
 */
abstract class Mage_Core_Model_Acl_Loader_Resource_ResourceAbstract implements Magento_Acl_Loader
{
    /**
     * Acl config
     *
     * @var Mage_Core_Model_Acl_Config_ConfigInterface
     */
    protected $_config;

    /**
     * Application object factory
     *
     * @var Magento_Acl_ResourceFactory
     */
    protected $_resourceFactory;

    /**
     * Populate ACL with resources from external storage
     *
     * @param Magento_Acl $acl
     * @throws Mage_Core_Exception
     */
    public function populateAcl(Magento_Acl $acl)
    {
        if (!($this->_config instanceof Mage_Core_Model_Acl_Config_ConfigInterface)) {
            throw new Mage_Core_Exception('Config loader is not defined');
        }
        if (!($this->_resourceFactory instanceof Magento_Acl_ResourceFactory)) {
            throw new Mage_Core_Exception('Resource Factory is not defined');
        }
        $this->_addResourceTree($acl, $this->_config->getAclResources(), null);
    }

    /**
     * Add list of nodes and their children to acl
     *
     * @param Magento_Acl $acl
     * @param DOMNodeList $resources
     * @param Magento_Acl_Resource $parent
     */
    protected function _addResourceTree(Magento_Acl $acl, DOMNodeList $resources, Magento_Acl_Resource $parent = null)
    {
        /** @var $resourceConfig DOMElement */
        foreach ($resources as $resourceConfig) {
            if (!($resourceConfig instanceof DOMElement)) {
                continue;
            }
            /** @var $resource Magento_Acl_Resource */
            $resource = $this->_resourceFactory->createResource(array($resourceConfig->getAttribute('id')));
            $acl->addResource($resource, $parent);
            if ($resourceConfig->hasChildNodes()) {
                $this->_addResourceTree($acl, $resourceConfig->childNodes, $resource);
            }
        }
    }
}
