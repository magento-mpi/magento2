<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Webapi_Model_Authorization_Loader_Rule implements Magento_Acl_Loader
{
    /**
     * Populate ACL with rules from external storage
     *
     * @param Magento_Acl $acl
     */
    public function populateAcl(Magento_Acl $acl)
    {
        $resourceModel = Mage::getSingleton('Mage_Core_Model_Resource');
        $adapter = $resourceModel->getConnection('read');
        $select = $adapter->select()->from($resourceModel->getTableName("webapi_rule"));
        $ruleList = $adapter->fetchAll($select);
        foreach ($ruleList as $rule) {
            $role = $rule['role_id'];
            $resource = $rule['resource_id'];
            $acl->allow($role, $resource);
        }
    }
}
