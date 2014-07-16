<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Product;

class Gallery extends \Magento\Catalog\Controller\Product
{
    /**
     * View product gallery action
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_initProduct()) {
            if (isset($_GET['store']) && !$this->getResponse()->isRedirect()) {
                $this->_redirect('');
            } elseif (!$this->getResponse()->isRedirect()) {
                $this->_forward('noroute');
            }
            return;
        }
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
