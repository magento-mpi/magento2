<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftRegistry\Controller\Adminhtml\Giftregistry;

use Magento\Framework\Model\Exception;

class Delete extends \Magento\GiftRegistry\Controller\Adminhtml\Giftregistry
{
    /**
     * Delete gift registry type
     *
     * @return void
     */
    public function execute()
    {
        try {
            $model = $this->_initType();
            $model->delete();
            $this->messageManager->addSuccess(__('You deleted the gift registry type.'));
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('adminhtml/*/edit', ['id' => $model->getId()]);
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(__("We couldn't delete this gift registry type."));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
        $this->_redirect('adminhtml/*/');
    }
}
