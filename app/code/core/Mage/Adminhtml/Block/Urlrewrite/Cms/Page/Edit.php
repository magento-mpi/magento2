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
 * @method Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit setCmsPage(Mage_Cms_Model_Page $cmsPage)
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

        if ($this->_getUrlRewrite()->getId()) {
            $this->_headerText = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Edit URL Rewrite for CMS page');
        } else {
            $this->_headerText = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Add URL Rewrite for CMS page');
        }

        if ($this->_getCmsPage()->getId()) {
            $this->_addCmsPageLinkBlock();
            $this->_addEditFormBlock();
            $this->_updateBackButtonLink($helper->getUrl('*/*/edit') . 'cms_page');
        } else {
            $this->_addUrlRewriteSelectorBlock();
            $this->_addCmsPageGridBlock();
        }
    }

    /**
     * Get or create new instance of CMS page
     *
     * @return Mage_Cms_Model_Page
     */
    private function _getCmsPage()
    {
        if (!$this->hasData('cms_page')) {
            $this->setCmsPage(Mage::getModel('Mage_Cms_Model_Page'));
        }
        return $this->getCmsPage();
    }

    /**
     * Add child CMS page link block
     */
    private function _addCmsPageLinkBlock()
    {
        /** @var $helper Mage_Adminhtml_Helper_Data */
        $helper = Mage::helper('Mage_Adminhtml_Helper_Data');
        $this->addChild('cms_page_link', 'Mage_Adminhtml_Block_Urlrewrite_Link', array(
            'item_url'  => $helper->getUrl('*/*/*') . 'cms_page',
            'item_name' => $this->getCmsPage()->getTitle(),
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('CMS page:')
        ));
    }

    /**
     * Add child CMS page block
     */
    private function _addCmsPageGridBlock()
    {
        $this->addChild('cms_pages_grid', 'Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Grid');
    }

    /**
     * Creates edit form block
     *
     * @return Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form
     */
    protected function _createEditFormBlock()
    {
        return $this->getLayout()->createBlock('Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form', '', array(
            'data' => array(
                'cms_page'    => $this->_getCmsPage(),
                'url_rewrite' => $this->_getUrlRewrite()
            )
        ));
    }
}
