<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class Edit extends \Magento\Rma\Controller\Adminhtml\Rma
{
    /**
     * Edit RMA
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function execute()
    {
        try {
            $model = $this->_initModel();
            if (!$model->getId()) {
                throw new \Magento\Framework\Model\Exception(__('The wrong RMA was requested.'));
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('adminhtml/*/');
            return;
        }
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(sprintf("#%s", $model->getIncrementId()));
        $this->_view->renderLayout();
    }
}
