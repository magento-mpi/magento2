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
 * Web API User resource model
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Resource_Acl_User extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('webapi_user', 'user_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Webapi_Model_Resource_Acl_User
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(
            array(
                'field' => 'user_name',
                'title' => Mage::helper('Mage_Webapi_Helper_Data')->__('User Name')
            ),
        );
        return $this;
    }
}
