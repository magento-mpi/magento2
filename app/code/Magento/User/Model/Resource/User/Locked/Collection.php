<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Admin user collection
 *
 * @category    Magento
 * @package     Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_User_Model_Resource_User_Locked_Collection extends Magento_User_Model_Resource_User_Collection
{
    /**
     * Collection Init Select
     *
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToFilter('lock_expires', array('notnull' => true));
    }
}
