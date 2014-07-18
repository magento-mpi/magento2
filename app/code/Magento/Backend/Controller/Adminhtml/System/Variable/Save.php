<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Variable;

class Save extends \Magento\Backend\Controller\Adminhtml\System\Variable
{
    /**
     * Save Action
     *
     * @return void
     */
    public function execute()
    {
        $variable = $this->_initVariable();
        $data = $this->getRequest()->getPost('variable');
        $back = $this->getRequest()->getParam('back', false);
        if ($data) {
            $data['variable_id'] = $variable->getId();
            $variable->setData($data);
            try {
                $variable->save();
                $this->messageManager->addSuccess(__('You saved the custom variable.'));
                if ($back) {
                    $this->_redirect(
                        'adminhtml/*/edit',
                        array('_current' => true, 'variable_id' => $variable->getId())
                    );
                } else {
                    $this->_redirect('adminhtml/*/', array());
                }
                return;
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
