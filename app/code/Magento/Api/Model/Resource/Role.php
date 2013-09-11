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
namespace Magento\Api\Model\Resource;

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
     * @return \Magento\Api\Model\Resource\Role
     */
    protected function _beforeSave(\Magento\Core\Model\AbstractModel $object)
    {
        if (!$object->getId()) {
            $object->setCreated(now());
        }
        $object->setModified(now());
        return $this;
    }

    /**
     * Load an object
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    public function load(\Magento\Core\Model\AbstractModel $object, $value, $field = null)
    {
        if (!intval($value) && is_string($value)) {
            $field = 'role_id';
        }
        return parent::load($object, $value, $field);
    }
}
