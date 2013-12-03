<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer reviews controller
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Customer\Controller;

class Review extends \Magento\App\Action\Action
{
    public function indexAction()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    public function viewAction()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
