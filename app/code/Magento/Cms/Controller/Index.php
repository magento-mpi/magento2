<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Controller;

/**
 * Cms index controller
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Renders CMS Home page
     *
     * @param string|null $coreRoute
     * @return void
     */
    public function indexAction($coreRoute = null)
    {
        $pageId = $this->_objectManager->get(
            'Magento\Framework\App\Config\ScopeConfigInterface'
        )->getValue(
            \Magento\Cms\Helper\Page::XML_PATH_HOME_PAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!$this->_objectManager->get('Magento\Cms\Helper\Page')->renderPage($this, $pageId)) {
            $this->_forward('defaultIndex');
        }
    }

    /**
     * Default index action (with 404 Not Found headers)
     * Used if default page don't configure or available
     *
     * @return void
     */
    public function defaultIndexAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
        $this->getResponse()->setHeader('Status', '404 File not found');

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Default no route page action
     * Used if no route page don't configure or available
     *
     * @return void
     */
    public function defaultNoRouteAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
        $this->getResponse()->setHeader('Status', '404 File not found');

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Render Disable cookies page
     *
     * @return void
     */
    public function noCookiesAction()
    {
        $pageId = $this->_objectManager->get(
            'Magento\Framework\App\Config\ScopeConfigInterface',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )->getValue(
            \Magento\Cms\Helper\Page::XML_PATH_NO_COOKIES_PAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!$this->_objectManager->get('Magento\Cms\Helper\Page')->renderPage($this, $pageId)) {
            $this->_forward('defaultNoCookies');
        }
    }

    /**
     * Default no cookies page action
     * Used if no cookies page don't configure or available
     *
     * @return void
     */
    public function defaultNoCookiesAction()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
