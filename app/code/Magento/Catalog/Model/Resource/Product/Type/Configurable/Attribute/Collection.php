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
 * Catalog Configurable Product Attribute Collection
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Configurable attributes label table name
     *
     * @var string
     */
    protected $_labelTable;

    /**
     * Configurable attributes price table name
     *
     * @var string
     */
    protected $_priceTable;

    /**
     * Product instance
     *
     * @var Magento_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Catalog data
     *
     * @var Magento_Catalog_Helper_Data
     */
    protected $_catalogData = null;

    /**
     * Collection constructor
     *
     *
     *
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        $this->_catalogData = $catalogData;
        parent::__construct($fetchStrategy, $resource);
    }

    /**
     * Initialize connection and define table names
     *
     */
    protected function _construct()
    {
        $this->_init(
            'Magento_Catalog_Model_Product_Type_Configurable_Attribute',
            'Magento_Catalog_Model_Resource_Product_Type_Configurable_Attribute'
        );
        $this->_labelTable = $this->getTable('catalog_product_super_attribute_label');
        $this->_priceTable = $this->getTable('catalog_product_super_attribute_pricing');
    }

    /**
     * Retrieve catalog helper
     *
     * @return Magento_Catalog_Helper_Data
     */
    public function getHelper()
    {
        return $this->_catalogData;
    }

    /**
     * Set Product filter (Configurable)
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
     */
    public function setProductFilter($product)
    {
        $this->_product = $product;
        return $this->addFieldToFilter('product_id', $product->getId());
    }

    /**
     * Get product type
     *
     * @return Magento_Catalog_Model_Product_Type_Configurable
     */
    private function getProductType()
    {
        return Mage::getSingleton('Magento_Catalog_Model_Product_Type_Configurable');
    }

    /**
     * Set order collection by Position
     *
     * @param string $dir
     * @return Magento_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
     */
    public function orderByPosition($dir = self::SORT_ORDER_ASC)
    {
        $this->setOrder('position ',  $dir);
        return $this;
    }

    /**
     * Retrieve Store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        return (int)$this->_product->getStoreId();
    }

    /**
     * After load collection process
     *
     * @return Magento_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        Magento_Profiler::start('TTT1:'.__METHOD__, array('group' => 'TTT1', 'method' => __METHOD__));
        $this->_addProductAttributes();
        Magento_Profiler::stop('TTT1:'.__METHOD__);
        Magento_Profiler::start('TTT2:'.__METHOD__, array('group' => 'TTT2', 'method' => __METHOD__));
        $this->_addAssociatedProductFilters();
        Magento_Profiler::stop('TTT2:'.__METHOD__);
        Magento_Profiler::start('TTT3:'.__METHOD__, array('group' => 'TTT3', 'method' => __METHOD__));
        $this->_loadLabels();
        Magento_Profiler::stop('TTT3:'.__METHOD__);
        Magento_Profiler::start('TTT4:'.__METHOD__, array('group' => 'TTT4', 'method' => __METHOD__));
        $this->_loadPrices();
        Magento_Profiler::stop('TTT4:'.__METHOD__);
        return $this;
    }

    /**
     * Add product attributes to collection items
     *
     * @return Magento_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
     */
    protected function _addProductAttributes()
    {
        foreach ($this->_items as $item) {
            $productAttribute = $this->getProductType()
                ->getAttributeById($item->getAttributeId(), $this->getProduct());
            $item->setProductAttribute($productAttribute);
        }
        return $this;
    }

    /**
     * Add Associated Product Filters (From Product Type Instance)
     *
     * @return Magento_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
     */
    public function _addAssociatedProductFilters()
    {
        $this->getProductType()->getUsedProducts(
            $this->getProduct(), $this->getColumnValues('attribute_id') // Filter associated products
        );
        return $this;
    }

    /**
     * Load attribute labels
     *
     * @return Magento_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
     */
    protected function _loadLabels()
    {
        if ($this->count()) {
            $useDefaultCheck = $this->getConnection()->getCheckSql(
                'store.use_default IS NULL',
                'def.use_default',
                'store.use_default'
            );

            $labelCheck = $this->getConnection()->getCheckSql(
                'store.value IS NULL',
                'def.value',
                'store.value'
            );

            $select = $this->getConnection()->select()
                ->from(array('def' => $this->_labelTable))
                ->joinLeft(
                    array('store' => $this->_labelTable),
                    $this->getConnection()->quoteInto(
                        'store.product_super_attribute_id = def.product_super_attribute_id AND store.store_id = ?',
                        $this->getStoreId()
                    ),
                    array(
                        'use_default' => $useDefaultCheck,
                        'label' => $labelCheck
                    ))
                ->where('def.product_super_attribute_id IN (?)', array_keys($this->_items))
                ->where('def.store_id = ?', 0);

                $result = $this->getConnection()->fetchAll($select);
                foreach ($result as $data) {
                    $this->getItemById($data['product_super_attribute_id'])->setLabel($data['label']);
                    $this->getItemById($data['product_super_attribute_id'])->setUseDefault($data['use_default']);
                }
        }
        return $this;
    }

    /**
     * Load attribute prices information
     *
     * @return Magento_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
     */
    protected function _loadPrices()
    {
        if ($this->count()) {
            $pricings = array(
                0 => array()
            );

            if ($this->getHelper()->isPriceGlobal()) {
                $websiteId = 0;
            } else {
                $websiteId = (int)Mage::app()->getStore($this->getStoreId())->getWebsiteId();
                $pricing[$websiteId] = array();
            }

            $select = $this->getConnection()->select()
                ->from(array('price' => $this->_priceTable))
                ->where('price.product_super_attribute_id IN (?)', array_keys($this->_items));

            if ($websiteId > 0) {
                $select->where('price.website_id IN(?)', array(0, $websiteId));
            } else {
                $select->where('price.website_id = ?', 0);
            }

            $query = $this->getConnection()->query($select);

            while ($row = $query->fetch()) {
                $pricings[(int)$row['website_id']][] = $row;
            }

            $values = array();
            $usedProducts = $this->getProductType()->getUsedProducts($this->getProduct());
            if ($usedProducts) {
                foreach ($this->_items as $item) {
                    $productAttribute = $item->getProductAttribute();
                    if (!($productAttribute instanceof Magento_Eav_Model_Entity_Attribute_Abstract)) {
                        continue;
                    }
                    $itemId = $item->getId();
                    $options = $productAttribute->getFrontend()->getSelectOptions();
                    foreach ($options as $option) {
                        foreach ($usedProducts as $associatedProduct) {
                            $attributeCodeValue = $associatedProduct->getData($productAttribute->getAttributeCode());
                            if (!empty($option['value']) && $option['value'] == $attributeCodeValue) {
                                // If option available in associated product
                                if (!isset($values[$item->getId() . ':' . $option['value']])) {
                                    $values[$itemId . ':' . $option['value']] = array(
                                        'product_super_attribute_id' => $itemId,
                                        'value_index'                => $option['value'],
                                        'label'                      => $option['label'],
                                        'default_label'              => $option['label'],
                                        'store_label'                => $option['label'],
                                        'is_percent'                 => 0,
                                        'pricing_value'              => null,
                                        'use_default_value'          => true
                                    );
                                }
                            }
                        }
                    }
                }
            }

            foreach ($pricings[0] as $pricing) {
                // Addding pricing to options
                $valueKey = $pricing['product_super_attribute_id'] . ':' . $pricing['value_index'];
                if (isset($values[$valueKey])) {
                    $values[$valueKey]['pricing_value']     = $pricing['pricing_value'];
                    $values[$valueKey]['is_percent']        = $pricing['is_percent'];
                    $values[$valueKey]['value_id']          = $pricing['value_id'];
                    $values[$valueKey]['use_default_value'] = true;
                }
            }

            if ($websiteId && isset($pricings[$websiteId])) {
                foreach ($pricings[$websiteId] as $pricing) {
                    $valueKey = $pricing['product_super_attribute_id'] . ':' . $pricing['value_index'];
                    if (isset($values[$valueKey])) {
                        $values[$valueKey]['pricing_value']     = $pricing['pricing_value'];
                        $values[$valueKey]['is_percent']        = $pricing['is_percent'];
                        $values[$valueKey]['value_id']          = $pricing['value_id'];
                        $values[$valueKey]['use_default_value'] = false;
                    }
                }
            }

            foreach ($values as $data) {
                $this->getItemById($data['product_super_attribute_id'])->addPrice($data);
            }
        }
        return $this;
    }

    /**
     * Retrieve product instance
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_product;
    }
}
