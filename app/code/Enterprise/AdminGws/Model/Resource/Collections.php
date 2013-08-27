<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_AdminGws
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Collections limiter resource model
 *
 * @category    Enterprise
 * @package     Enterprise_AdminGws
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_AdminGws_Model_Resource_Collections extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Admin gws data
     *
     * @var Enterprise_AdminGws_Helper_Data
     */
    protected $_adminGwsData = null;

    /**
     * Class constructor
     *
     *
     *
     * @param Enterprise_AdminGws_Helper_Data $adminGwsData
     * @param Magento_Core_Model_Resource $resource
     */
    public function __construct(
        Enterprise_AdminGws_Helper_Data $adminGwsData,
        Magento_Core_Model_Resource $resource
    ) {
        $this->_adminGwsData = $adminGwsData;
        parent::__construct($resource);
    }

    /**
     * Class construction & resource initialization
     */
    protected function _construct()
    {
        $this->_init('admin_role', 'role_id');
    }

    /**
     * Retreive role ids that has higher/other gws roles
     *
     * @param int $isAll
     * @param array $allowedWebsites
     * @param array $allowedStoreGroups
     * @return array
     */
    public function getRolesOutsideLimitedScope($isAll, $allowedWebsites, $allowedStoreGroups)
    {
        $result = array();
        if (!$isAll) {
            $select = $this->_getReadAdapter()->select();
            $select->from(
                $this->getTable('admin_role'),
                array(
                    'role_id',
                    'gws_is_all',
                    'gws_websites',
                    'gws_store_groups'
                )
            );
            $select->where('parent_id = ?', 0);
            $roles = $this->_getReadAdapter()->fetchAll($select);

            foreach ($roles as $role) {
                $roleStoreGroups = $this->_adminGwsData->explodeIds($role['gws_store_groups']);
                $roleWebsites = $this->_adminGwsData->explodeIds($role['gws_websites']);

                $hasAllPermissions = ($role['gws_is_all'] == 1);

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
                } else if ($roleWebsites) {
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
                } else if ($roleStoreGroups) {
                    $result[] = $role['role_id'];
                    continue;
                }
            }
        }

        return $result;
    }

    /**
     * Retreive user ids that has higher/other gws roles
     *
     * @param int $isAll
     * @param array $allowedWebsites
     * @param array $allowedStoreGroups
     * @return array
     */
    public function getUsersOutsideLimitedScope($isAll, $allowedWebsites, $allowedStoreGroups)
    {
        $result = array();

        $limitedRoles = $this->getRolesOutsideLimitedScope($isAll, $allowedWebsites, $allowedStoreGroups);
        if ($limitedRoles) {
            $select = $this->_getReadAdapter()->select();
            $select->from($this->getTable('admin_role'), array('user_id'))
                ->where('parent_id IN (?)', $limitedRoles);

            $users = $this->_getReadAdapter()->fetchCol($select);

            if ($users) {
                $result = $users;
            }
        }

        return $result;
    }
}
