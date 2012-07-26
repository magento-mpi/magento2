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
 * Block for CMS pages URL rewrites
 *
 * @method Mage_Cms_Model_Page getCmsPage()
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit extends Mage_Adminhtml_Block_Urlrewrite_Edit
{
    /**
     * Prepare layout for URL rewrite creating for CMS page
     */
    protected function _prepareLayoutFeatures()
    {
        /** @var $helper Mage_Adminhtml_Helper_Data */
        $helper = Mage::helper('Mage_Adminhtml_Helper_Data');
        $this->_headerText = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Add URL Rewrite for CMS page');

        if ($this->getCmsPage()->getId()) {
            $this->_addCmsPageLinkBlock();
            $this->_addEditFormBlock();
            $this->_updateBackButtonLink($helper->getUrl('*/*/edit') . 'cms_page');
        } else {
            $this->_addUrlRewriteSelectorBlock();
            $this->_addCmsPageGridBlock();
        }
    }

    /**
     * Add child CMS page link block
     *
     * @param Mage_Catalog_Model_Category $category
     */
    private function _addCmsPageLinkBlock()
    {
        /** @var $helper Mage_Adminhtml_Helper_Data */
        $helper = Mage::helper('Mage_Adminhtml_Helper_Data');
        $this->setChild('category_link', $this->getLayout()->createBlock('Mage_Adminhtml_Block_Urlrewrite_Link')
            ->setData(array(
                'item_url'  => $helper->getUrl('*/*/*') . 'cms_page',
                'item_name' => $this->getCmsPage()->getTitle(),
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('CMS page:')
            ))
        );
    }

    /**
     * Add child CMS page block
     */
    private function _addCmsPageGridBlock()
    {
        $this->setChild(
            'cms_pages_grid',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Grid')
        );
    }

    /**
     * Creates edit form block
     *
     * @return Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form
     */
    protected function _createEditFormBlock()
    {
        return $this->getLayout()->createBlock('Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form', '', array(
            'cms_page'    => $this->getCmsPage(),
            'url_rewrite' => $this->getUrlRewrite()
        ));
    }
}
