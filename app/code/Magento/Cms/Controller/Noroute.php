<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Controller;

class Noroute extends \Magento\App\Action\Action
{
    /**
     * Render CMS 404 Not found page
     *
     * @return void
     */
    public function indexAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
        $this->getResponse()->setHeader('Status', '404 File not found');

        $pageId = $this->_objectManager->get('Magento\Core\Model\Store\Config')
            ->getConfig(\Magento\Cms\Helper\Page::XML_PATH_NO_ROUTE_PAGE);
        if (!$this->_objectManager->get('Magento\Cms\Helper\Page')->renderPage($this, $pageId)) {
            $this->_forward('defaultNoRoute');
        }
    }
} 