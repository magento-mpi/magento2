<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Controller\Adminhtml\Giftcardaccount;

class Save extends \Magento\GiftCardAccount\Controller\Adminhtml\Giftcardaccount
{
    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array $data
     * @return array
     */
    protected function _filterPostData($data)
    {
        $inputFilter = new \Zend_Filter_Input(array('date_expires' => $this->_dateFilter), array(), $data);
        return $inputFilter->getUnescaped();
    }

    /**
     * Save action
     *
     * @return void
     */
    public function execute()
    {
        // check if data sent
        $data = $this->getRequest()->getPost();
        if ($data) {
            $data = $this->_filterPostData($data);
            // init model and set data
            $id = $this->getRequest()->getParam('giftcardaccount_id');
            $model = $this->_initGca('giftcardaccount_id');
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This gift card account has been deleted.'));
                $this->_redirect('adminhtml/*/');
                return;
            }

            if ($this->_objectManager->get('Magento\Store\Model\StoreManager')->isSingleStoreMode()) {
                $data['website_id'] = $this->_objectManager->get(
                    'Magento\Store\Model\StoreManager'
                )->getStore(
                    true
                )->getWebsiteId();
            }

            if (!empty($data)) {
                $model->addData($data);
            }

            // try to save it
            try {
                // save the data
                $model->save();
                $sending = null;
                $status = null;

                if ($model->getSendAction()) {
                    try {
                        if ($model->getStatus()) {
                            $model->sendEmail();
                            $sending = $model->getEmailSent();
                        } else {
                            $status = true;
                        }
                    } catch (\Exception $e) {
                        $sending = false;
                    }
                }

                if (!is_null($sending)) {
                    if ($sending) {
                        $this->messageManager->addSuccess(__('You saved the gift card account.'));
                    } else {
                        $this->messageManager->addError(
                            __('You saved the gift card account, but an email was not sent.')
                        );
                    }
                } else {
                    $this->messageManager->addSuccess(__('You saved the gift card account.'));

                    if ($status) {
                        $this->messageManager->addNotice(
                            __('An email was not sent because the gift card account is not active.')
                        );
                    }
                }

                // clear previously saved data from session
                $this->_getSession()->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('adminhtml/*/');
                return;
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // save data in session
                $this->_getSession()->setFormData($data);
                // redirect to edit form
                $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
                return;
            }
        }
        $this->_redirect('adminhtml/*/');
    }
}
