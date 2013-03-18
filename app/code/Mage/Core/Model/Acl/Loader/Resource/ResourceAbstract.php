<?php
/**
 * Abstraction of ACL Resource Loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
     * @param Mage_Core_Model_Acl_Config_ConfigInterface $configuration
     * @param Magento_Acl_ResourceFactory $resourceFactory
     */
    public function __construct(Mage_Core_Model_Acl_Config_ConfigInterface $configuration,
        Magento_Acl_ResourceFactory $resourceFactory
    ) {
        $this->_config = $configuration;
        $this->_resourceFactory = $resourceFactory;
    }

    /**
     * Populate ACL with resources from external storage
     *
     * @param Magento_Acl $acl
     * @throws Mage_Core_Exception
     */
    public function populateAcl(Magento_Acl $acl)
    {
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
            $resource = $this->_resourceFactory->createResource(
                array('resourceId' => $resourceConfig->getAttribute('id'))
            );
            $acl->addResource($resource, $parent);
            if ($resourceConfig->hasChildNodes()) {
                $this->_addResourceTree($acl, $resourceConfig->childNodes, $resource);
            }
        }
    }
}
