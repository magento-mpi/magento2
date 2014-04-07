<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Product\Widget;

/**
 * New products widget
 */
class NewWidget extends \Magento\Catalog\Block\Product\NewProduct implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Display products type - all products
     */
    const DISPLAY_TYPE_ALL_PRODUCTS = 'all_products';

    /**
     * Display products type - new products
     */
    const DISPLAY_TYPE_NEW_PRODUCTS = 'new_products';

    /**
     * Default value whether show pager or not
     */
    const DEFAULT_SHOW_PAGER = false;

    /**
     * Default value for products per page
     */
    const DEFAULT_PRODUCTS_PER_PAGE = 5;

    /**
     * Name of request parameter for page number value
     */
    const PAGE_VAR_NAME = 'np';

    /**
     * Instance of pager block
     *
     * @var \Magento\Catalog\Block\Product\Widget\Html\Pager
     */
    protected $_pager;

    /**
     * Initialize block's cache and template settings
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addPriceBlockType(
            'bundle',
            'Magento\Bundle\Block\Catalog\Product\Price',
            'bundle/catalog/product/price.phtml'
        );
    }

    /**
     * Product collection initialize process
     *
     * @return \Magento\Catalog\Model\Resource\Product\Collection|Object|\Magento\Data\Collection
     */
    protected function _getProductCollection()
    {
        switch ($this->getDisplayType()) {
            case self::DISPLAY_TYPE_NEW_PRODUCTS:
                $collection = parent::_getProductCollection();
                break;
            default:
                $collection = $this->_getRecentlyAddedProductsCollection();
                break;
        }
        return $collection;
    }

    /**
     * Prepare collection for recent product list
     *
     * @return \Magento\Catalog\Model\Resource\Product\Collection|Object|\Magento\Data\Collection
     */
    protected function _getRecentlyAddedProductsCollection()
    {
        /** @var $collection \Magento\Catalog\Model\Resource\Product\Collection */
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToSort('created_at', 'desc')
            ->setPageSize($this->getProductsCount())
            ->setCurPage(1);
        return $collection;
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array_merge(
            parent::getCacheKeyInfo(),
            array(
                $this->getDisplayType(),
                $this->getProductsPerPage(),
                intval($this->getRequest()->getParam(self::PAGE_VAR_NAME))
            )
        );
    }

    /**
     * Retrieve display type for products
     *
     * @return string
     */
    public function getDisplayType()
    {
        if (!$this->hasData('display_type')) {
            $this->setData('display_type', self::DISPLAY_TYPE_ALL_PRODUCTS);
        }
        return $this->getData('display_type');
    }

    /**
     * Retrieve how much products should be displayed
     *
     * @return int
     */
    public function getProductsCount()
    {
        if (!$this->hasData('products_count')) {
            return parent::getProductsCount();
        }
        return $this->getData('products_count');
    }

    /**
     * Retrieve how much products should be displayed
     *
     * @return int
     */
    public function getProductsPerPage()
    {
        if (!$this->hasData('products_per_page')) {
            $this->setData('products_per_page', self::DEFAULT_PRODUCTS_PER_PAGE);
        }
        return $this->getData('products_per_page');
    }

    /**
     * Return flag whether pager need to be shown or not
     *
     * @return bool
     */
    public function showPager()
    {
        if (!$this->hasData('show_pager')) {
            $this->setData('show_pager', self::DEFAULT_SHOW_PAGER);
        }
        return (bool)$this->getData('show_pager');
    }

    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        if ($this->showPager()) {
            if (!$this->_pager) {
                $this->_pager = $this->getLayout()->createBlock(
                    'Magento\Catalog\Block\Product\Widget\Html\Pager',
                    'widget.new.product.list.pager'
                );

                $this->_pager->setUseContainer(true)
                    ->setShowAmounts(true)
                    ->setShowPerPage(false)
                    ->setPageVarName(self::PAGE_VAR_NAME)
                    ->setLimit($this->getProductsPerPage())
                    ->setTotalLimit($this->getProductsCount())
                    ->setCollection($this->getProductCollection());
            }
            if ($this->_pager instanceof \Magento\View\Element\AbstractBlock) {
                return $this->_pager->toHtml();
            }
        }
        return '';
    }

    /**
     * Return HTML block with price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $type
     * @param string $renderZone
     * @return string
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $type = null,
        $renderZone = \Magento\Pricing\Render::ZONE_ITEM_LIST
    ) {
        /** @var \Magento\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPriceInterface::PRICE_TYPE_FINAL,
                $product,
                [
                    'price_id'              => 'old-price-' . $product->getId() . '-' . $type,
                    'display_minimal_price' => true,
                    'include_container'     => true,
                    'zone'                  => $renderZone
                ]
            );
        }
        return $price;
    }
}
