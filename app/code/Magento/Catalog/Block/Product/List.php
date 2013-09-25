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
 * Product list
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Block_Product_List extends Magento_Catalog_Block_Product_Abstract
{
    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'Magento_Catalog_Block_Product_List_Toolbar';

    /**
     * Product Collection
     *
     * @var Magento_Eav_Model_Entity_Collection_Abstract
     */
    protected $_productCollection;

    /**
     * Catalog layer
     *
     * @var Magento_Catalog_Model_Layer
     */
    protected $_catalogLayer;

    /**
     * Category factory
     *
     * @var Magento_Catalog_Model_CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * Construct
     *
     * @param Magento_Catalog_Model_CategoryFactory $categoryFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Model_Config $catalogConfig
     * @param Magento_Catalog_Model_Layer $catalogLayer
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_CategoryFactory $categoryFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Model_Config $catalogConfig,
        Magento_Catalog_Model_Layer $catalogLayer,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_catalogLayer = $catalogLayer;
        parent::__construct($storeManager, $catalogConfig, $coreRegistry, $taxData, $catalogData, $coreData,
            $context, $data);
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Magento_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $layer = $this->getLayer();
            /* @var $layer Magento_Catalog_Model_Layer */
            if ($this->getShowRootCategory()) {
                $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if ($this->_coreRegistry->registry('product')) {
                // get collection of categories this product is associated with
                $categories = $this->_coreRegistry->registry('product')->getCategoryCollection()
                    ->setPage(1, 1)
                    ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                /** @var Magento_Catalog_Model_Category $category */
                $category = $this->_categoryFactory->create()->load($this->getCategoryId());
                if ($category->getId()) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                }
            }
            $this->_productCollection = $layer->getProductCollection();

            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
        }

        return $this->_productCollection;
    }

    /**
     * Get catalog layer model
     *
     * @return Magento_Catalog_Model_Layer
     */
    public function getLayer()
    {
        $layer = $this->_coreRegistry->registry('current_layer');
        if ($layer) {
            return $layer;
        }
        return $this->_catalogLayer;
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Magento_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChildBlock('toolbar')->getCurrentMode();
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->_getProductCollection();

        // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }
        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $this->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        $this->_eventManager->dispatch('catalog_block_product_list_collection', array(
            'collection' => $this->_getProductCollection()
        ));

        $this->_getProductCollection()->load();

        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Toolbar block
     *
     * @return Magento_Catalog_Block_Product_List_Toolbar
     */
    public function getToolbarBlock()
    {
        $blockName = $this->getToolbarBlockName();
        if ($blockName) {
            $block = $this->getLayout()->getBlock($blockName);
            if ($block) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, uniqid(microtime()));
        return $block;
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    public function setCollection($collection)
    {
        $this->_productCollection = $collection;
        return $this;
    }

    public function addAttribute($code)
    {
        $this->_getProductCollection()->addAttributeToSelect($code);
        return $this;
    }

    public function getPriceBlockTemplate()
    {
        return $this->_getData('price_block_template');
    }

    /**
     * Retrieve Catalog Config object
     *
     * @return Magento_Catalog_Model_Config
     */
    protected function _getConfig()
    {
        return $this->_catalogConfig;
    }

    /**
     * Prepare Sort By fields from Category Data
     *
     * @param Magento_Catalog_Model_Category $category
     * @return Magento_Catalog_Block_Product_List
     */
    public function prepareSortableFieldsByCategory($category) {
        if (!$this->getAvailableOrders()) {
            $this->setAvailableOrders($category->getAvailableSortByOptions());
        }
        $availableOrders = $this->getAvailableOrders();
        if (!$this->getSortBy()) {
            $categorySortBy = $category->getDefaultSortBy();
            if ($categorySortBy) {
                if (!$availableOrders) {
                    $availableOrders = $this->_getConfig()->getAttributeUsedForSortByArray();
                }
                if (isset($availableOrders[$categorySortBy])) {
                    $this->setSortBy($categorySortBy);
                }
            }
        }

        return $this;
    }
}
