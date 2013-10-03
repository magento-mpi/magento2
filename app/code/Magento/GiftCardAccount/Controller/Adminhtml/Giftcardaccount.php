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
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Controller\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $this->_title(__('Gift Card Accounts'));

        if ($this->_showCodePoolStatusMessage) {
            $usage = $this->_objectManager->create('Magento\GiftCardAccount\Model\Pool')->getPoolUsageInfo();

            $url = $this->_objectManager->get('Magento\Backend\Model\Url')->getUrl('*/*/generate');
            $notice = __('Code Pool used: <b>%1%%</b> (free <b>%2</b> of <b>%3</b> total). Generate new code pool <a href="%4">here</a>.', $usage->getPercent(), $usage->getFree(), $usage->getTotal(), $url);
            if ($usage->getPercent() == 100) {
                $this->_getSession()->addError($notice);
            } else {
                $this->_getSession()->addNotice($notice);
            }
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
            $this->_getSession()->addError(__('This gift card account has been deleted.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($model->getId() ? $model->getCode() : __('New Account'));

        $data = $this->_getSession()->getFormData(true);
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
        $data = $this->getRequest()->getPost();
        if ($data) {
            $data = $this->_filterPostData($data);
            // init model and set data
            $id = $this->getRequest()->getParam('giftcardaccount_id');
            $model = $this->_initGca('giftcardaccount_id');
            if (!$model->getId() && $id) {
                $this->_getSession()->addError(__('This gift card account has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }

            if ($this->_objectManager->get('Magento\Core\Model\StoreManager')->isSingleStoreMode()) {
                $data['website_id'] = $this->_objectManager->get('Magento\Core\Model\StoreManager')->getStore(true)
                    ->getWebsiteId();
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
                        } else {
                            $status = true;
                        }
                    } catch (\Exception $e) {
                        $sending = false;
                    }
                }

                if (!is_null($sending)) {
                    if ($sending) {
                        $this->_getSession()->addSuccess(__('You saved the gift card account.'));
                    } else {
                        $this->_getSession()->addError(__('You saved the gift card account, but an email was not sent.'));
                    }
                } else {
                    $this->_getSession()->addSuccess(__('You saved the gift card account.'));

                    if ($status) {
                        $this->_getSession()->addNotice(__('An email was not sent because the gift card account is not active.'));
                    }
                }

                // clear previously saved data from session
                $this->_getSession()->setFormData(false);

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
                $this->_getSession()->addError($e->getMessage());
                // save data in session
                $this->_getSession()->setFormData($data);
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
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Magento\GiftCardAccount\Model\Giftcardaccount');
                $model->load($id);
                $model->delete();
                // display success message
                $this->_getSession()->addSuccess(__('This gift card account has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (\Exception $e) {
                // display error message
                $this->_getSession()->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        $this->_getSession()->addError(__("We couldn't find a gift card account to delete."));
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
            $this->_objectManager->create('Magento\GiftCardAccount\Model\Pool')->generatePool();
            $this->_getSession()->addSuccess(__('New code pool was generated.'));
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('We were unable to generate a new code pool.'));
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
     * @return \Magento\GiftCardAccount\Model\Giftcardaccount
     */
    protected function _initGca($idFieldName = 'id')
    {
        $this->_title(__('Gift Card Accounts'));

        $id = (int)$this->getRequest()->getParam($idFieldName);
        $model = $this->_objectManager->create('Magento\GiftCardAccount\Model\Giftcardaccount');
        if ($id) {
            $model->load($id);
        }
        $this->_coreRegistry->register('current_giftcardaccount', $model);
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
                    $model = $this->_objectManager->create('Magento\GiftCardAccount\Model\Giftcardaccount')->load($id);
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
