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
 * @method Magento_Cms_Model_Page getCmsPage()
 * @method Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit setCmsPage(Magento_Cms_Model_Page $cmsPage)
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit extends Magento_Adminhtml_Block_Urlrewrite_Edit
{
    /**
     * @var Magento_Cms_Model_PageFactory
     */
    protected $_pageFactory;

    /**
     * @param Magento_Cms_Model_PageFactory $pageFactory
     * @param Magento_Core_Model_Url_RewriteFactory $rewriteFactory
     * @param Magento_Backend_Helper_Data $adminhtmlData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Cms_Model_PageFactory $pageFactory,
        Magento_Core_Model_Url_RewriteFactory $rewriteFactory,
        Magento_Backend_Helper_Data $adminhtmlData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_pageFactory = $pageFactory;
        parent::__construct($rewriteFactory, $adminhtmlData, $coreData, $context, $data);
    }

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
     * @return Magento_Cms_Model_Page
     */
    private function _getCmsPage()
    {
        if (!$this->hasData('cms_page')) {
            $this->setCmsPage($this->_pageFactory->create());
        }
        return $this->getCmsPage();
    }

    /**
     * Add child CMS page link block
     */
    private function _addCmsPageLinkBlock()
    {
        $this->addChild('cms_page_link', 'Magento_Adminhtml_Block_Urlrewrite_Link', array(
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
        $this->addChild('cms_pages_grid', 'Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Grid');
    }

    /**
     * Creates edit form block
     *
     * @return Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form
     */
    protected function _createEditFormBlock()
    {
        return $this->getLayout()->createBlock('Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form', '', array(
            'data' => array(
                'cms_page'    => $this->_getCmsPage(),
                'url_rewrite' => $this->_getUrlRewrite()
            )
        ));
    }
}
