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
class Magento_Api_Model_Resource_Role extends Magento_Core_Model_Resource_Db_Abstract
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
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Api_Model_Resource_Role
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $object)
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
     * @param Magento_Core_Model_Abstract $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return Magento_Core_Model_Resource_Db_Abstract
     */
    public function load(Magento_Core_Model_Abstract $object, $value, $field = null)
    {
        if (!intval($value) && is_string($value)) {
            $field = 'role_id';
        }
        return parent::load($object, $value, $field);
    }
}
