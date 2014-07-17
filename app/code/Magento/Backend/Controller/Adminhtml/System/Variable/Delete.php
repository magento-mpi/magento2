<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Variable;

class Delete extends \Magento\Backend\Controller\Adminhtml\System\Variable
{
    /**
     * Delete Action
     *
     * @return void
     */
    public function execute()
    {
        $variable = $this->_initVariable();
        if ($variable->getId()) {
            try {
                $variable->delete();
                $this->messageManager->addSuccess(__('You deleted the custom variable.'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('_current' => true));
                return;
            }
        }
        $this->_redirect('adminhtml/*/', array());
        return;
    }
}
