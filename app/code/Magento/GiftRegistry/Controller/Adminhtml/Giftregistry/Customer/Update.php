<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftRegistry\Controller\Adminhtml\Giftregistry\Customer;

use Magento\Framework\Model\Exception;

class Update extends \Magento\GiftRegistry\Controller\Adminhtml\Giftregistry\Customer
{
    /**
     * Update gift registry items qty
     *
     * @return void
     */
    public function execute()
    {
        $items = $this->getRequest()->getParam('items');
        $entity = $this->_initEntity();
        $updatedCount = 0;

        if (is_array($items)) {
            try {
                $model = $this->_objectManager->create('Magento\GiftRegistry\Model\Item');
                foreach ($items as $itemId => $data) {
                    if (!empty($data['action'])) {
                        $model->load($itemId);
                        if ($model->getId() && $model->getEntityId() == $entity->getId()) {
                            if ($data['action'] == 'remove') {
                                $model->delete();
                            } else {
                                $model->setQty($data['qty']);
                                $model->save();
                            }
                        }
                        $updatedCount++;
                    }
                }
                if ($updatedCount) {
                    $this->messageManager->addSuccess(__('You updated this gift registry.'));
                }
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', ['id' => $entity->getId()]);
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__("We couldn't update these gift registry items."));
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            }
        }
        $this->_redirect('adminhtml/*/edit', ['id' => $entity->getId()]);
    }
}
