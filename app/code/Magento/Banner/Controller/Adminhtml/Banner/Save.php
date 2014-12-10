<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Banner\Controller\Adminhtml\Banner;

class Save extends \Magento\Banner\Controller\Adminhtml\Banner
{
    /**
     * Save action
     *
     * @return void
     */
    public function execute()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        $data = $this->getRequest()->getPost();
        if ($data) {
            $bannerId = $this->getRequest()->getParam('id');
            $model = $this->_initBanner();
            if (!$model->getId() && $bannerId) {
                $this->messageManager->addError(__('This banner does not exist.'));
                $this->_redirect('adminhtml/*/');
                return;
            }

            //Filter disallowed data
            $currentStores = array_keys(
                $this->_objectManager->get('Magento\Store\Model\StoreManager')->getStores(true)
            );
            if (isset($data['store_contents_not_use'])) {
                $data['store_contents_not_use'] = array_intersect($data['store_contents_not_use'], $currentStores);
            }
            if (isset($data['store_contents'])) {
                $data['store_contents'] = array_intersect_key($data['store_contents'], array_flip($currentStores));
            }

            // prepare post data
            if (isset($data['banner_catalog_rules'])) {
                $related = $this->_objectManager->get(
                    'Magento\Backend\Helper\Js'
                )->decodeGridSerializedInput(
                    $data['banner_catalog_rules']
                );
                foreach ($related as $_key => $_rid) {
                    $related[$_key] = (int)$_rid;
                }
                $data['banner_catalog_rules'] = $related;
            }
            if (isset($data['banner_sales_rules'])) {
                $related = $this->_objectManager->get(
                    'Magento\Backend\Helper\Js'
                )->decodeGridSerializedInput(
                    $data['banner_sales_rules']
                );
                foreach ($related as $_key => $_rid) {
                    $related[$_key] = (int)$_rid;
                }
                $data['banner_sales_rules'] = $related;
            }

            // save model
            try {
                if (!empty($data)) {
                    $model->addData($data);
                    $this->_getSession()->setFormData($data);
                }
                $model->save();
                $this->_getSession()->setFormData(false);
                $this->messageManager->addSuccess(__('You saved the banner.'));
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $redirectBack = true;
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We cannot save the banner.'));
                $redirectBack = true;
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            }
            if ($redirectBack) {
                $this->_redirect('adminhtml/*/edit', ['id' => $model->getId()]);
                return;
            }
        }
        $this->_redirect('adminhtml/*/');
    }
}
