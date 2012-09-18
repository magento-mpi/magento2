<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Webapi_Model_Authorization_Loader_Role implements Magento_Acl_Loader
{
    /**
     * Populate ACL with roles from external storage
     *
     * @param Magento_Acl $acl
     */
    public function populateAcl(Magento_Acl $acl)
    {
        /** @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('Mage_Core_Model_Resource');
        $roleTableName = $resource->getTableName('webapi_role');
        $adapter = $resource->getConnection('read');
        $select = $adapter->select() ->from($roleTableName);
        $roleList = $adapter->fetchAll($select);

        foreach ($roleList as $role) {
            /** @var $aclRole Mage_Webapi_Model_Authorization_Role */
            $aclRole = Mage::getConfig()->getModelInstance(
                'Mage_Webapi_Model_Authorization_Role',
                $role['role_id']
            );
            $acl->addRole($aclRole);
            //Deny all privileges to Role. Some of them could be allowed later by whitelist
            $acl->deny($aclRole);
        }
    }
}
