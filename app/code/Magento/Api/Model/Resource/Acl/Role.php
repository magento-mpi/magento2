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
 * ACL role resource
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Api\Model\Resource\Acl;

class Role extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('api_role', 'role_id');
    }

    /**
     * Action before save
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return \Magento\Api\Model\Resource\Acl\Role
     */
    protected function _beforeSave(\Magento\Core\Model\AbstractModel $object)
    {
        if (!$object->getId()) {
            $this->setCreated(\Mage::getSingleton('Magento\Core\Model\Date')->gmtDate());
        }
        return $this;
    }
}
