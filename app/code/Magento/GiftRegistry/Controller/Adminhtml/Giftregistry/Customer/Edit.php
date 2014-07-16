<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller\Adminhtml\Giftregistry\Customer;

use \Magento\Framework\Model\Exception;

class Edit extends \Magento\GiftRegistry\Controller\Adminhtml\Giftregistry\Customer
{
    /**
     * Get customer gift registry info block
     *
     * @return void
     */
    public function execute()
    {
        try {
            $model = $this->_initEntity();
            $customer = $this->_objectManager->create(
                'Magento\Customer\Model\Customer'
            )->load(
                $model->getCustomerId()
            );

            $this->_title->add(__('Customers'));
            $this->_title->add(__('Customers'));
            $this->_title->add($customer->getName());
            $this->_title->add(__("Edit '%1' Gift Registry", $model->getTitle()));

            $this->_view->loadLayout()->renderLayout();
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect(
                'customer/index/edit',
                array('id' => $this->getRequest()->getParam('customer'), 'active_tab' => 'giftregistry')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Something went wrong while editing the gift registry.'));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $this->_redirect(
                'customer/index/edit',
                array('id' => $this->getRequest()->getParam('customer'), 'active_tab' => 'giftregistry')
            );
        }
    }
}
