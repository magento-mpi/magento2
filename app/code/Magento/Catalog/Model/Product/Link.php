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
 * Catalog product link model
 *
 * @method Magento_Catalog_Model_Resource_Product_Link _getResource()
 * @method Magento_Catalog_Model_Resource_Product_Link getResource()
 * @method int getProductId()
 * @method Magento_Catalog_Model_Product_Link setProductId(int $value)
 * @method int getLinkedProductId()
 * @method Magento_Catalog_Model_Product_Link setLinkedProductId(int $value)
 * @method int getLinkTypeId()
 * @method Magento_Catalog_Model_Product_Link setLinkTypeId(int $value)
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Link extends Magento_Core_Model_Abstract
{
    const LINK_TYPE_RELATED     = 1;
    const LINK_TYPE_GROUPED     = 3;
    const LINK_TYPE_UPSELL      = 4;
    const LINK_TYPE_CROSSSELL   = 5;

    protected $_attributeCollection = null;

    /**
     * Product collection factory
     *
     * @var Magento_Catalog_Model_Resource_Product_Link_Product_CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Link collection factory
     *
     * @var Magento_Catalog_Model_Resource_Product_Link_CollectionFactory
     */
    protected $_linkCollectionFactory;

    /**
     * Construct
     *
     * @param Magento_Catalog_Model_Resource_Product_Link_CollectionFactory $linkCollectionFactory
     * @param Magento_Catalog_Model_Resource_Product_Link_Product_CollectionFactory $productCollectionFactory
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_Resource_Product_Link_CollectionFactory $linkCollectionFactory,
        Magento_Catalog_Model_Resource_Product_Link_Product_CollectionFactory $productCollectionFactory,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_linkCollectionFactory = $linkCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('Magento_Catalog_Model_Resource_Product_Link');
    }

    public function useRelatedLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_RELATED);
        return $this;
    }

    public function useGroupedLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_GROUPED);
        return $this;
    }

    public function useUpSellLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_UPSELL);
        return $this;
    }

    /**
     * @return Magento_Catalog_Model_Product_Link
     */
    public function useCrossSellLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_CROSSSELL);
        return $this;
    }

    /**
     * Retrieve table name for attribute type
     *
     * @param   string $type
     * @return  string
     */
    public function getAttributeTypeTable($type)
    {
        return $this->_getResource()->getAttributeTypeTable($type);
    }

    /**
     * Retrieve linked product collection
     */
    public function getProductCollection()
    {
        $collection = $this->_productCollectionFactory->create()
            ->setLinkModel($this);
        return $collection;
    }

    /**
     * Retrieve link collection
     */
    public function getLinkCollection()
    {
        $collection = $this->_linkCollectionFactory->create()
            ->setLinkModel($this);
        return $collection;
    }

    public function getAttributes($type=null)
    {
        if (is_null($type)) {
            $type = $this->getLinkTypeId();
        }
        return $this->_getResource()->getAttributesByType($type);
    }

    /**
     * Save data for product relations
     *
     * @param   Magento_Catalog_Model_Product $product
     * @return  Magento_Catalog_Model_Product_Link
     */
    public function saveProductRelations($product)
    {
        $data = $product->getRelatedLinkData();
        if (!is_null($data)) {
            $this->_getResource()->saveProductLinks($product, $data, self::LINK_TYPE_RELATED);
        }
        $data = $product->getUpSellLinkData();
        if (!is_null($data)) {
            $this->_getResource()->saveProductLinks($product, $data, self::LINK_TYPE_UPSELL);
        }
        $data = $product->getCrossSellLinkData();
        if (!is_null($data)) {
            $this->_getResource()->saveProductLinks($product, $data, self::LINK_TYPE_CROSSSELL);
        }
        return $this;
    }

    /**
     * Save grouped product relation links
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_Catalog_Model_Product_Link
     */
    public function saveGroupedLinks($product)
    {
        $data = $product->getGroupedLinkData();
        if (!is_null($data)) {
            $this->_getResource()->saveGroupedLinks($product, $data, self::LINK_TYPE_GROUPED);
        }
        return $this;
    }
}
