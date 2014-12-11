<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Banner\Controller\Adminhtml\Banner;

class MassDelete extends \Magento\Banner\Controller\Adminhtml\Banner
{
    /**
     * Delete specified banners using grid massaction
     *
     * @return void
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('banner');
        if (!is_array($ids)) {
            $this->messageManager->addError(__('Please select a banner(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = $this->_objectManager->create('Magento\Banner\Model\Banner')->load($id);
                    $model->delete();
                }

                $this->messageManager->addSuccess(__('You deleted %1 record(s).', count($ids)));
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong mass-deleting banners. Please review the action log and try again.')
                );
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
                return;
            }
        }
        $this->_redirect('adminhtml/*/index');
    }
}
