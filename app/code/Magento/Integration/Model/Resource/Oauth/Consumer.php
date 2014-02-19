<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Integration\Model\Resource\Oauth;

class Consumer extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $_dateTime;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Stdlib\DateTime $dateTime
     */
    public function __construct(\Magento\App\Resource $resource, \Magento\Stdlib\DateTime $dateTime)
    {
        $this->_dateTime = $dateTime;
        parent::__construct($resource);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('oauth_consumer', 'entity_id');
    }

    /**
     * Set updated_at automatically before saving
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return $this
     */
    public function _beforeSave(\Magento\Core\Model\AbstractModel $object)
    {
        $object->setUpdatedAt($this->_dateTime->formatDate(time()));
        return parent::_beforeSave($object);
    }

    /**
     * Delete all Nonce entries associated with the consumer
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return $this
     */
    public function _afterDelete(\Magento\Core\Model\AbstractModel $object)
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->delete($this->getTable('oauth_nonce'), array('consumer_id' => $object->getId()));
        $adapter->delete($this->getTable('oauth_token'), array('consumer_id' => $object->getId()));
        return parent::_afterDelete($object);
    }
}
