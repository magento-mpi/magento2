<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Controller\Adminhtml\Reward\Rate;

class Delete extends \Magento\Reward\Controller\Adminhtml\Reward\Rate
{
    /**
     * Delete Action
     *
     * @return void
     */
    public function execute()
    {
        $rate = $this->_initRate();
        if ($rate->getId()) {
            try {
                $rate->delete();
                $this->messageManager->addSuccess(__('You deleted the rate.'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', ['_current' => true]);
                return;
            }
        }

        return $this->_redirect('adminhtml/*/');
    }
}
