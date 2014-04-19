<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model\Resource;

/**
 * Gift registry entity items resource model
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Item extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Stdlib\DateTime $dateTime
     */
    public function __construct(\Magento\Framework\App\Resource $resource, \Magento\Stdlib\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
        parent::__construct($resource);
    }

    /**
     * Resource model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_giftregistry_item', 'item_id');
    }

    /**
     * Add creation date to object
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\Model\Resource\Db\AbstractDb
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getAddedAt()) {
            $object->setAddedAt($this->dateTime->formatDate(true));
        }
        return parent::_beforeSave($object);
    }

    /**
     * Load item by registry id and product id
     *
     * @param \Magento\GiftRegistry\Model\Item $object
     * @param int $registryId
     * @param int $productId
     * @return $this
     */
    public function loadByProductRegistry($object, $registryId, $productId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(
            $this->getMainTable()
        )->where(
            'entity_id = :entity_id'
        )->where(
            'product_id = :product_id'
        );
        $bind = array(':entity_id' => (int)$registryId, ':product_id' => (int)$productId);
        $data = $adapter->fetchRow($select, $bind);
        if ($data) {
            $object->setData($data);
        }

        $this->_afterLoad($object);
        return $this;
    }
}
