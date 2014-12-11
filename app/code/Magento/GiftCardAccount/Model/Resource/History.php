<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftCardAccount\Model\Resource;

/**
 * GiftCard account history serource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class History extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     */
    public function __construct(\Magento\Framework\App\Resource $resource, \Magento\Framework\Stdlib\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
        parent::__construct($resource);
    }

    /**
     * Define main table and primary key field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_giftcardaccount_history', 'history_id');
    }

    /**
     * Setting "updated_at" date before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setUpdatedAt($this->dateTime->formatDate(time()));
        return parent::_beforeSave($object);
    }
}
