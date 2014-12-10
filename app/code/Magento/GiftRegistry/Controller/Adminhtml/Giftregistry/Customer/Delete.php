<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftRegistry\Controller\Adminhtml\Giftregistry\Customer;

use Magento\Framework\Model\Exception;

class Delete extends \Magento\GiftRegistry\Controller\Adminhtml\Giftregistry\Customer
{
    /**
     * Delete gift registry action
     *
     * @return void
     */
    public function execute()
    {
        try {
            $model = $this->_initEntity();
            $customerId = $model->getCustomerId();
            $model->delete();
            $this->messageManager->addSuccess(__('You deleted this gift registry entity.'));
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('adminhtml/*/edit', ['id' => $model->getId()]);
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(__("We couldn't delete this gift registry entity."));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
        $this->_redirect('customer/index/edit', ['id' => $customerId, 'active_tab' => 'giftregistry']);
    }
}
