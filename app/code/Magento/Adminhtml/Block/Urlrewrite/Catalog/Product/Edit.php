<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block for Catalog Category URL rewrites editing
 *
 * @method Magento_Catalog_Model_Category getCategory()
 * @method Magento_Adminhtml_Block_Urlrewrite_Catalog_Product_Edit setCategory(Magento_Catalog_Model_Category $category)
 * @method Magento_Catalog_Model_Product getProduct()
 * @method Magento_Adminhtml_Block_Urlrewrite_Catalog_Product_Edit setProduct(Magento_Catalog_Model_Product $product)
 * @method bool getIsCategoryMode()
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Urlrewrite_Catalog_Product_Edit extends Magento_Adminhtml_Block_Urlrewrite_Edit
{
    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Magento_Catalog_Model_CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Catalog_Model_CategoryFactory $categoryFactory
     * @param Magento_Core_Model_Url_RewriteFactory $rewriteFactory
     * @param Magento_Backend_Helper_Data $adminhtmlData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Catalog_Model_CategoryFactory $categoryFactory,
        Magento_Core_Model_Url_RewriteFactory $rewriteFactory,
        Magento_Backend_Helper_Data $adminhtmlData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_productFactory = $productFactory;
        parent::__construct($rewriteFactory, $adminhtmlData, $coreData, $context, $data);
    }

    /**
     * Prepare layout for URL rewrite creating for product
     */
    protected function _prepareLayoutFeatures()
    {
        if ($this->_getUrlRewrite()->getId()) {
            $this->_headerText = __('Edit URL Rewrite for a Product');
        } else {
            $this->_headerText = __('Add URL Rewrite for a Product');
        }

        if ($this->_getProduct()->getId()) {
            $this->_addProductLinkBlock($this->_getProduct());
        }

        if ($this->_getCategory()->getId()) {
            $this->_addCategoryLinkBlock();
        }

        if ($this->_getProduct()->getId()) {
            if ($this->_getCategory()->getId() || !$this->getIsCategoryMode()) {
                $this->_addEditFormBlock();
                $this->_updateBackButtonLink(
                    $this->_adminhtmlData
                        ->getUrl('*/*/edit', array('product' => $this->_getProduct()->getId())) . 'category'
                );
            } else {
                // categories selector & skip categories button
                $this->_addCategoriesTreeBlock();
                $this->_addSkipCategoriesBlock();
                $this->_updateBackButtonLink($this->_adminhtmlData->getUrl('*/*/edit') . 'product');
            }
        } else {
            $this->_addUrlRewriteSelectorBlock();
            $this->_addProductsGridBlock();
        }
    }

    /**
     * Get or create new instance of product
     *
     * @return Magento_Catalog_Model_Product
     */
    private function _getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setProduct($this->_productFactory->create());
        }
        return $this->getProduct();
    }

    /**
     * Get or create new instance of category
     *
     * @return Magento_Catalog_Model_Category
     */
    private function _getCategory()
    {
        if (!$this->hasData('category')) {
            $this->setCategory($this->_categoryFactory->create());
        }
        return $this->getCategory();
    }

    /**
     * Add child product link block
     */
    private function _addProductLinkBlock()
    {
        $this->addChild('product_link', 'Magento_Adminhtml_Block_Urlrewrite_Link', array(
            'item_url'  => $this->_adminhtmlData->getUrl('*/*/*') . 'product',
            'item_name' => $this->_getProduct()->getName(),
            'label'     => __('Product:')
        ));
    }

    /**
     * Add child category link block
     */
    private function _addCategoryLinkBlock()
    {
        $this->addChild('category_link', 'Magento_Adminhtml_Block_Urlrewrite_Link', array(
            'item_url'  => $this->_adminhtmlData
                ->getUrl('*/*/*', array('product' => $this->_getProduct()->getId())) . 'category',
            'item_name' => $this->_getCategory()->getName(),
            'label'     => __('Category:')
        ));
    }

    /**
     * Add child products grid block
     */
    private function _addProductsGridBlock()
    {
        $this->addChild('products_grid', 'Magento_Adminhtml_Block_Urlrewrite_Catalog_Product_Grid');
    }

    /**
     * Add child Categories Tree block
     */
    private function _addCategoriesTreeBlock()
    {
        $this->addChild('categories_tree', 'Magento_Adminhtml_Block_Urlrewrite_Catalog_Category_Tree');
    }

    /**
     * Add child Skip Categories block
     */
    private function _addSkipCategoriesBlock()
    {
        $this->addChild('skip_categories', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label' => __('Skip Category Selection'),
            'onclick' => 'window.location = \''
                . $this->_adminhtmlData->getUrl('*/*/*', array('product' => $this->_getProduct()->getId())) . '\'',
            'class' => 'save',
            'level' => -1
        ));
    }

    /**
     * Creates edit form block
     *
     * @return Magento_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form
     */
    protected function _createEditFormBlock()
    {
        return $this->getLayout()->createBlock('Magento_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form', '', array(
            'data' => array(
                'product'     => $this->_getProduct(),
                'category'    => $this->_getCategory(),
                'url_rewrite' => $this->_getUrlRewrite()
            )
        ));
    }
}
