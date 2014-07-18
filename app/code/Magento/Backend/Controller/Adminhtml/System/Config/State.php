<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Config;

class State extends AbstractScopeConfig
{
    /**
     * Save fieldset state through AJAX
     *
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getParam(
            'isAjax'
        ) && $this->getRequest()->getParam(
            'container'
        ) != '' && $this->getRequest()->getParam(
            'value'
        ) != ''
        ) {
            $configState = array($this->getRequest()->getParam('container') => $this->getRequest()->getParam('value'));
            $this->_saveState($configState);
            $this->getResponse()->setBody('success');
        }
    }
}
