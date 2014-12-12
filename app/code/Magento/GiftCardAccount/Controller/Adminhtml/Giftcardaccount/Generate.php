<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftCardAccount\Controller\Adminhtml\Giftcardaccount;

class Generate extends \Magento\GiftCardAccount\Controller\Adminhtml\Giftcardaccount
{
    /**
     * Generate code pool
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->_objectManager->create('Magento\GiftCardAccount\Model\Pool')->generatePool();
            $this->messageManager->addSuccess(__('New code pool was generated.'));
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We were unable to generate a new code pool.'));
        }

        $this->_redirect($this->getUrl('*/*/'));
    }
}
