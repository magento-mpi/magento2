<?php
/**
 * Resource model for ACL rule.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method array getResources() getResources()
 * @method \Magento\Webapi\Model\Resource\Acl\Rule setResources() setResources(array $resourcesList)
 * @method int getRoleId() getRoleId()
 * @method \Magento\Webapi\Model\Resource\Acl\Rule setRoleId() setRoleId(int $roleId)
 */
namespace Magento\Webapi\Model\Resource\Acl;

class Rule extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Resource initialization.
     */
    protected function _construct()
    {
        $this->_init('webapi_rule', 'rule_id');
    }

    /**
     * Get all rules from DB.
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
     * Get resource IDs assigned to role.
     *
     * @param integer $roleId Web api user role ID
     * @return array
     */
    public function getResourceIdsByRole($roleId)
    {
        $adapter = $this->getReadConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), array('resource_id'))
            ->where('role_id = ?', (int)$roleId);
        return $adapter->fetchCol($select);
    }

    /**
     * Save resources.
     *
     * @param \Magento\Webapi\Model\Acl\Rule $rule
     * @throws \Exception
     */
    public function saveResources(\Magento\Webapi\Model\Acl\Rule $rule)
    {
        $roleId = $rule->getRoleId();
        if ($roleId > 0) {
            $adapter = $this->_getWriteAdapter();
            $adapter->beginTransaction();

            try {
                $adapter->delete($this->getMainTable(), array('role_id = ?' => (int)$roleId));

                $resources = $rule->getResources();
                if ($resources) {
                    $resourcesToInsert = array();
                    foreach ($resources as $resName) {
                        $resourcesToInsert[] = array(
                            'role_id'       => $roleId,
                            'resource_id'   => trim($resName)
                        );
                    }
                    $adapter->insertArray(
                        $this->getMainTable(),
                        array('role_id', 'resource_id'),
                        $resourcesToInsert
                    );
                }

                $adapter->commit();
            } catch (\Exception $e) {
                $adapter->rollBack();
                throw $e;
            }
        }
    }
}
