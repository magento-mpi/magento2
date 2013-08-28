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
 * Block for Catalog Category URL rewrites
 *
 * @method Magento_Catalog_Model_Category getCategory()
 * @method Magento_Adminhtml_Block_Urlrewrite_Catalog_Category_Edit
 *    setCategory(Magento_Catalog_Model_Category $category)
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Urlrewrite_Catalog_Category_Edit
    extends Magento_Adminhtml_Block_Urlrewrite_Edit
{
    /**
     * Prepare layout for URL rewrite creating for category
     */
    protected function _prepareLayoutFeatures()
    {
        if ($this->_getUrlRewrite()->getId()) {
            $this->_headerText = __('Edit URL Rewrite for a Category');
        } else {
            $this->_headerText = __('Add URL Rewrite for a Category');
        }

        if ($this->_getCategory()->getId()) {
            $this->_addCategoryLinkBlock();
            $this->_addEditFormBlock();
            $this->_updateBackButtonLink($this->_adminhtmlData->getUrl('*/*/edit') . 'category');
        } else {
            $this->_addUrlRewriteSelectorBlock();
            $this->_addCategoryTreeBlock();
        }
    }

    /**
     * Get or create new instance of category
     *
     * @return Magento_Catalog_Model_Product
     */
    private function _getCategory()
    {
        if (!$this->hasData('category')) {
            $this->setCategory(Mage::getModel('Magento_Catalog_Model_Category'));
        }
        return $this->getCategory();
    }

    /**
     * Add child category link block
     */
    private function _addCategoryLinkBlock()
    {
        $this->addChild('category_link', 'Magento_Adminhtml_Block_Urlrewrite_Link', array(
            'item_url'  => $this->_adminhtmlData->getUrl('*/*/*') . 'category',
            'item_name' => $this->_getCategory()->getName(),
            'label'     => __('Category:')
        ));
    }

    /**
     * Add child category tree block
     */
    private function _addCategoryTreeBlock()
    {
        $this->addChild('categories_tree', 'Magento_Adminhtml_Block_Urlrewrite_Catalog_Category_Tree');
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
                'category'    => $this->_getCategory(),
                'url_rewrite' => $this->_getUrlRewrite()
            )
        ));
    }
}
