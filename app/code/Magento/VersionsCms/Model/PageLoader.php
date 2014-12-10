<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\VersionsCms\Model;

class PageLoader
{
    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Cms\Model\PageFactory $pageFactory
    ) {
        $this->registry = $registry;
        $this->pageFactory = $pageFactory;
    }

    /**
     * Load cms page by id
     *
     * @param string $pageId
     * @return \Magento\Cms\Model\Page
     */
    public function load($pageId = null)
    {
        /** @var \Magento\Cms\Model\Page $page */
        $page = $this->pageFactory->create();

        if ($pageId) {
            $page->load($pageId);
        }

        $this->registry->register('cms_page', $page);
        return $page;
    }
}
