<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Urlrewrite\Cms\Page;

/**
 * Block for CMS pages URL rewrites
 *
 * @method \Magento\Cms\Model\Page getCmsPage()
 * @method \Magento\Backend\Block\Urlrewrite\Cms\Page\Edit setCmsPage(\Magento\Cms\Model\Page $cmsPage)
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Edit extends \Magento\Backend\Block\Urlrewrite\Edit
{
    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url\RewriteFactory $rewriteFactory
     * @param \Magento\Backend\Helper\Data $adminhtmlData
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url\RewriteFactory $rewriteFactory,
        \Magento\Backend\Helper\Data $adminhtmlData,
        \Magento\Cms\Model\PageFactory $pageFactory,
        array $data = array()
    ) {
        $this->_pageFactory = $pageFactory;
        parent::__construct($context, $rewriteFactory, $adminhtmlData, $data);
    }

    /**
     * Prepare layout for URL rewrite creating for CMS page
     *
     * @return void
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
            $this->_updateBackButtonLink($this->_adminhtmlData->getUrl('adminhtml/*/edit') . 'cms_page');
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
            $this->setCmsPage($this->_pageFactory->create());
        }
        return $this->getCmsPage();
    }

    /**
     * Add child CMS page link block
     *
     * @return void
     */
    private function _addCmsPageLinkBlock()
    {
        $this->addChild('cms_page_link', 'Magento\Backend\Block\Urlrewrite\Link', array(
            'item_url'  => $this->_adminhtmlData->getUrl('adminhtml/*/*') . 'cms_page',
            'item_name' => $this->getCmsPage()->getTitle(),
            'label'     => __('CMS page:')
        ));
    }

    /**
     * Add child CMS page block
     *
     * @return void
     */
    private function _addCmsPageGridBlock()
    {
        $this->addChild('cms_pages_grid', 'Magento\Backend\Block\Urlrewrite\Cms\Page\Grid');
    }

    /**
     * Creates edit form block
     *
     * @return \Magento\Backend\Block\Urlrewrite\Cms\Page\Edit\Form
     */
    protected function _createEditFormBlock()
    {
        return $this->getLayout()->createBlock('Magento\Backend\Block\Urlrewrite\Cms\Page\Edit\Form', '', array(
            'data' => array(
                'cms_page'    => $this->_getCmsPage(),
                'url_rewrite' => $this->_getUrlRewrite()
            )
        ));
    }
}
