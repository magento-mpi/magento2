<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CMS Page Helper
 *
 * @category   Magento
 * @package    Magento_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Cms\Helper;

class Page extends \Magento\Core\Helper\AbstractHelper
{
    const XML_PATH_NO_ROUTE_PAGE        = 'web/default/cms_no_route';
    const XML_PATH_NO_COOKIES_PAGE      = 'web/default/cms_no_cookies';
    const XML_PATH_HOME_PAGE            = 'web/default/cms_home_page';

    /**
     * Catalog product
     *
     * @var \Magento\Page\Helper\Layout
     */
    protected $_pageLayout;

    /**
     * Design package instance
     *
     * @var \Magento\View\DesignInterface
     */
    protected $_design;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_page;

    /**
     * @var \Magento\Core\Model\Session\Pool
     */
    protected $_sessionPool;

    /**
     * Locale
     *
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Page factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Magento\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\View\Action\LayoutServiceInterface
     */
    protected $_layoutService;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Session\Pool $sessionFactory
     * @param \Magento\Cms\Model\Page $page
     * @param \Magento\Page\Helper\Layout $pageLayout
     * @param \Magento\View\DesignInterface $design
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Escaper $escaper
     * @param \Magento\View\Action\LayoutServiceInterface $layoutService
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Session\Pool $sessionFactory,
        \Magento\Cms\Model\Page $page,
        \Magento\Page\Helper\Layout $pageLayout,
        \Magento\View\DesignInterface $design,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Escaper $escaper,
        \Magento\View\Action\LayoutServiceInterface $layoutService
    ) {
        $this->_sessionPool = $sessionFactory;
        $this->_layoutService = $layoutService;
        $this->_page = $page;
        $this->_pageLayout = $pageLayout;
        $this->_design = $design;
        $this->_pageFactory = $pageFactory;
        $this->_storeManager = $storeManager;
        $this->_locale = $locale;
        $this->_escaper = $escaper;
        parent::__construct($context);
    }

    /**
     * Renders CMS page on front end
     *
     * Call from controller action
     *
     * @param \Magento\App\Action\Action $action
     * @param integer $pageId
     * @return boolean
     */
    public function renderPage(\Magento\App\Action\Action $action, $pageId = null)
    {
        return $this->_renderPage($action, $pageId);
    }

    /**
     * Renders CMS page
     *
     * @param \Magento\App\Action\Action|\Magento\App\Action\Action $action
     * @param integer $pageId
     * @param bool $renderLayout
     * @return boolean
     */
    protected function _renderPage(\Magento\App\Action\Action  $action, $pageId = null, $renderLayout = true)
    {
        if (!is_null($pageId) && $pageId!==$this->_page->getId()) {
            $delimiterPosition = strrpos($pageId, '|');
            if ($delimiterPosition) {
                $pageId = substr($pageId, 0, $delimiterPosition);
            }

            $this->_page->setStoreId($this->_storeManager->getStore()->getId());
            if (!$this->_page->load($pageId)) {
                return false;
            }
        }

        if (!$this->_page->getId()) {
            return false;
        }

        $inRange = $this->_locale->isStoreDateInInterval(null, $this->_page->getCustomThemeFrom(),
            $this->_page->getCustomThemeTo());

        if ($this->_page->getCustomTheme()) {
            if ($inRange) {
                $this->_design->setDesignTheme($this->_page->getCustomTheme());
            }
        }
        $this->_layoutService->getLayout()->getUpdate()->addHandle('default')->addHandle('cms_page_view');
        $this->_layoutService->addPageLayoutHandles(array('id' => $this->_page->getIdentifier()));

        $this->_layoutService->addActionLayoutHandles();
        if ($this->_page->getRootTemplate()) {
            $handle = ($this->_page->getCustomRootTemplate()
                        && $this->_page->getCustomRootTemplate() != 'empty'
                        && $inRange) ? $this->_page->getCustomRootTemplate() : $this->_page->getRootTemplate();
            $this->_pageLayout->applyHandle($handle);
        }

        $this->_eventManager->dispatch(
            'cms_page_render',
            array('page' => $this->_page, 'controller_action' => $action)
        );

        $this->_layoutService->loadLayoutUpdates();
        $layoutUpdate = ($this->_page->getCustomLayoutUpdateXml() && $inRange)
            ? $this->_page->getCustomLayoutUpdateXml() : $this->_page->getLayoutUpdateXml();
        if (!empty($layoutUpdate)) {
            $this->_layoutService->getLayout()->getUpdate()->addUpdate($layoutUpdate);
        }
        $this->_layoutService->generateLayoutXml()->generateLayoutBlocks();

        $contentHeadingBlock = $this->_layoutService->getLayout()->getBlock('page_content_heading');
        if ($contentHeadingBlock) {
            $contentHeading = $this->_escaper->escapeHtml($this->_page->getContentHeading());
            $contentHeadingBlock->setContentHeading($contentHeading);
        }

        if ($this->_page->getRootTemplate()) {
            $this->_pageLayout->applyTemplate($this->_page->getRootTemplate());
        }

        /* @TODO: Move catalog and checkout storage types to appropriate modules */
        $messageBlock = $this->_layoutService->getLayout()->getMessagesBlock();
        $sessions = array(
            'Magento\Catalog\Model\Session',
            'Magento\Checkout\Model\Session',
            'Magento\Customer\Model\Session'
        );
        foreach ($sessions as $storageType) {
            $storage = $this->_sessionPool->get($storageType);
            if ($storage) {
                $messageBlock->addStorageType($storageType);
                $messageBlock->addMessages($storage->getMessages(true));
            }
        }

        if ($renderLayout) {
            $this->_layoutService->renderLayout();
        }

        return true;
    }

    /**
     * Renders CMS Page with more flexibility then original renderPage function.
     * Allows to use also backend action as first parameter.
     * Also takes third parameter which allows not run renderLayout method.
     *
     * @param \Magento\App\Action\Action $action
     * @param $pageId
     * @param $renderLayout
     * @return bool
     */
    public function renderPageExtended(\Magento\App\Action\Action $action, $pageId = null, $renderLayout = true)
    {
        return $this->_renderPage($action, $pageId, $renderLayout);
    }

    /**
     * Retrieve page direct URL
     *
     * @param string $pageId
     * @return string
     */
    public function getPageUrl($pageId = null)
    {
        /** @var \Magento\Cms\Model\Page $page */
        $page = $this->_pageFactory->create();
        if (!is_null($pageId) && $pageId !== $page->getId()) {
            $page->setStoreId($this->_storeManager->getStore()->getId());
            if (!$page->load($pageId)) {
                return null;
            }
        }

        if (!$page->getId()) {
            return null;
        }

        return $this->_urlBuilder->getUrl(null, array('_direct' => $page->getIdentifier()));
    }
}
