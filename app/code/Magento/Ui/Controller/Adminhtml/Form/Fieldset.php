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

class Fieldset extends \Magento\Ui\Controller\Adminhtml\AbstractAction
{
    /**
     * Action for AJAX request
     *
     * @return void
     */
    public function execute()
    {
        $component = $this->getComponent();
        $name = $this->getName();
        if ($component && $name) {
            $fieldset = $this->factory->createUiComponent($this->getComponent(), $this->getName())->getContainer($this->_request->getParam('container'));
            $fieldset->setNotLoadByAjax();
            $this->_response->appendBody(
                $fieldset->render()
            );
        } else {
            $this->_redirect('admin');
        }
    }
}
