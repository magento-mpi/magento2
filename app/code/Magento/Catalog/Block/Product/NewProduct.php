<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Product;

/**
 * New products block
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class NewProduct extends \Magento\Catalog\Block\Product\AbstractProduct implements
    \Magento\View\Block\IdentityInterface
{
    /**
     * Default value for products count that will be shown
     */
    const DEFAULT_PRODUCTS_COUNT = 10;

    /**
     * Products count
     *
     * @var int
     */
    protected $_productsCount;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\Resource\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @param Context $context
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     * @param array $priceBlockTypes
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = array(),
        array $priceBlockTypes = array()
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->httpContext = $httpContext;
        parent::__construct(
            $context,
            $data,
            $priceBlockTypes
        );
        $this->_isScopePrivate = true;
    }

    /**
     * Initialize block's cache
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addColumnCountLayoutDepend(
            'empty',
            6
        )->addColumnCountLayoutDepend(
            'one_column',
            5
        )->addColumnCountLayoutDepend(
            'two_columns_left',
            4
        )->addColumnCountLayoutDepend(
            'two_columns_right',
            4
        )->addColumnCountLayoutDepend(
            'three_columns',
            3
        );

        $this->addData(
            array('cache_lifetime' => 86400, 'cache_tags' => array(\Magento\Catalog\Model\Product::CACHE_TAG))
        );
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
           $this->httpContext->getValue(\Magento\Customer\Helper\Data::CONTEXT_GROUP),
           'template' => $this->getTemplate(),
           $this->getProductsCount()
        );
    }

    /**
     * Prepare and return product collection
     *
     * @return \Magento\Catalog\Model\Resource\Product\Collection|Object|\Magento\Data\Collection
     */
    protected function _getProductCollection()
    {
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(
            '00:00:00'
        )->toString(
            \Magento\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT
        );

        $todayEndOfDayDate = $this->_localeDate->date()->setTime(
            '23:59:59'
        )->toString(
            \Magento\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT
        );

        /** @var $collection \Magento\Catalog\Model\Resource\Product\Collection */
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());


        $collection = $this->_addProductAttributesAndPrices(
            $collection
        )->addStoreFilter()->addAttributeToFilter(
            'news_from_date',
            array(
                'or' => array(
                    0 => array('date' => true, 'to' => $todayEndOfDayDate),
                    1 => array('is' => new \Zend_Db_Expr('null'))
                )
            ),
            'left'
        )->addAttributeToFilter(
            'news_to_date',
            array(
                'or' => array(
                    0 => array('date' => true, 'from' => $todayStartOfDayDate),
                    1 => array('is' => new \Zend_Db_Expr('null'))
                )
            ),
            'left'
        )->addAttributeToFilter(
            array(
                array('attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')),
                array('attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null'))
            )
        )->addAttributeToSort(
            'news_from_date',
            'desc'
        )->setPageSize(
            $this->getProductsCount()
        )->setCurPage(
            1
        );

        return $collection;
    }

    /**
     * Prepare collection with new products
     *
     * @return \Magento\View\Element\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        $this->setProductCollection($this->_getProductCollection());
        return parent::_beforeToHtml();
    }

    /**
     * Set how much product should be displayed at once.
     *
     * @param int $count
     * @return $this
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

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return array(\Magento\Catalog\Model\Product::CACHE_TAG);
    }
}
