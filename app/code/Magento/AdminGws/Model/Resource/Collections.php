<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Collections limiter resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdminGws\Model\Resource;

class Collections extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Admin gws data
     *
     * @var \Magento\AdminGws\Helper\Data
     */
    protected $_adminGwsData = null;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\AdminGws\Helper\Data $adminGwsData
     */
    public function __construct(\Magento\Framework\App\Resource $resource, \Magento\AdminGws\Helper\Data $adminGwsData)
    {
        $this->_adminGwsData = $adminGwsData;
        parent::__construct($resource);
    }

    /**
     * Class construction & resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('authorization_role', 'role_id');
    }

    /**
     * Retrieve role ids that has higher/other gws roles
     *
     * @param int $isAll
     * @param array $allowedWebsites
     * @param array $allowedStoreGroups
     * @return array
     */
    public function getRolesOutsideLimitedScope($isAll, $allowedWebsites, $allowedStoreGroups)
    {
        $result = [];
        if (!$isAll) {
            $select = $this->_getReadAdapter()->select();
            $select->from(
                $this->getTable('authorization_role'),
                ['role_id', 'gws_is_all', 'gws_websites', 'gws_store_groups']
            );
            $select->where('parent_id = ?', 0);
            $roles = $this->_getReadAdapter()->fetchAll($select);

            foreach ($roles as $role) {
                $roleStoreGroups = $this->_adminGwsData->explodeIds($role['gws_store_groups']);
                $roleWebsites = $this->_adminGwsData->explodeIds($role['gws_websites']);

                $hasAllPermissions = $role['gws_is_all'] == 1;

                if ($hasAllPermissions) {
                    $result[] = $role['role_id'];
                    continue;
                }

                if ($allowedWebsites) {
                    foreach ($roleWebsites as $website) {
                        if (!in_array($website, $allowedWebsites)) {
                            $result[] = $role['role_id'];
                            continue 2;
                        }
                    }
                } elseif ($roleWebsites) {
                    $result[] = $role['role_id'];
                    continue;
                }

                if ($allowedStoreGroups) {
                    foreach ($roleStoreGroups as $group) {
                        if (!in_array($group, $allowedStoreGroups)) {
                            $result[] = $role['role_id'];
                            continue 2;
                        }
                    }
                } elseif ($roleStoreGroups) {
                    $result[] = $role['role_id'];
                    continue;
                }
            }
        }

        return $result;
    }

    /**
     * Retrieve user ids that has higher/other gws roles
     *
     * @param int $isAll
     * @param array $allowedWebsites
     * @param array $allowedStoreGroups
     * @return array
     */
    public function getUsersOutsideLimitedScope($isAll, $allowedWebsites, $allowedStoreGroups)
    {
        $result = [];

        $limitedRoles = $this->getRolesOutsideLimitedScope($isAll, $allowedWebsites, $allowedStoreGroups);
        if ($limitedRoles) {
            $select = $this->_getReadAdapter()->select();
            $select->from($this->getTable('authorization_role'), ['user_id'])
                ->where('parent_id IN (?)', $limitedRoles);

            $users = $this->_getReadAdapter()->fetchCol($select);

            if ($users) {
                $result = $users;
            }
        }

        return $result;
    }
}
