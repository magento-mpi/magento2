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

        $this->_headerText = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Add URL Rewrite for a Category');

        if ($this->getCategory()->getId()) {
            $this->_addCategoryLinkBlock();
            $this->_addEditFormBlock();
            $this->_updateBackButtonLink($helper->getUrl('*/*/edit') . 'category');
        } else {
            $this->_addUrlRewriteSelectorBlock();
            $this->setChild(
                'categories_tree',
                $this->getLayout()->createBlock('Mage_Adminhtml_Block_Urlrewrite_Catalog_Category_Tree')
            );
        }
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
                'item_url'  => $helper->getUrl('*/*/*') . 'category',
                'item_name' => $this->getCategory()->getName(),
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Category:')
            ))
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
            'category'    => $this->getCategory(),
            'url_rewrite' => $this->getUrlRewrite()
        ));
    }
}
