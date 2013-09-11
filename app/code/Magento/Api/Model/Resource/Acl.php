<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Resource model for admin ACL
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Api\Model\Resource;

class Acl extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize resource connections
     *
     */
    protected function _construct()
    {
        $this->_init('api_role', 'role_id');
    }

    /**
     * Load ACL for the user
     *
     * @return \Magento\Api\Model\Acl
     */
    public function loadAcl()
    {
        $acl = \Mage::getModel('\Magento\Api\Model\Acl');
        $adapter = $this->_getReadAdapter();

        \Mage::getSingleton('Magento\Api\Model\Config')->loadAclResources($acl);

        $rolesArr = $adapter->fetchAll(
            $adapter->select()
                ->from($this->getTable('api_role'))
                ->order(array('tree_level', 'role_type'))
        );
        $this->loadRoles($acl, $rolesArr);

        $rulesArr =  $adapter->fetchAll(
            $adapter->select()
                ->from(array('r'=>$this->getTable('api_rule')))
                ->joinLeft(
                    array('a'=>$this->getTable('api_assert')),
                    'a.assert_id=r.assert_id',
                    array('assert_type', 'assert_data')
                ));
        $this->loadRules($acl, $rulesArr);
        return $acl;
    }

    /**
     * Load roles
     *
     * @param \Magento\Api\Model\Acl $acl
     * @param array $rolesArr
     * @return \Magento\Api\Model\Resource\Acl
     */
    public function loadRoles(\Magento\Api\Model\Acl $acl, array $rolesArr)
    {
        foreach ($rolesArr as $role) {
            $parent = $role['parent_id']>0 ? \Magento\Api\Model\Acl::ROLE_TYPE_GROUP.$role['parent_id'] : null;
            switch ($role['role_type']) {
                case \Magento\Api\Model\Acl::ROLE_TYPE_GROUP:
                    $roleId = $role['role_type'].$role['role_id'];
                    $acl->addRole(
                        \Mage::getModel('\Magento\Api\Model\Acl\Role\Group', array('roleId' => $roleId)),
                        $parent
                    );
                    break;

                case \Magento\Api\Model\Acl::ROLE_TYPE_USER:
                    $roleId = $role['role_type'].$role['user_id'];
                    if (!$acl->hasRole($roleId)) {
                        $acl->addRole(
                            \Mage::getModel('\Magento\Api\Model\Acl\Role\User', array('roleId' => $roleId)),
                            $parent
                        );
                    } else {
                        $acl->addRoleParent($roleId, $parent);
                    }
                    break;
            }
        }

        return $this;
    }

    /**
     * Load rules
     *
     * @param \Magento\Api\Model\Acl $acl
     * @param array $rulesArr
     * @return \Magento\Api\Model\Resource\Acl
     */
    public function loadRules(\Magento\Api\Model\Acl $acl, array $rulesArr)
    {
        foreach ($rulesArr as $rule) {
            $role = $rule['role_type'].$rule['role_id'];
            $resource = $rule['resource_id'];
            $privileges = !empty($rule['api_privileges']) ? explode(',', $rule['api_privileges']) : null;

            $assert = null;
            if (0!=$rule['assert_id']) {
                $assertClass = \Mage::getSingleton('Magento\Api\Model\Config')->getAclAssert($rule['assert_type'])->getClassName();
                $assert = new $assertClass(unserialize($rule['assert_data']));
            }
            try {
                if ($rule['api_permission'] == 'allow') {
                    $acl->allow($role, $resource, $privileges, $assert);
                } else if ($rule['api_permission'] == 'deny') {
                    $acl->deny($role, $resource, $privileges, $assert);
                }
            } catch (\Exception $e) {
                // TODO: properly process exception
            }
        }
        return $this;
    }
}
