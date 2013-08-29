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
     * @param Magento_Page_Helper_Layout $pageLayout
     * @param Magento_Core_Helper_Context $context
     */
    public function __construct(
        Magento_Page_Helper_Layout $pageLayout,
        Magento_Core_Helper_Context $context
    ) {
        $this->_pageLayout = $pageLayout;
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

        $page = Mage::getSingleton('Magento_Cms_Model_Page');
        if (!is_null($pageId) && $pageId!==$page->getId()) {
            $delimeterPosition = strrpos($pageId, '|');
            if ($delimeterPosition) {
                $pageId = substr($pageId, 0, $delimeterPosition);
            }

            $page->setStoreId(Mage::app()->getStore()->getId());
            if (!$page->load($pageId)) {
                return false;
            }
        }

        if (!$page->getId()) {
            return false;
        }

        $inRange = Mage::app()->getLocale()
            ->isStoreDateInInterval(null, $page->getCustomThemeFrom(), $page->getCustomThemeTo());

        if ($page->getCustomTheme()) {
            if ($inRange) {
                Mage::getDesign()->setDesignTheme($page->getCustomTheme());
            }
        }
        $action->addPageLayoutHandles(array('id' => $page->getIdentifier()));

        $action->addActionLayoutHandles();
        if ($page->getRootTemplate()) {
            $handle = ($page->getCustomRootTemplate()
                        && $page->getCustomRootTemplate() != 'empty'
                        && $inRange) ? $page->getCustomRootTemplate() : $page->getRootTemplate();
            $this->_pageLayout->applyHandle($handle);
        }

        Mage::dispatchEvent('cms_page_render', array('page' => $page, 'controller_action' => $action));

        $action->loadLayoutUpdates();
        $layoutUpdate = ($page->getCustomLayoutUpdateXml() && $inRange)
            ? $page->getCustomLayoutUpdateXml() : $page->getLayoutUpdateXml();
        if (!empty($layoutUpdate)) {
            $action->getLayout()->getUpdate()->addUpdate($layoutUpdate);
        }
        $action->generateLayoutXml()->generateLayoutBlocks();

        $contentHeadingBlock = $action->getLayout()->getBlock('page_content_heading');
        if ($contentHeadingBlock) {
            $contentHeading = $this->escapeHtml($page->getContentHeading());
            $contentHeadingBlock->setContentHeading($contentHeading);
        }

        if ($page->getRootTemplate()) {
            $this->_pageLayout->applyTemplate($page->getRootTemplate());
        }

        /* @TODO: Move catalog and checkout storage types to appropriate modules */
        $messageBlock = $action->getLayout()->getMessagesBlock();
        foreach (array('Magento_Catalog_Model_Session', 'Magento_Checkout_Model_Session', 'Magento_Customer_Model_Session') as $storageType) {
            $storage = Mage::getSingleton($storageType);
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
