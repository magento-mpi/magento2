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
 * New products widget
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Block_Product_Widget_New extends Magento_Catalog_Block_Product_New
    implements Magento_Widget_Block_Interface
{
    /**
     * Display products type
     */
    const DISPLAY_TYPE_ALL_PRODUCTS         = 'all_products';
    const DISPLAY_TYPE_NEW_PRODUCTS         = 'new_products';

    /**
     * Default value whether show pager or not
     */
    const DEFAULT_SHOW_PAGER                = false;

    /**
     * Default value for products per page
     */
    const DEFAULT_PRODUCTS_PER_PAGE         = 5;

    /**
     * Name of request parameter for page number value
     */
    const PAGE_VAR_NAME                     = 'np';

    /**
     * Instance of pager block
     *
     * @var Magento_Catalog_Block_Product_Widget_Html_Pager
     */
    protected $_pager;

    /**
     * Initialize block's cache and template settings
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addPriceBlockType(
            'bundle',
            'Magento_Bundle_Block_Catalog_Product_Price',
            'bundle/catalog/product/price.phtml'
        );
    }

    /**
     * Product collection initialize process
     *
     * @return Magento_Catalog_Model_Resource_Product_Collection|Object|\Magento\Data\Collection
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
     * @return Magento_Catalog_Model_Resource_Product_Collection|Object|\Magento\Data\Collection
     */
    protected function _getRecentlyAddedProductsCollection()
    {
        /** @var $collection Magento_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getResourceModel('Magento_Catalog_Model_Resource_Product_Collection');
        $collection->setVisibility(Mage::getSingleton('Magento_Catalog_Model_Product_Visibility')->getVisibleInCatalogIds());

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToSort('created_at', 'desc')
            ->setPageSize($this->getProductsCount())
            ->setCurPage(1)
        ;
        return $collection;
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array_merge(parent::getCacheKeyInfo(), array(
            $this->getDisplayType(),
            $this->getProductsPerPage(),
            intval($this->getRequest()->getParam(self::PAGE_VAR_NAME))
        ));
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
                $this->_pager = $this->getLayout()
                    ->createBlock('Magento_Catalog_Block_Product_Widget_Html_Pager', 'widget.new.product.list.pager');

                $this->_pager->setUseContainer(true)
                    ->setShowAmounts(true)
                    ->setShowPerPage(false)
                    ->setPageVarName(self::PAGE_VAR_NAME)
                    ->setLimit($this->getProductsPerPage())
                    ->setTotalLimit($this->getProductsCount())
                    ->setCollection($this->getProductCollection());
            }
            if ($this->_pager instanceof Magento_Core_Block_Abstract) {
                return $this->_pager->toHtml();
            }
        }
        return '';
    }
}
