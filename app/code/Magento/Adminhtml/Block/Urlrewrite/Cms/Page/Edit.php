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
 * Block for CMS pages URL rewrites
 *
 * @method \Magento\Cms\Model\Page getCmsPage()
 * @method \Magento\Adminhtml\Block\Urlrewrite\Cms\Page\Edit setCmsPage(\Magento\Cms\Model\Page $cmsPage)
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Urlrewrite\Cms\Page;

class Edit extends \Magento\Adminhtml\Block\Urlrewrite\Edit
{
    /**
     * Prepare layout for URL rewrite creating for CMS page
     */
    protected function _prepareLayoutFeatures()
    {
        if ($this->_getUrlRewrite()->getId()) {
            $this->_headerText = __('Edit URL Rewrite for CMS page');
        } else {
            $this->_headerText = __('Add URL Rewrite for CMS page');
        }

        if ($this->_getCmsPage()->getId()) {
            $this->_addCmsPageLinkBlock();
            $this->_addEditFormBlock();
            $this->_updateBackButtonLink($this->_adminhtmlData->getUrl('*/*/edit') . 'cms_page');
        } else {
            $this->_addUrlRewriteSelectorBlock();
            $this->_addCmsPageGridBlock();
        }
    }

    /**
     * Get or create new instance of CMS page
     *
     * @return \Magento\Cms\Model\Page
     */
    private function _getCmsPage()
    {
        if (!$this->hasData('cms_page')) {
            $this->setCmsPage(\Mage::getModel('Magento\Cms\Model\Page'));
        }
        return $this->getCmsPage();
    }

    /**
     * Add child CMS page link block
     */
    private function _addCmsPageLinkBlock()
    {
        $this->addChild('cms_page_link', 'Magento\Adminhtml\Block\Urlrewrite\Link', array(
            'item_url'  => $this->_adminhtmlData->getUrl('*/*/*') . 'cms_page',
            'item_name' => $this->getCmsPage()->getTitle(),
            'label'     => __('CMS page:')
        ));
    }

    /**
     * Add child CMS page block
     */
    private function _addCmsPageGridBlock()
    {
        $this->addChild('cms_pages_grid', 'Magento\Adminhtml\Block\Urlrewrite\Cms\Page\Grid');
    }

    /**
     * Creates edit form block
     *
     * @return \Magento\Adminhtml\Block\Urlrewrite\Cms\Page\Edit\Form
     */
    protected function _createEditFormBlock()
    {
        return $this->getLayout()->createBlock('Magento\Adminhtml\Block\Urlrewrite\Cms\Page\Edit\Form', '', array(
            'data' => array(
                'cms_page'    => $this->_getCmsPage(),
                'url_rewrite' => $this->_getUrlRewrite()
            )
        ));
    }
}
