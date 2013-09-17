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
class Magento_Oauth_Model_Resource_Consumer extends Magento_Core_Model_Resource_Db_Abstract
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
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Oauth_Model_Resource_Consumer
     */
    public function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        $object->setUpdatedAt($this->formatDate(time()));
        return parent::_beforeSave($object);
    }

    /**
     * Delete all Nonce entries associated with the consumer
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Oauth_Model_Resource_Consumer
     */
    public function _afterDelete(Magento_Core_Model_Abstract $object)
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->delete($this->getTable('oauth_nonce'), array('consumer_id' => $object->getId()));
        $adapter->delete($this->getTable('oauth_token'), array('consumer_id' => $object->getId()));
        return parent::_afterDelete($object);
    }
}
