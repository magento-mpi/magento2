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
     * Design package instance
     *
     * @var \Magento\Core\Model\View\DesignInterface
     */
    protected $_design = null;

    /**
     * @param \Magento\Core\Model\View\DesignInterface $design
     * @param \Magento\Core\Helper\Context $context
     */
    public function __construct(
        \Magento\Core\Model\View\DesignInterface $design,
        \Magento\Core\Helper\Context $context
    ) {
        $this->_design = $design;
        parent::__construct($context);
    }

    /**
    * Renders CMS page on front end
    *
    * Call from controller action
    *
    * @param \Magento\Core\Controller\Front\Action $action
    * @param integer $pageId
    * @return boolean
    */
    public function renderPage(\Magento\Core\Controller\Front\Action $action, $pageId = null)
    {
        return $this->_renderPage($action, $pageId);
    }

    /**
     * Renders CMS page
     *
     * @param \Magento\Core\Controller\Front\Action|\Magento\Core\Controller\Varien\Action $action
     * @param integer $pageId
     * @param bool $renderLayout
     * @return boolean
     */
    protected function _renderPage(\Magento\Core\Controller\Varien\Action  $action, $pageId = null, $renderLayout = true)
    {

        $page = \Mage::getSingleton('Magento\Cms\Model\Page');
        if (!is_null($pageId) && $pageId!==$page->getId()) {
            $delimeterPosition = strrpos($pageId, '|');
            if ($delimeterPosition) {
                $pageId = substr($pageId, 0, $delimeterPosition);
            }

            $page->setStoreId(\Mage::app()->getStore()->getId());
            if (!$page->load($pageId)) {
                return false;
            }
        }

        if (!$page->getId()) {
            return false;
        }

        $inRange = \Mage::app()->getLocale()
            ->isStoreDateInInterval(null, $page->getCustomThemeFrom(), $page->getCustomThemeTo());

        if ($page->getCustomTheme()) {
            if ($inRange) {
                $this->_design->setDesignTheme($page->getCustomTheme());
            }
        }
        $action->addPageLayoutHandles(array('id' => $page->getIdentifier()));

        $action->addActionLayoutHandles();
        if ($page->getRootTemplate()) {
            $handle = ($page->getCustomRootTemplate()
                        && $page->getCustomRootTemplate() != 'empty'
                        && $inRange) ? $page->getCustomRootTemplate() : $page->getRootTemplate();
            $action->getLayout()->helper('Magento\Page\Helper\Layout')->applyHandle($handle);
        }

        \Mage::dispatchEvent('cms_page_render', array('page' => $page, 'controller_action' => $action));

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
            $action->getLayout()->helper('Magento\Page\Helper\Layout')
                ->applyTemplate($page->getRootTemplate());
        }

        /* @TODO: Move catalog and checkout storage types to appropriate modules */
        $messageBlock = $action->getLayout()->getMessagesBlock();
        foreach (array('Magento\Catalog\Model\Session', 'Magento\Checkout\Model\Session', 'Magento\Customer\Model\Session') as $storageType) {
            $storage = \Mage::getSingleton($storageType);
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
     * @param \Magento\Core\Controller\Varien\Action $action
     * @param $pageId
     * @param $renderLayout
     * @return bool
     */
    public function renderPageExtended(\Magento\Core\Controller\Varien\Action $action, $pageId = null, $renderLayout = true)
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
        $page = \Mage::getModel('Magento\Cms\Model\Page');
        if (!is_null($pageId) && $pageId !== $page->getId()) {
            $page->setStoreId(\Mage::app()->getStore()->getId());
            if (!$page->load($pageId)) {
                return null;
            }
        }

        if (!$page->getId()) {
            return null;
        }

        return \Mage::getUrl(null, array('_direct' => $page->getIdentifier()));
    }
}
