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
namespace Magento\GiftCardAccount\Model\Resource;

class History extends \Magento\Core\Model\Resource\Db\AbstractDb
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
     * @param \Magento\Core\Model\AbstractModel $object
     * @return \Magento\GiftCardAccount\Model\Resource\History
     */
    protected function _beforeSave(\Magento\Core\Model\AbstractModel $object)
    {
        $object->setUpdatedAt($this->formatDate(time()));

        return parent::_beforeSave($object);
    }
}
