<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Web API ACL Rules resource model
 *
 * @method array getResources()
 * @method Mage_Webapi_Model_Resource_Acl_Rule setResources(array $resourcesList)
 * @method int getRoleId()
 * @method Mage_Webapi_Model_Resource_Acl_Rule setRoleId(int $roleId)
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Resource_Acl_Rule extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('webapi_rule', 'rule_id');
    }

    /**
     * Get all rules from DB
     *
     * @return array
     */
    public function getRuleList()
    {
        $adapter = $this->getReadConnection();
        $select = $adapter->select()->from($this->getMainTable(), array('resource_id', 'role_id'));
        return $adapter->fetchAll($select);
    }

    /**
     * Save resources
     *
     * @param Mage_Webapi_Model_Acl_Rule $rule
     * @throws Exception
     */
    public function saveResources(Mage_Webapi_Model_Acl_Rule $rule)
    {
        $roleId = $rule->getRoleId();
        if ($roleId > 0) {
            $adapter = $this->_getWriteAdapter();
            $adapter->beginTransaction();

            try {
                $adapter->delete($this->getMainTable(), array('role_id = ?' => $roleId));

                $resources = $rule->getResources();
                if ($resources) {
                    foreach ($resources as $resName) {
                        $adapter->insert($this->getMainTable(), array(
                            'role_id'       => $roleId,
                            'resource_id'   => trim($resName),
                        ));
                    }
                }

                $adapter->commit();
            } catch (Exception $e) {
                $adapter->rollBack();
                throw $e;
            }
        }
    }
}
