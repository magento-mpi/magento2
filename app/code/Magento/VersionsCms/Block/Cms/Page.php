<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Block\Cms;

/**
 * Cms page content block
 */
class Page extends \Magento\Cms\Block\Page
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\VersionsCms\Model\Hierarchy\NodeFactory
     */
    protected $_hierarchyNodeFactory;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Cms\Model\Page $page
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Framework\View\Page\Config $pageConfig
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Cms\Model\Page $page,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\VersionsCms\Model\Hierarchy\NodeFactory $hierarchyNodeFactory
    ) {
        parent::__construct($context, $page, $filterProvider, $storeManager, $pageFactory, $pageConfig);

        $this->_coreRegistry = $coreRegistry;
        $this->_hierarchyNodeFactory = $hierarchyNodeFactory;
    }


    /**
     * Prepare breadcrumbs
     *
     * @param \Magento\Cms\Model\Page $page
     * @throws \Magento\Framework\Exception
     */
    protected function _addBreadcrumbs(\Magento\Cms\Model\Page $page)
    {
        $breadcrumbs = array();
        if ($this->_scopeConfig->getValue(
                'web/default/show_cms_breadcrumbs',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ) && ($breadcrumbsBlock = $this->getLayout()->getBlock(
                'breadcrumbs'
            )) && $page->getIdentifier() !== $this->_scopeConfig->getValue(
                'web/default/cms_home_page',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ) && $page->getIdentifier() !== $this->_scopeConfig->getValue(
                'web/default/cms_no_route',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        ) {
            $breadcrumbsBlock->addCrumb(
                'home',
                array(
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                )
            );

            if ($currentNode = $this->_coreRegistry->registry('current_cms_hierarchy_node')) {
                $nodePathIds = explode('/', $currentNode->getXpath());
                foreach ($nodePathIds as $nodeId) {
                    if ($currentNode->getId() != $nodeId) {
                        $nodeModel = $this->_hierarchyNodeFactory->create();
                        $node = $nodeModel->load($nodeId);
                        $breadcrumbs[] = array(
                            'crumbName' => 'cms_node_' . $node->getId(),
                            'crumbInfo' => array(
                                'label' => $node->getLabel(),
                                'link' => $node->getUrl(),
                                'title' => $node->getLabel()));
                    }
                }
            }

            foreach ($breadcrumbs as $breadcrumbsItem) {
                $breadcrumbsBlock->addCrumb($breadcrumbsItem['crumbName'], $breadcrumbsItem['crumbInfo']);
            }

            $breadcrumbsBlock->addCrumb('cms_page', array('label' => $page->getTitle(), 'title' => $page->getTitle()));
        }
    }
}
