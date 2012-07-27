<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_User_Acl_Loader_Resource implements Magento_Acl_Loader
{
    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_config = isset($data['config']) ? $data['config'] : Mage::getSingleton('Mage_Backend_Model_Acl_Config');
    }

    /**
     * Populate ACL with resources from external storage
     *
     * @param Magento_Acl $acl
     */
    public function populateAcl(Magento_Acl $acl)
    {
        if (is_null($resource)) {
            $resource = $this->getAdminhtmlConfig()->getNode("acl/resources");
            $resourceName = null;
        } else {
            $resourceName = (is_null($parentName) ? '' : $parentName . '/') . $resource->getName();
            $acl->add(Mage::getModel('Magento_Acl_Resource', $resourceName), $parentName);
        }

        if (isset($resource->all)) {
            $acl->add(Mage::getModel('Magento_Acl_Resource', 'all'), null);
        }

        if (isset($resource->admin)) {
            $children = $resource->admin;
        } elseif (isset($resource->children)){
            $children = $resource->children->children();
        }



        if (empty($children)) {
        }

        foreach ($children as $res) {
            if (1 == $res->disabled) {
                continue;
            }
            $this->loadAclResources($acl, $res, $resourceName);
        }
    }
}
