<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grouped product type implementation
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Type_Grouped extends Magento_Catalog_Model_Product_Type_Abstract
{
    const TYPE_CODE = 'grouped';

    /**
     * Cache key for Associated Products
     *
     * @var string
     */
    protected $_keyAssociatedProducts   = '_cache_instance_associated_products';

    /**
     * Cache key for Associated Product Ids
     *
     * @var string
     */
    protected $_keyAssociatedProductIds = '_cache_instance_associated_product_ids';

    /**
     * Cache key for Status Filters
     *
     * @var string
     */
    protected $_keyStatusFilters        = '_cache_instance_status_filters';

    /**
     * Product is composite properties
     *
     * @var bool
     */
    protected $_isComposite             = true;

    /**
     * Product is configurable
     *
     * @var bool
     */
    protected $_canConfigure            = true;

    /**
     * Initialize data
     *
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Helper_File_Storage_Database $fileStorageDb
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Logger $logger
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Helper_File_Storage_Database $fileStorageDb,
        Magento_Filesystem $filesystem,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Logger $logger,
        array $data = array()
    ) {
        parent::__construct($eventManager, $coreData, $fileStorageDb, $filesystem, $coreRegistry, $logger, $data);
    }

    /**
     * Return relation info about used products
     *
     * @return Magento_Object Object with information data
     */
    public function getRelationInfo()
    {
        $info = new Magento_Object();
        $info->setTable('catalog_product_link')
            ->setParentFieldName('product_id')
            ->setChildFieldName('linked_product_id')
            ->setWhere('link_type_id=' . Magento_Catalog_Model_Product_Link::LINK_TYPE_GROUPED);
        return $info;
    }

    /**
     * Retrieve Required children ids
     * Return grouped array, ex array(
     *   group => array(ids)
     * )
     *
     * @param int $parentId
     * @param bool $required
     * @return array
     */
    public function getChildrenIds($parentId, $required = true)
    {
        return Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Product_Link')
            ->getChildrenIds($parentId,
                Magento_Catalog_Model_Product_Link::LINK_TYPE_GROUPED);
    }

    /**
     * Retrieve parent ids array by requered child
     *
     * @param int|array $childId
     * @return array
     */
    public function getParentIdsByChild($childId)
    {
        return Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Product_Link')
            ->getParentIdsByChild($childId,
                Magento_Catalog_Model_Product_Link::LINK_TYPE_GROUPED);
    }

    /**
     * Retrieve array of associated products
     *
     * @param Magento_Catalog_Model_Product $product
     * @return array
     */
    public function getAssociatedProducts($product)
    {
        if (!$product->hasData($this->_keyAssociatedProducts)) {
            $associatedProducts = array();

            if (!Mage::app()->getStore()->isAdmin()) {
                $this->setSaleableStatus($product);
            }

            $collection = $this->getAssociatedProductCollection($product)
                ->addAttributeToSelect('*')
                ->addFilterByRequiredOptions()
                ->setPositionOrder()
                ->addStoreFilter($this->getStoreFilter($product))
                ->addAttributeToFilter('status', array('in' => $this->getStatusFilters($product)));

            foreach ($collection as $item) {
                $associatedProducts[] = $item;
            }

            $product->setData($this->_keyAssociatedProducts, $associatedProducts);
        }
        return $product->getData($this->_keyAssociatedProducts);
    }

    /**
     * Add status filter to collection
     *
     * @param  int $status
     * @param  Magento_Catalog_Model_Product $product
     * @return Magento_Catalog_Model_Product_Type_Grouped
     */
    public function addStatusFilter($status, $product)
    {
        $statusFilters = $product->getData($this->_keyStatusFilters);
        if (!is_array($statusFilters)) {
            $statusFilters = array();
        }

        $statusFilters[] = $status;
        $product->setData($this->_keyStatusFilters, $statusFilters);

        return $this;
    }

    /**
     * Set only saleable filter
     *
     * @param  Magento_Catalog_Model_Product $product
     * @return Magento_Catalog_Model_Product_Type_Grouped
     */
    public function setSaleableStatus($product)
    {
        $product->setData($this->_keyStatusFilters,
            Mage::getSingleton('Magento_Catalog_Model_Product_Status')->getSaleableStatusIds());
        return $this;
    }

    /**
     * Return all assigned status filters
     *
     * @param Magento_Catalog_Model_Product $product
     * @return array
     */
    public function getStatusFilters($product)
    {
        if (!$product->hasData($this->_keyStatusFilters)) {
            return array(
                Magento_Catalog_Model_Product_Status::STATUS_ENABLED,
                Magento_Catalog_Model_Product_Status::STATUS_DISABLED
            );
        }
        return $product->getData($this->_keyStatusFilters);
    }

    /**
     * Retrieve related products identifiers
     *
     * @param Magento_Catalog_Model_Product $product
     * @return array
     */
    public function getAssociatedProductIds($product)
    {
        if (!$product->hasData($this->_keyAssociatedProductIds)) {
            $associatedProductIds = array();
            foreach ($this->getAssociatedProducts($product) as $item) {
                $associatedProductIds[] = $item->getId();
            }
            $product->setData($this->_keyAssociatedProductIds, $associatedProductIds);
        }
        return $product->getData($this->_keyAssociatedProductIds);
    }

    /**
     * Retrieve collection of associated products
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    public function getAssociatedProductCollection($product)
    {
        $collection = $product->getLinkInstance()->useGroupedLinks()
            ->getProductCollection()
            ->setFlag('require_stock_items', true)
            ->setFlag('product_children', true)
            ->setIsStrongMode();
        $collection->setProduct($product);
        return $collection;
    }

    /**
     * Check is product available for sale
     *
     * @param Magento_Catalog_Model_Product $product
     * @return bool
     */
    public function isSalable($product)
    {
        $salable = parent::isSalable($product);
        if (!is_null($salable)) {
            return $salable;
        }

        $salable = false;
        foreach ($this->getAssociatedProducts($product) as $associatedProduct) {
            $salable = $salable || $associatedProduct->isSalable();
        }
        return $salable;
    }

    /**
     * Save type related data
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_Catalog_Model_Product_Type_Grouped
     */
    public function save($product)
    {
        parent::save($product);
        $product->getLinkInstance()->saveGroupedLinks($product);
        return $this;
    }

    /**
     * Prepare product and its configuration to be added to some products list.
     * Perform standard preparation process and add logic specific to Grouped product type.
     *
     * @param Magento_Object $buyRequest
     * @param Magento_Catalog_Model_Product $product
     * @param string $processMode
     * @return array|string
     */
    protected function _prepareProduct(Magento_Object $buyRequest, $product, $processMode)
    {
        $productsInfo = $buyRequest->getSuperGroup();
        $isStrictProcessMode = $this->_isStrictProcessMode($processMode);

        if (!$isStrictProcessMode || (!empty($productsInfo) && is_array($productsInfo))) {
            $products = array();
            $associatedProductsInfo = array();
            $associatedProducts = $this->getAssociatedProducts($product);
            if ($associatedProducts || !$isStrictProcessMode) {
                foreach ($associatedProducts as $subProduct) {
                    $subProductId = $subProduct->getId();
                    if(isset($productsInfo[$subProductId])) {
                        $qty = $productsInfo[$subProductId];
                        if (!empty($qty) && is_numeric($qty)) {

                            $_result = $subProduct->getTypeInstance()
                                ->_prepareProduct($buyRequest, $subProduct, $processMode);
                            if (is_string($_result) && !is_array($_result)) {
                                return $_result;
                            }

                            if (!isset($_result[0])) {
                                return __('We cannot process the item.');
                            }

                            if ($isStrictProcessMode) {
                                $_result[0]->setCartQty($qty);
                                $_result[0]->addCustomOption('product_type', self::TYPE_CODE, $product);
                                $_result[0]->addCustomOption('info_buyRequest',
                                    serialize(array(
                                        'super_product_config' => array(
                                            'product_type'  => self::TYPE_CODE,
                                            'product_id'    => $product->getId()
                                        )
                                    ))
                                );
                                $products[] = $_result[0];
                            } else {
                                $associatedProductsInfo[] = array($subProductId => $qty);
                                $product->addCustomOption('associated_product_' . $subProductId, $qty);
                            }
                        }
                    }
                }
            }

            if (!$isStrictProcessMode || count($associatedProductsInfo)) {
                $product->addCustomOption('product_type', self::TYPE_CODE, $product);
                $product->addCustomOption('info_buyRequest', serialize($buyRequest->getData()));

                $products[] = $product;
            }

            if (count($products)) {
                return $products;
            }
        }

        return __('Please specify the quantity of product(s).');
    }

    /**
     * Retrieve products divided into groups required to purchase
     * At least one product in each group has to be purchased
     *
     * @param Magento_Catalog_Model_Product $product
     * @return array
     */
    public function getProductsToPurchaseByReqGroups($product)
    {
        return array($this->getAssociatedProducts($product));
    }

    /**
     * Prepare selected qty for grouped product's options
     *
     * @param  Magento_Catalog_Model_Product $product
     * @param  Magento_Object $buyRequest
     * @return array
     */
    public function processBuyRequest($product, $buyRequest)
    {
        $superGroup = $buyRequest->getSuperGroup();
        $superGroup = (is_array($superGroup)) ? array_filter($superGroup, 'intval') : array();

        $options = array('super_group' => $superGroup);

        return $options;
    }

    /**
     * Check that product of this type has weight
     *
     * @return bool
     */
    public function hasWeight()
    {
        return false;
    }

    /**
     * Delete data specific for Grouped product type
     *
     * @param Magento_Catalog_Model_Product $product
     */
    public function deleteTypeSpecificData(Magento_Catalog_Model_Product $product)
    {
    }
}
