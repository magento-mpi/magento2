<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller;

/**
 * Customer reviews controller
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Review extends \Magento\App\Action\Action
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
