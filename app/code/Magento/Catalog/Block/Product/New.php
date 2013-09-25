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
 * New products block
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Catalog_Block_Product_New extends Magento_Catalog_Block_Product_Abstract
{
    /**
     * Default value for products count that will be shown
     */
    const DEFAULT_PRODUCTS_COUNT = 10;

    /**
     * Products count
     *
     * @var null
     */
    protected $_productsCount;

    /**
     * Customer session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * Catalog product visibility
     *
     * @var Magento_Catalog_Model_Product_Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Locale
     *
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * Product collection factory
     *
     * @var Magento_Catalog_Model_Resource_Product_CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Model_Config $catalogConfig
     * @param Magento_Catalog_Model_Resource_Product_CollectionFactory $productCollectionFactory
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Catalog_Model_Product_Visibility $catalogProductVisibility
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Model_Config $catalogConfig,
        Magento_Catalog_Model_Resource_Product_CollectionFactory $productCollectionFactory,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Catalog_Model_Product_Visibility $catalogProductVisibility,
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_locale = $locale;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_customerSession = $customerSession;
        parent::__construct($storeManager, $catalogConfig, $coreRegistry, $taxData, $catalogData, $coreData,
            $context, $data);
    }

    /**
     * Initialize block's cache
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addColumnCountLayoutDepend('empty', 6)
            ->addColumnCountLayoutDepend('one_column', 5)
            ->addColumnCountLayoutDepend('two_columns_left', 4)
            ->addColumnCountLayoutDepend('two_columns_right', 4)
            ->addColumnCountLayoutDepend('three_columns', 3);

        $this->addData(array(
            'cache_lifetime'    => 86400,
            'cache_tags'        => array(Magento_Catalog_Model_Product::CACHE_TAG),
        ));
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
           'CATALOG_PRODUCT_NEW',
           $this->_storeManager->getStore()->getId(),
           $this->_design->getDesignTheme()->getId(),
           $this->_customerSession->getCustomerGroupId(),
           'template' => $this->getTemplate(),
           $this->getProductsCount()
        );
    }

    /**
     * Prepare and return product collection
     *
     * @return Magento_Catalog_Model_Resource_Product_Collection|Object|Magento_Data_Collection
     */
    protected function _getProductCollection()
    {
        $todayStartOfDayDate  = $this->_locale->date()
            ->setTime('00:00:00')
            ->toString(Magento_Date::DATETIME_INTERNAL_FORMAT);

        $todayEndOfDayDate  = $this->_locale->date()
            ->setTime('23:59:59')
            ->toString(Magento_Date::DATETIME_INTERNAL_FORMAT);

        /** @var $collection Magento_Catalog_Model_Resource_Product_Collection */
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());


        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToFilter('news_from_date', array('or'=> array(
                0 => array('date' => true, 'to' => $todayEndOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter('news_to_date', array('or'=> array(
                0 => array('date' => true, 'from' => $todayStartOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter(
                array(
                    array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
                    array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
                    )
              )
            ->addAttributeToSort('news_from_date', 'desc')
            ->setPageSize($this->getProductsCount())
            ->setCurPage(1)
        ;

        return $collection;
    }

    /**
     * Prepare collection with new products
     *
     * @return Magento_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $this->setProductCollection($this->_getProductCollection());
        return parent::_beforeToHtml();
    }

    /**
     * Set how much product should be displayed at once.
     *
     * @param $count
     * @return Magento_Catalog_Block_Product_New
     */
    public function setProductsCount($count)
    {
        $this->_productsCount = $count;
        return $this;
    }

    /**
     * Get how much products should be displayed at once.
     *
     * @return int
     */
    public function getProductsCount()
    {
        if (null === $this->_productsCount) {
            $this->_productsCount = self::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->_productsCount;
    }
}
