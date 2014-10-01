<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Controller\Adminhtml\Form;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Element\UiComponentFactory;

class Save extends \Magento\Ui\Controller\Adminhtml\AbstractAction
{
    /**
     * Action for AJAX request
     *
     * @return void
     */
    public function execute()
    {
        $formElement = $this->factory->createUiComponent($this->getComponent(), $this->getName());
        list($module, $controller, $action) = explode('\\', $formElement->getSaveMca());
        $this->_forward($action, $controller, $module, $this->getRequest()->getParams());
    }
}
