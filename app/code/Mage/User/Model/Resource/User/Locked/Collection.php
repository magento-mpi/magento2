<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Admin user collection
 *
 * @category    Mage
 * @package     Mage_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_User_Model_Resource_User_Locked_Collection extends Mage_User_Model_Resource_User_Collection
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
