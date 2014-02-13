<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml;

class Noroute extends \Magento\Backend\App\Action
{
    /**
     * Noroute action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
        $this->getResponse()->setHeader('Status', '404 File not found');
        $this->_view->loadLayout(array('default', 'adminhtml_noroute'));
        $this->_view->renderLayout();
    }
} 