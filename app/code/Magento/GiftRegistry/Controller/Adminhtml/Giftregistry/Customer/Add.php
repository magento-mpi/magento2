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

class Add extends \Magento\GiftRegistry\Controller\Adminhtml\Giftregistry\Customer
{
    /**
     * Add quote items to gift registry
     *
     * @return void
     */
    public function execute()
    {
        if ($quoteIds = $this->getRequest()->getParam('products')) {
            $model = $this->_initEntity();
            try {
                $skippedItems = $model->addQuoteItems($quoteIds);
                if (count($quoteIds) - $skippedItems > 0) {
                    $this->messageManager->addSuccess(__('Shopping cart items have been added to gift registry.'));
                }
                if ($skippedItems) {
                    $this->messageManager->addNotice(
                        __('Virtual, Downloadable, and virtual Gift Card products cannot be added to gift registries.')
                    );
                }
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Failed to add shopping cart items to gift registry.'));
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            }
        }
        $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
    }
}
