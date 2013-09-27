<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth Application resource model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Oauth\Model\Resource;

class Consumer extends \Magento\Core\Model\Resource\Db\AbstractDb
{
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
     * @return \Magento\Oauth\Model\Resource\Consumer
     */
    public function _beforeSave(\Magento\Core\Model\AbstractModel $object)
    {
        $object->setUpdatedAt($this->formatDate(time()));
        return parent::_beforeSave($object);
    }

    /**
     * Delete all Nonce entries associated with the consumer
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return \Magento\Oauth\Model\Resource\Consumer
     */
    public function _afterDelete(\Magento\Core\Model\AbstractModel $object)
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->delete($this->getTable('oauth_nonce'), array('consumer_id' => $object->getId()));
        $adapter->delete($this->getTable('oauth_token'), array('consumer_id' => $object->getId()));
        return parent::_afterDelete($object);
    }
}
