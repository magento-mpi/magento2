<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA entity resource model
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Rma\Model\Resource;

class Item extends \Magento\Eav\Model\Entity\AbstractEntity
{
    /**
     * Store firstly set attributes to filter selected attributes when used specific store_id
     *
     * @var array
     */
    protected $_attributes   = array();

    /**
     * Array of aviable items types for rma
     *
     * @var array
     */
    protected $_aviableProductTypes = array(
        \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
        \Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE,
        \Magento\Catalog\Model\Product\Type::TYPE_GROUPED,
        \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
    );

    /**
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaData;

    /**
     * @var \Magento\Sales\Model\Resource\Order\Item\CollectionFactory
     */
    protected $_ordersFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * Main constructor
     *
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param \Magento\Sales\Model\Resource\Order\Item\CollectionFactory $ordersFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rma\Helper\Data $rmaData,
        \Magento\Sales\Model\Resource\Order\Item\CollectionFactory $ordersFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        $data = array()
    ) {
        $this->_rmaData = $rmaData;
        $this->_ordersFactory = $ordersFactory;
        $this->_productFactory = $productFactory;
        parent::__construct($data);
    }

    /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->setType('rma_item');
        $this->setConnection('rma_item_read', 'rma_item_write');
    }

    /**
     * Redeclare attribute model
     *
     * @return string
     */
    protected function _getDefaultAttributeModel()
    {
        return 'magento_rma/item_attribute';
    }

    /**
     * Returns default Store ID
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        return \Magento\Catalog\Model\AbstractModel::DEFAULT_STORE_ID;
    }

    /**
     * Check whether the attribute is Applicable to the object
     *
     * @param \Magento\Object $object
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute $attribute
     * @return boolean
     */
    protected function _isApplicableAttribute($object, $attribute)
    {
        $applyTo = $attribute->getApplyTo();
        return count($applyTo) == 0 || in_array($object->getTypeId(), $applyTo);
    }

    /**
     * Check whether attribute instance (attribute, backend, frontend or source) has method and applicable
     *
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute|\Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend|\Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend|\Magento\Eav\Model\Entity\Attribute\Source\AbstractSource $instance
     * @param string $method
     * @param array $args array of arguments
     * @return boolean
     */
    protected function _isCallableAttributeInstance($instance, $method, $args)
    {
        if ($instance instanceof \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
            && ($method == 'beforeSave' || $method = 'afterSave')
        ) {
            $attributeCode = $instance->getAttribute()->getAttributeCode();
            if (isset($args[0]) && $args[0] instanceof \Magento\Object && $args[0]->getData($attributeCode) === false) {
                return false;
            }
        }

        return parent::_isCallableAttributeInstance($instance, $method, $args);
    }

    /**
     * Reset firstly loaded attributes
     *
     * @param \Magento\Object $object
     * @param integer $entityId
     * @param array|null $attributes
     * @return \Magento\Catalog\Model\Resource\AbstractResource
     */
    public function load($object, $entityId, $attributes = array())
    {
        $this->_attributes = array();
        return parent::load($object, $entityId, $attributes);
    }

    /**
     * Gets rma authorized items ids an qty by rma id
     *
     * @param  int $rmaId
     * @return array
     */
    public function getAuthorizedItems($rmaId)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->getTable('magento_rma_item_entity'), array())
            ->where('rma_entity_id = ?', $rmaId)
            ->where('status = ?', \Magento\Rma\Model\Rma\Source\Status::STATE_AUTHORIZED)
            ->group(array('order_item_id', 'product_name'))
            ->columns(
                array(
                    'order_item_id' => 'order_item_id',
                    'qty'           => new \Zend_Db_Expr('SUM(qty_authorized)'),
                    'product_name'  => new \Zend_Db_Expr('MAX(product_name)')
                )
            )
        ;

        $return     = $adapter->fetchAll($select);
        $itemsArray = array();
        if (!empty($return)) {
            foreach ($return as $item) {
                $itemsArray[$item['order_item_id']] = $item;
            }
        }
        return $itemsArray;
    }

    /**
     * Gets rma items ids by order
     *
     * @param  int $orderId
     * @return array
     */
    public function getItemsIdsByOrder($orderId)
    {
        $adapter = $this->_getReadAdapter();

        $subSelect = $adapter->select()
            ->from(
                array('main' => $this->getTable('magento_rma')),
                array()
            )
            ->where('main.order_id = ?', $orderId)
            ->where('main.status NOT IN (?)',
                array(
                    \Magento\Rma\Model\Rma\Source\Status::STATE_CLOSED,
                    \Magento\Rma\Model\Rma\Source\Status::STATE_PROCESSED_CLOSED
                )
            );

        $select = $adapter->select()
            ->from(
                array('item_entity' => $this->getTable('magento_rma_item_entity')),
                array('item_entity.order_item_id','item_entity.order_item_id')
            )
            ->exists($subSelect, 'main.entity_id = item_entity.rma_entity_id');

        return array_values($adapter->fetchPairs($select));
    }

    /**
     * Gets order items collection
     *
     * @param int $orderId
     * @return \Magento\Sales\Model\Resource\Order\Item\Collection
     */
    public function getOrderItemsCollection($orderId)
    {
        $adapter = $this->getReadConnection();
        $expression = new \Zend_Db_Expr('(' . $adapter->quoteIdentifier('qty_shipped') . ' - '
            . $adapter->quoteIdentifier('qty_returned') . ')');
        /** @var $collection \Magento\Sales\Model\Resource\Order\Item\Collection */
        $collection = $this->_ordersFactory->create();
        return $collection->addExpressionFieldToSelect(
                'available_qty',
                $expression,
                array('qty_shipped', 'qty_returned')
            )
            ->addFieldToFilter('order_id', $orderId)
            ->addFieldToFilter('product_type', array("in" => $this->_aviableProductTypes))
            ->addFieldToFilter($expression, array("gt" => 0));
    }

    /**
     * Gets available order items collection
     *
     * @param  int $orderId
     * @param  int|bool $parentId if need retrieves only bundle and its children
     * @return \Magento\Sales\Model\Resource\Order\Item\Collection
     */
    public function getOrderItems($orderId, $parentId = false)
    {
        $getItemsIdsByOrder     = $this->getItemsIdsByOrder($orderId);

        /** @var $orderItemsCollection \Magento\Sales\Model\Resource\Order\Item\Collection */
        $orderItemsCollection   = $this->getOrderItemsCollection($orderId);


        if (!$orderItemsCollection->count()) {
            return $orderItemsCollection;
        }

        /**
         * contains data that defines possibility of return for an order item
         * array value ['self'] refers to item's own rules
         * array value ['child'] refers to rules defined from item's sub-items
         */
        $parent = array();

        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->_productFactory->create();

        foreach ($orderItemsCollection as $item) {
            /* retrieves only bundle and children by $parentId */
            if ($parentId && ($item->getId() != $parentId) && ($item->getParentItemId() != $parentId)) {
                $orderItemsCollection->removeItemByKey($item->getId());
                continue;
            }

            $allowed = true;
            /* checks item in active rma */
            if (in_array($item->getId(), $getItemsIdsByOrder)) {
                $allowed = false;
            }


            /* checks enable on product level */
            $product->reset();
            $product->setStoreId($item->getStoreId());
            $product->load($item->getProductId());

            if (!$this->_rmaData->canReturnProduct($product, $item->getStoreId())) {
                $allowed = false;
            }

            if ($item->getParentItemId()) {
                if (!isset($parent[$item->getParentItemId()]['child'])) {
                    $parent[$item->getParentItemId()]['child'] = false;
                }
                if (!$allowed) {
                    $item->setIsOrdered(1);
                    $item->setAvailableQty($item->getQtyShipped()-$item->getQtyRefunded()-$item->getQtyCanceled());
                }
                $parent[$item->getParentItemId()]['child']  = $parent[$item->getParentItemId()]['child'] || $allowed;
                $parent[$item->getItemId()]['self']         = false;
            } else {
                $parent[$item->getItemId()]['self']         = $allowed;
            }
        }

        $bundle = false;
        foreach ($orderItemsCollection as $item) {
            if (isset($parent[$item->getId()]['child'])
                && ($parent[$item->getId()]['child'] === false || $parent[$item->getId()]['self'] == false)
            ) {
                $orderItemsCollection->removeItemByKey($item->getId());
                $bundle = $item->getId();
                continue;
            }

            if ($bundle && $item->getParentItemId() && $bundle == $item->getParentItemId()) {
                $orderItemsCollection->removeItemByKey($item->getId());
            } elseif (isset($parent[$item->getId()]['self']) && $parent[$item->getId()]['self'] === false) {
                if ($item->getParentItemId() && $bundle != $item->getParentItemId()) {

                } else {
                    $orderItemsCollection->removeItemByKey($item->getId());
                    continue;
                }
            }

            if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
                && !isset($parent[$item->getId()]['child'])
            ) {
                $orderItemsCollection->removeItemByKey($item->getId());
                continue;
            }

            if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE) {
                $productOptions     = $item->getProductOptions();
                $product->reset();
                $product->load($product->getIdBySku($productOptions['simple_sku']));
                if (!$this->_rmaData->canReturnProduct($product, $item->getStoreId())) {
                    $orderItemsCollection->removeItemByKey($item->getId());
                    continue;
                }
            }

            $item->setName($this->getProductName($item));
        }

        return $orderItemsCollection;
    }

    /**
     * Gets Product Name
     *
     * @param $item \Magento\Sales\Model\Order\Item
     * @return string
     */
    public function getProductName($item)
    {
        $name   = $item->getName();
        $result = array();
        if ($options = $item->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }

            if (!empty($result)) {
                $implode = array();
                foreach ($result as $val) {
                    $implode[] =  isset($val['print_value']) ? $val['print_value'] : $val['value'];
                }
                return $name.' ('.implode(', ', $implode).')';
            }
        }
        return $name;
    }
}
