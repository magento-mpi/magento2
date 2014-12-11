<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerBalance\Model\Resource\Balance;

/**
 * Customerbalance history resource model
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
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_customerbalance_history', 'history_id');
    }

    /**
     * Set updated_at automatically before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setUpdatedAt($this->dateTime->formatDate(time()));
        return parent::_beforeSave($object);
    }

    /**
     * Mark specified balance history record as sent to customer
     *
     * @param int $id
     * @return void
     */
    public function markAsSent($id)
    {
        $this->_getWriteAdapter()->update(
            $this->getMainTable(),
            ['is_customer_notified' => 1],
            ['history_id = ?' => $id]
        );
    }
}
