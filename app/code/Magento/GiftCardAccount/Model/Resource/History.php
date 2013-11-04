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
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\Core\Model\Resource $resource
     */
    public function __construct(\Magento\Stdlib\DateTime $dateTime, \Magento\Core\Model\Resource $resource)
    {
        $this->dateTime = $dateTime;
        parent::__construct($resource);
    }

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
        $object->setUpdatedAt($this->dateTime->formatDate(time()));
        return parent::_beforeSave($object);
    }
}
