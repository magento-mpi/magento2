<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Stub block that outputs a raw CMS-page
 *
 */
namespace Magento\WebsiteRestriction\Block\Cms;

class Stub extends \Magento\Cms\Block\Page
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Cms\Model\Page $page
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Cms\Model\Page $page,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        parent::__construct($context, $page, $filterProvider, $storeManager, $pageFactory, $pageConfig, $data);
        $this->_coreRegistry = $registry;
    }

    /**
     * Retrieve page from registry if it is not there try to laod it by indetifier
     *
     * @return \Magento\Cms\Model\Page
     */
    public function getPage()
    {
        if (!$this->hasData('page')) {
            $page = $this->_coreRegistry->registry('restriction_landing_page');
            if (!$page) {
                $page = $this->_pageFactory->create()->load($this->getPageIdentifier(), 'identifier');
            }
            $this->setData('page', $page);
        }
        return $this->getData('page');
    }

    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        $page = $this->getPage();
        $this->pageConfig->addBodyClass('cms-' . $page->getIdentifier());

        if ($head = $this->getLayout()->getBlock('head')) {
            $this->pageConfig->setTitle($page->getTitle());
            $this->pageConfig->setKeywords($page->getMetaKeywords());
            $this->pageConfig->setDescription($page->getMetaDescription());
        }

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            // Setting empty page title if content heading is absent
            $cmsTitle = $page->getContentHeading() ?: ' ';
            $pageMainTitle->setPageTitle($this->escapeHtml($cmsTitle));
        }
    }
}
