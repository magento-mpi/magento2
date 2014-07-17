<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class LoadSplitLine extends \Magento\Rma\Controller\Adminhtml\Rma
{
    /**
     * Load new row of RMA's item for Split Line functionality
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function execute()
    {
        $response = false;
        $rmaId = $this->getRequest()->getParam('rma_id');
        $itemId = $this->getRequest()->getParam('item_id');

        try {
            $model = $this->_initModel();
            if (!$model->getId()) {
                throw new \Magento\Framework\Model\Exception(__('The wrong RMA was requested.'));
            }
            /** @var $rma_item \Magento\Rma\Model\Item */
            $rma_item = $this->_objectManager->create('Magento\Rma\Model\Item');
            if ($itemId) {
                $rma_item->load($itemId);
                if (!$rma_item->getId()) {
                    throw new \Magento\Framework\Model\Exception(__('The wrong RMA item was requested.'));
                }
                $this->_coreRegistry->register('current_rma_item', $rma_item);
            } else {
                throw new \Magento\Framework\Model\Exception(__('The wrong RMA item was requested.'));
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
        } catch (\Exception $e) {
            $response = array('error' => true, 'message' => __('We cannot display the item attributes.'));
        }

        $this->_view->loadLayout();

        $response = $this->_view->getLayout()->getBlock(
            'magento_rma_edit_items_grid'
        )->setItemFilter(
            $itemId
        )->setAllFieldsEditable()->toHtml();

        if (is_array($response)) {
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($response)
            );
        } else {
            $this->getResponse()->setBody($response);
        }
    }
}
