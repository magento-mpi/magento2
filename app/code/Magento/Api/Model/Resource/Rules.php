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
 * Rules resource model
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Api\Model\Resource;

class Rules extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('api_rule', 'rule_id');
    }

    /**
     * Save rule
     *
     * @param \Magento\Api\Model\Rules $rule
     */
    public function saveRel(\Magento\Api\Model\Rules $rule)
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->beginTransaction();

        try {
            $roleId = $rule->getRoleId();
            $adapter->delete($this->getMainTable(), array('role_id = ?' => $roleId));
            $masterResources = \Mage::getModel('\Magento\Api\Model\Roles')->getResourcesList2D();
            $masterAdmin = false;
            if ($postedResources = $rule->getResources()) {
                foreach ($masterResources as $index => $resName) {
                    if (!$masterAdmin) {
                        $permission = (in_array($resName, $postedResources))? 'allow' : 'deny';
                        $adapter->insert($this->getMainTable(), array(
                            'role_type'     => 'G',
                            'resource_id'   => trim($resName, '/'),
                            'api_privileges'    => null,
                            'assert_id'     => 0,
                            'role_id'       => $roleId,
                            'api_permission'    => $permission
                            ));
                    }
                    if ($resName == 'all' && $permission == 'allow') {
                        $masterAdmin = true;
                    }
                }
            }

            $adapter->commit();
        } catch (\Magento\Core\Exception $e) {
            throw $e;
        } catch (\Exception $e) {
            $adapter->rollBack();
        }
    }
}
