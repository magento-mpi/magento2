<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Service_Acl_ResourceLoader implements Magento_Acl_Loader
{
    /**
     * Application object factory
     *
     * @var Mage_Core_Model_Config
     */
    protected $_objectFactory;

    public function __construct()
    {
        $this->_objectFactory = Mage::getConfig();
    }

    /**
     * Populate ACL with resources from external storage
     *
     * @param Magento_Acl $acl
     */
    public function populateAcl(Magento_Acl $acl)
    {
        // parse and build the list of all services
        // @todo remove stub
        $resources = array(
            'Mage_Adminhtml::all' => array(
                'id' => 'Mage_Adminhtml::all'
            )
        );

        $this->_addResourceTree($acl, $resources, null);
    }

    /**
     * Add list of nodes and their children to acl
     *
     * @param Magento_Acl $acl
     * @param array $resources
     * @param Magento_Acl_Resource $parent
     */
    protected function _addResourceTree(Magento_Acl $acl, $resources, Magento_Acl_Resource $parent = null)
    {
        foreach ($resources as $resourceConfig) {
            /** @var $resource Magento_Acl_Resource */
            $resource = $this->_objectFactory->getModelInstance(
                'Magento_Acl_Resource',
                array('resourceId' => $resourceConfig['id'])
            );

            $acl->addResource($resource, $parent);

            if (isset($resourceConfig['children'])) {
                $this->_addResourceTree($acl, $resourceConfig['children'], $resource);
            }
        }
    }
}
