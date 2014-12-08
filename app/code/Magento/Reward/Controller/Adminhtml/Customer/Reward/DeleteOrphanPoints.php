<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Controller\Adminhtml\Customer\Reward;

class DeleteOrphanPoints extends \Magento\Reward\Controller\Adminhtml\Customer\Reward
{
    /**
     *  Delete orphan points Action
     *
     * @return void
     */
    public function execute()
    {
        $customerId = $this->getRequest()->getParam('id', 0);
        if ($customerId) {
            try {
                $this->_objectManager->create(
                    'Magento\Reward\Model\Reward'
                )->deleteOrphanPointsByCustomer(
                    $customerId
                );
                $this->messageManager->addSuccess(__('You removed the orphan points.'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('customer/index/edit', ['_current' => true]);
    }
}
