<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System\Design;

class Delete extends \Magento\Backend\Controller\Adminhtml\System\Design
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $design = $this->_objectManager->create('Magento\Framework\App\DesignInterface')->load($id);

            try {
                $design->delete();
                $this->messageManager->addSuccess(__('You deleted the design change.'));
            } catch (\Magento\Framework\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __("Cannot delete the design change."));
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('adminhtml/*/'));
    }
}
