<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block for Catalog Category URL rewrites
 *
 * @method Mage_Catalog_Model_Category getCategory()
 * @method Mage_Adminhtml_Block_Urlrewrite_Catalog_Category_Edit setCategory(Mage_Catalog_Model_Category $category)
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Urlrewrite_Catalog_Category_Edit extends Mage_Adminhtml_Block_Urlrewrite_Edit
{
    /**
     * Prepare layout for URL rewrite creating for category
     */
    protected function _prepareLayoutFeatures()
    {
        /** @var $helper Mage_Adminhtml_Helper_Data */
        $helper = Mage::helper('Mage_Adminhtml_Helper_Data');

        if ($this->_getUrlRewrite()->getId()) {
            $this->_headerText = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Edit URL Rewrite for a Category');
        } else {
            $this->_headerText = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Add URL Rewrite for a Category');
        }

        if ($this->_getCategory()->getId()) {
            $this->_addCategoryLinkBlock();
            $this->_addEditFormBlock();
            $this->_updateBackButtonLink($helper->getUrl('*/*/edit') . 'category');
        } else {
            $this->_addUrlRewriteSelectorBlock();
            $this->_addCategoryTreeBlock();
        }
    }

    /**
     * Get or create new instance of category
     *
     * @return Mage_Catalog_Model_Product
     */
    private function _getCategory()
    {
        if (!$this->hasData('category')) {
            $this->setCategory(Mage::getModel('Mage_Catalog_Model_Category'));
        }
        return $this->getCategory();
    }

    /**
     * Add child category link block
     */
    private function _addCategoryLinkBlock()
    {
        /** @var $helper Mage_Adminhtml_Helper_Data */
        $helper = Mage::helper('Mage_Adminhtml_Helper_Data');
        $this->addChild('category_link', 'Mage_Adminhtml_Block_Urlrewrite_Link', array(
            'item_url'  => $helper->getUrl('*/*/*') . 'category',
            'item_name' => $this->_getCategory()->getName(),
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Category:')
        ));
    }

    /**
     * Add child category tree block
     */
    private function _addCategoryTreeBlock()
    {
        $this->addChild('categories_tree', 'Mage_Adminhtml_Block_Urlrewrite_Catalog_Category_Tree');
    }

    /**
     * Creates edit form block
     *
     * @return Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form
     */
    protected function _createEditFormBlock()
    {
        return $this->getLayout()->createBlock('Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form', '', array(
            'category'    => $this->_getCategory(),
            'url_rewrite' => $this->_getUrlRewrite()
        ));
    }
}
