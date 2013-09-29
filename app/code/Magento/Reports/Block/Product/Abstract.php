<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reports Recently Products Abstract Block
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Reports_Block_Product_Abstract extends Magento_Catalog_Block_Product_Abstract
{
    /**
     * Product Index model type
     *
     * @var string
     */
    protected $_indexType;

    /**
     * Product Index Collection
     *
     * @var Magento_Reports_Model_Resource_Product_Index_Collection_Abstract
     */
    protected $_collection;

    /**
     * @var Magento_Catalog_Model_Config
     */
    protected $_catalogConfig;

    /**
     * @var Magento_Catalog_Model_Product_Visibility
     */
    protected $_productVisibility;

    /**
     * @var Magento_Reports_Model_Product_Index_Factory
     */
    protected $_indexFactory;

    /**
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Catalog_Model_Config $catalogConfig
     * @param Magento_Catalog_Model_Product_Visibility $productVisibility
     * @param Magento_Reports_Model_Product_Index_Factory $indexFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Catalog_Model_Config $catalogConfig,
        Magento_Catalog_Model_Product_Visibility $productVisibility,
        Magento_Reports_Model_Product_Index_Factory $indexFactory,
        array $data = array()
    ) {
        parent::__construct($coreRegistry, $taxData, $catalogData, $coreData, $context, $data);
        $this->_catalogConfig = $catalogConfig;
        $this->_productVisibility = $productVisibility;
        $this->_indexFactory = $indexFactory;
    }

    /**
     * Retrieve page size
     *
     * @return int
     */
    public function getPageSize()
    {
        if ($this->hasData('page_size')) {
            return $this->getData('page_size');
        }
        return 5;
    }

    /**
     * Retrieve product ids, that must not be included in collection
     *
     * @return array
     */
    protected function _getProductsToSkip()
    {
        return array();
    }

    /**
     * Retrieve Product Index model instance
     *
     * @return Magento_Reports_Model_Product_Index_Abstract
     */
    protected function _getModel()
    {
        try {
            $model = $this->_indexFactory->get($this->_indexType);
        } catch (InvalidArgumentException $e) {
            new Magento_Core_Exception(__('Index type is not valid'));
        }

        return $model;
    }

    /**
     * Public method for retrieve Product Index model
     *
     * @return Magento_Reports_Model_Product_Index_Abstract
     */
    public function getModel()
    {
        return $this->_getModel();
    }

    /**
     * Retrieve Index Product Collection
     *
     * @return Magento_Reports_Model_Resource_Product_Index_Collection_Abstract
     */
    public function getItemsCollection()
    {
        if (is_null($this->_collection)) {
            $attributes = $this->_catalogConfig->getProductAttributes();

            $this->_collection = $this->_getModel()
                ->getCollection()
                ->addAttributeToSelect($attributes);

                if ($this->getCustomerId()) {
                    $this->_collection->setCustomerId($this->getCustomerId());
                }

                $this->_collection->excludeProductIds($this->_getModel()->getExcludeProductIds())
                    ->addUrlRewrite()
                    ->setPageSize($this->getPageSize())
                    ->setCurPage(1);

            /* Price data is added to consider item stock status using price index */
            $this->_collection->addPriceData();

            $ids = $this->getProductIds();
            if (empty($ids)) {
                $this->_collection->addIndexFilter();
            } else {
                $this->_collection->addFilterByIds($ids);
            }
            $this->_collection->setAddedAtOrder()
                ->setVisibility($this->_productVisibility->getVisibleInSiteIds());
        }

        return $this->_collection;
    }

    /**
     * Retrieve count of product index items
     *
     * @return int
     */
    public function getCount()
    {
        if (!$this->_getModel()->getCount()) {
            return 0;
        }
        return $this->getItemsCollection()->count();
    }
}
