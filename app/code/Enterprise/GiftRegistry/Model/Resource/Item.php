<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift registry entity items resource model
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftRegistry_Model_Resource_Item extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_giftregistry_item', 'item_id');
    }

    /**
     * Add creation date to object
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Core_Model_Resource_Db_Abstract
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        if (!$object->getAddedAt()) {
            $object->setAddedAt($this->formatDate(true));
        }
        return parent::_beforeSave($object);
    }

    /**
     * Load item by registry id and product id
     *
     * @param Enterprise_GiftRegistry_Model_Item $object
     * @param int $registryId
     * @param int $productId
     * @return Enterprise_GiftRegistry_Model_Resource_Item
     */
    public function loadByProductRegistry($object, $registryId, $productId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getMainTable())
            ->where('entity_id = :entity_id')
            ->where('product_id = :product_id');
        $bind = array(
            ':entity_id'  => (int)$registryId,
            ':product_id' => (int)$productId
        );
        $data = $adapter->fetchRow($select, $bind);
        if ($data) {
            $object->setData($data);
        }

        $this->_afterLoad($object);
        return $this;
    }
}
