<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller;

/**
 * Customer reviews controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Review extends \Magento\Framework\App\Action\Action
{
    /**
     * @return void
     */
    public function indexAction()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * @return void
     */
    public function viewAction()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
