<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
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
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * Construct
     *
     * @param \Magento\Core\Block\Context $context
     * @param \Magento\Cms\Model\Page $page
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Context $context,
        \Magento\Cms\Model\Page $page,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        parent::__construct($context, $page, $filterProvider, $storeManager, $pageFactory, $data);
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

    protected function _prepareLayout()
    {
        $page = $this->getPage();

        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('cms-'.$page->getIdentifier());
        }

        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle($page->getTitle());
            $head->setKeywords($page->getMetaKeywords());
            $head->setDescription($page->getMetaDescription());
        }
    }
}
