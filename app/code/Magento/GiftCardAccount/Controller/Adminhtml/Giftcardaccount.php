<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Controller\Adminhtml;

class Giftcardaccount extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Defines if status message of code pool is show
     *
     * @var bool
     */
    protected $_showCodePoolStatusMessage = true;

    /**
     * Default action
     */
    public function indexAction()
    {
        $this->_title(__('Gift Card Accounts'));

        if ($this->_showCodePoolStatusMessage) {
            $usage = \Mage::getModel('Magento\GiftCardAccount\Model\Pool')->getPoolUsageInfo();

            $function = 'addNotice';
            if ($usage->getPercent() == 100) {
                $function = 'addError';
            }

            $url = \Mage::getSingleton('Magento\Backend\Model\Url')->getUrl('*/*/generate');
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->$function(
                __('Code Pool used: <b>%1%%</b> (free <b>%2</b> of <b>%3</b> total). Generate new code pool <a href="%4">here</a>.', $usage->getPercent(), $usage->getFree(), $usage->getTotal(), $url)
            );
        }

        $this->loadLayout();
        $this->_setActiveMenu('Magento_GiftCardAccount::customer_giftcardaccount');
        $this->renderLayout();
    }


    /**
     * Create new Gift Card Account
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit GiftCardAccount
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_initGca();

        if (!$model->getId() && $id) {
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('This gift card account has been deleted.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($model->getId() ? $model->getCode() : __('New Account'));

        $data = \Mage::getSingleton('Magento\Adminhtml\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->loadLayout()
            ->_addBreadcrumb($id ? __('Edit Gift Card Account') : __('New Gift Card Account'),
                             $id ? __('Edit Gift Card Account') : __('New Gift Card Account'))
            ->_addContent(
                $this->getLayout()->createBlock('Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Edit')
                    ->setData('form_action_url', $this->getUrl('*/*/save'))
            )
            ->_addLeft(
                $this->getLayout()->createBlock('Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Edit\Tabs')
            )
            ->_setActiveMenu('Magento_GiftCardAccount::customer_giftcardaccount')
            ->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            $data = $this->_filterPostData($data);
            // init model and set data
            $id = $this->getRequest()->getParam('giftcardaccount_id');
            $model = $this->_initGca('giftcardaccount_id');
            if (!$model->getId() && $id) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('This gift card account has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }

            if (\Mage::app()->isSingleStoreMode()) {
                $data['website_id'] = \Mage::app()->getStore(true)->getWebsiteId();
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
                        if($model->getStatus()){
                            $model->sendEmail();
                            $sending = $model->getEmailSent();
                        }
                        else {
                            $status = true;
                        }
                    } catch (\Exception $e) {
                        $sending = false;
                    }
                }

                if (!is_null($sending)) {
                    if ($sending) {
                        \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('You saved the gift card account.'));
                    } else {
                        \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('You saved the gift card account, but an email was not sent.'));
                    }
                } else {
                    \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('You saved the gift card account.'));

                    if ($status) {
                        \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addNotice(__('An email was not sent because the gift card account is not active.'));
                    }
                }

                // clear previously saved data from session
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (\Exception $e) {
                // display error message
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                // save data in session
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                // init model and delete
                $model = \Mage::getModel('Magento\GiftCardAccount\Model\Giftcardaccount');
                $model->load($id);
                $model->delete();
                // display success message
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('This gift card account has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (\Exception $e) {
                // display error message
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__("We couldn't find a gift card account to delete."));
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Render GCA grid
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Generate code pool
     */
    public function generateAction()
    {
        try {
            \Mage::getModel('Magento\GiftCardAccount\Model\Pool')->generatePool();
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('New code pool was generated.'));
        } catch (\Magento\Core\Exception $e) {
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
        } catch (\Exception $e) {
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addException($e, __('We were unable to generate a new code pool.'));
        }
        $this->_redirectReferer('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_GiftCardAccount::customer_giftcardaccount');
    }

    /**
     * Render GCA history grid
     */
    public function gridHistoryAction()
    {
        $model = $this->_initGca();
        $id = (int)$this->getRequest()->getParam('id');
        if ($id && !$model->getId()) {
            return;
        }

        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Edit\Tab\History')
                ->toHtml()
        );
    }

    /**
     * Load GCA from request
     *
     * @param string $idFieldName
     */
    protected function _initGca($idFieldName = 'id')
    {
        $this->_title(__('Gift Card Accounts'));

        $id = (int)$this->getRequest()->getParam($idFieldName);
        $model = \Mage::getModel('Magento\GiftCardAccount\Model\Giftcardaccount');
        if ($id) {
            $model->load($id);
        }
        \Mage::register('current_giftcardaccount', $model);
        return $model;
    }

    /**
     * Export GCA grid to MSXML
     */
    public function exportMsxmlAction()
    {
        $this->loadLayout();
        $fileName = 'giftcardaccounts.xml';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('gift.card.account.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile($fileName));
    }

    /**
     * Export GCA grid to CSV
     */
    public function exportCsvAction()
    {
        $this->loadLayout();
        $fileName = 'giftcardaccounts.csv';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('gift.card.account.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile($fileName));
    }

    /**
     * Delete gift card accounts specified using grid massaction
     */
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('giftcardaccount');
        if (!is_array($ids)) {
            $this->_getSession()->addError(__('Please select a gift card account(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = \Mage::getSingleton('Magento\GiftCardAccount\Model\Giftcardaccount')->load($id);
                    $model->delete();
                }

                $this->_getSession()->addSuccess(
                    __('You deleted a total of %1 records.', count($ids))
                );
            } catch (\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('date_expires'));

        return $data;
    }

    /**
     * Setter for code pool status message flag
     *
     * @param bool $isShow
     */
    public function setShowCodePoolStatusMessage($isShow)
    {
        $this->_showCodePoolStatusMessage = (bool)$isShow;
    }
}
