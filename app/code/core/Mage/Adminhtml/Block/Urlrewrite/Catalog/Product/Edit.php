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
 * Block for Catalog Category URL rewrites editing
 *
 * @method Mage_Catalog_Model_Category getCategory()
 * @method Mage_Catalog_Model_Product getProduct()
 * @method bool getIsCategoryMode()
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_Edit extends Mage_Adminhtml_Block_Urlrewrite_Edit
{
    /**
     * Prepare layout for URL rewrite creating for product
     */
    protected function _prepareLayoutFeatures()
    {
        /** @var $helper Mage_Adminhtml_Helper_Data */
        $helper = Mage::helper('Mage_Adminhtml_Helper_Data');

        $this->_headerText = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Add URL Rewrite for a Product');

        if ($this->getProduct()->getId()) {
            $this->_addProductLinkBlock($this->getProduct());
        }

        if ($this->getCategory()->getId()) {
            $this->_addCategoryLinkBlock();
        }

        if ($this->getProduct()->getId()) {
            if ($this->getCategory()->getId() || !$this->getIsCategoryMode()) {
                $this->_addEditFormBlock();
                $this->_updateBackButtonLink(
                    $helper->getUrl('*/*/edit', array('product' => $this->getProduct()->getId())) . 'category'
                );
            } else {
                // categories selector & skip categories button
                $this->_addCategoriesTreeBlock();
                $this->_addSkipCategoriesBlock();
                $this->_updateBackButtonLink($helper->getUrl('*/*/edit') . 'product');
            }
        } else {
            $this->_addUrlRewriteSelectorBlock();
            $this->_addProductsGridBlock();
        }
    }

    /**
     * Add child product link block
     */
    private function _addProductLinkBlock()
    {
        /** @var $helper Mage_Adminhtml_Helper_Data */
        $helper = Mage::helper('Mage_Adminhtml_Helper_Data');
        $this->setChild('product_link', $this->getLayout()->createBlock('Mage_Adminhtml_Block_Urlrewrite_Link')
            ->setData(array(
                'item_url'  => $helper->getUrl('*/*/*') . 'product',
                'item_name' => $this->getProduct()->getName(),
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Product:')
            ))
        );
    }

    /**
     * Add child category link block
     */
    private function _addCategoryLinkBlock()
    {
        /** @var $helper Mage_Adminhtml_Helper_Data */
        $helper = Mage::helper('Mage_Adminhtml_Helper_Data');
        $this->setChild('category_link', $this->getLayout()->createBlock('Mage_Adminhtml_Block_Urlrewrite_Link')
            ->setData(array(
                'item_url'  => $helper->getUrl('*/*/*', array('product' => $this->getProduct()->getId())) . 'category',
                'item_name' => $this->getCategory()->getName(),
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Category:')
            ))
        );
    }

    /**
     * Add child products grid block
     */
    private function _addProductsGridBlock()
    {
        $this->setChild(
            'products_grid',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_Grid')
        );
    }

    /**
     * Add child Skip Categories block
     */
    private function _addSkipCategoriesBlock()
    {
        /** @var $helper Mage_Adminhtml_Helper_Data */
        $helper = Mage::helper('Mage_Adminhtml_Helper_Data');
        $this->setChild('skip_categories',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setData(array(
                'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Skip Category Selection'),
                'onclick' => 'window.location = \''
                    . $helper->getUrl('*/*/*', array('product' => $this->getProduct()->getId())) . '\'',
                'class' => 'save',
                'level' => -1
            ))
        );
    }

    /**
     * Add child Categories Tree block
     */
    private function _addCategoriesTreeBlock()
    {
        $this->setChild(
            'categories_tree',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Urlrewrite_Catalog_Category_Tree')
        );
    }

    /**
     * Creates edit form block
     *
     * @return Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form
     */
    protected function _createEditFormBlock()
    {
        return $this->getLayout()->createBlock('Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form', '', array(
            'product'     => $this->getProduct(),
            'category'    => $this->getCategory(),
            'url_rewrite' => $this->getUrlRewrite()
        ));
    }
}
