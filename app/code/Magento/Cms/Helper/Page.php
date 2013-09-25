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
class Magento_Cms_Helper_Page extends Magento_Core_Helper_Abstract
{
    const XML_PATH_NO_ROUTE_PAGE        = 'web/default/cms_no_route';
    const XML_PATH_NO_COOKIES_PAGE      = 'web/default/cms_no_cookies';
    const XML_PATH_HOME_PAGE            = 'web/default/cms_home_page';

    /**
     * Catalog product
     *
     * @var Magento_Page_Helper_Layout
     */
    protected $_pageLayout = null;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * Design package instance
     *
     * @var Magento_Core_Model_View_DesignInterface
     */
    protected $_design = null;

    /**
     * @var Magento_Cms_Model_Page
     */
    protected $_page;

    /**
     * @var Magento_Core_Model_Session_Pool
     */
    protected $_sessionPool;

    /**
     * @param Magento_Core_Model_Session_Pool $sessionFactory
     * @param Magento_Cms_Model_Page $page
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Page_Helper_Layout $pageLayout
     * @param Magento_Core_Model_View_DesignInterface $design
     * @param Magento_Core_Helper_Context $context
     */
    public function __construct(
        Magento_Core_Model_Session_Pool $sessionFactory,
        Magento_Cms_Model_Page $page,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Page_Helper_Layout $pageLayout,
        Magento_Core_Model_View_DesignInterface $design,
        Magento_Core_Helper_Context $context
    ) {
        $this->_sessionPool = $sessionFactory;
        $this->_page = $page;
        $this->_eventManager = $eventManager;
        $this->_pageLayout = $pageLayout;
        $this->_design = $design;
        parent::__construct($context);
    }

    /**
     * Renders CMS page on front end
     *
     * Call from controller action
     *
     * @param Magento_Core_Controller_Front_Action $action
     * @param integer $pageId
     * @return boolean
     */
    public function renderPage(Magento_Core_Controller_Front_Action $action, $pageId = null)
    {
        return $this->_renderPage($action, $pageId);
    }

    /**
     * Renders CMS page
     *
     * @param Magento_Core_Controller_Front_Action|Magento_Core_Controller_Varien_Action $action
     * @param integer $pageId
     * @param bool $renderLayout
     * @return boolean
     */
    protected function _renderPage(Magento_Core_Controller_Varien_Action  $action, $pageId = null, $renderLayout = true)
    {
        if (!is_null($pageId) && $pageId!==$this->_page->getId()) {
            $delimeterPosition = strrpos($pageId, '|');
            if ($delimeterPosition) {
                $pageId = substr($pageId, 0, $delimeterPosition);
            }

            $this->_page->setStoreId(Mage::app()->getStore()->getId());
            if (!$this->_page->load($pageId)) {
                return false;
            }
        }

        if (!$this->_page->getId()) {
            return false;
        }

        $inRange = Mage::app()->getLocale()
            ->isStoreDateInInterval(null, $this->_page->getCustomThemeFrom(), $this->_page->getCustomThemeTo());

        if ($this->_page->getCustomTheme()) {
            if ($inRange) {
                $this->_design->setDesignTheme($this->_page->getCustomTheme());
            }
        }
        $action->addPageLayoutHandles(array('id' => $this->_page->getIdentifier()));

        $action->addActionLayoutHandles();
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

        $action->loadLayoutUpdates();
        $layoutUpdate = ($this->_page->getCustomLayoutUpdateXml() && $inRange)
            ? $this->_page->getCustomLayoutUpdateXml() : $this->_page->getLayoutUpdateXml();
        if (!empty($layoutUpdate)) {
            $action->getLayout()->getUpdate()->addUpdate($layoutUpdate);
        }
        $action->generateLayoutXml()->generateLayoutBlocks();

        $contentHeadingBlock = $action->getLayout()->getBlock('page_content_heading');
        if ($contentHeadingBlock) {
            $contentHeading = $this->escapeHtml($this->_page->getContentHeading());
            $contentHeadingBlock->setContentHeading($contentHeading);
        }

        if ($this->_page->getRootTemplate()) {
            $this->_pageLayout->applyTemplate($this->_page->getRootTemplate());
        }

        /* @TODO: Move catalog and checkout storage types to appropriate modules */
        $messageBlock = $action->getLayout()->getMessagesBlock();
        $sessions = array(
            'Magento_Catalog_Model_Session',
            'Magento_Checkout_Model_Session',
            'Magento_Customer_Model_Session'
        );
        foreach ($sessions as $storageType) {
            $storage = $this->_sessionPool->get($storageType);
            if ($storage) {
                $messageBlock->addStorageType($storageType);
                $messageBlock->addMessages($storage->getMessages(true));
            }
        }

        if ($renderLayout) {
            $action->renderLayout();
        }

        return true;
    }

    /**
     * Renders CMS Page with more flexibility then original renderPage function.
     * Allows to use also backend action as first parameter.
     * Also takes third parameter which allows not run renderLayout method.
     *
     * @param Magento_Core_Controller_Varien_Action $action
     * @param $pageId
     * @param $renderLayout
     * @return bool
     */
    public function renderPageExtended(Magento_Core_Controller_Varien_Action $action, $pageId = null, $renderLayout = true)
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
        $page = Mage::getModel('Magento_Cms_Model_Page');
        if (!is_null($pageId) && $pageId !== $page->getId()) {
            $page->setStoreId(Mage::app()->getStore()->getId());
            if (!$page->load($pageId)) {
                return null;
            }
        }

        if (!$page->getId()) {
            return null;
        }

        return Mage::getUrl(null, array('_direct' => $page->getIdentifier()));
    }
}
