<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * GiftCard account history serource model
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftCardAccount_Model_Resource_History extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table and primary key field
     *
     */
    protected function _construct()
    {
        $this->_init('magento_giftcardaccount_history', 'history_id');
    }

    /**
     * Setting "updated_at" date before saving
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_GiftCardAccount_Model_Resource_History
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        $object->setUpdatedAt($this->formatDate(time()));

        return parent::_beforeSave($object);
    }
}
