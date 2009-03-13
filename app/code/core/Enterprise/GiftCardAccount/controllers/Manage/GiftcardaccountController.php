<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterpirse_GiftCardAccount
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_GiftCardAccount_Manage_GiftcardaccountController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Default action
     */
    public function indexAction()
    {
        $usage = Mage::getModel('enterprise_giftcardaccount/pool')->getPoolUsageInfo();

        $function = 'addNotice';
        if ($usage->getPercent() == 100) {
            $function = 'addError';
        }

        Mage::getSingleton('adminhtml/session')->$function(
            Mage::helper('enterprise_giftcardaccount')->__(
                'Code pool is %.2f%% used (%d free of %d total). <a href="%s">Generate new</a>.',
                $usage->getPercent(),
                $usage->getFree(),
                $usage->getTotal(),
                Mage::getUrl('*/*/generate'))
        );

        $this->loadLayout();
        $this->_setActiveMenu('customer/giftcardaccount');
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

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('enterprise_giftcardaccount/giftcardaccount');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            Mage::dispatchEvent('enterprise_giftcardaccount_view', array('status' => 'success', 'code' => $model->getCode()));
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('enterprise_giftcardaccount')->__('This Gift Card Account no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        }

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('current_giftcardaccount', $model);

        // 5. Build edit form
        $this->loadLayout()
            ->_addBreadcrumb($id ? Mage::helper('enterprise_giftcardaccount')->__('Edit Gift Card Account') : Mage::helper('enterprise_giftcardaccount')->__('New Gift Card Account'), $id ? Mage::helper('enterprise_giftcardaccount')->__('Edit Gift Card Account') : Mage::helper('enterprise_giftcardaccount')->__('New Gift Card Account'))
            ->_addContent($this->getLayout()->createBlock('enterprise_giftcardaccount/manage_giftcardaccount_edit')->setData('action', $this->getUrl('*/_/save')))
            ->_addLeft($this->getLayout()->createBlock('enterprise_giftcardaccount/manage_giftcardaccount_edit_tabs'))
            ->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            // init model and set data
            $model = Mage::getModel('enterprise_giftcardaccount/giftcardaccount');
            if (isset($data['info'])) {
                $model->addData($data['info']);
            }
            // try to save it
            try {
                // save the data
                $model->load($data['info']['giftcardaccount_id']);
                $model->save();
                // display success message
                Mage::dispatchEvent('enterprise_giftcardaccount_save', array('status' => 'success', 'code' => $model->getCode()));
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('enterprise_giftcardaccount')->__('Gift Card Account was successfully saved'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('block_id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::dispatchEvent('enterprise_giftcardaccount_save', array('status' => 'fail', 'code' => $model->getCode()));
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('block_id' => $this->getRequest()->getParam('block_id')));
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
                $model = Mage::getModel('enterprise_giftcardaccount/giftcardaccount');
                $model->load($id);
                $model->delete();
                Mage::dispatchEvent('enterprise_giftcardaccount_delete', array('status' => 'success', 'code' => $model->getCode()));
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('enterprise_giftcardaccount')->__('Gift Card Account was successfully deleted'));
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::dispatchEvent('enterprise_giftcardaccount_delete', array('status' => 'fail', 'code' => $model->getCode()));
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('enterprise_giftcardaccount')->__('Unable to find a Gift Card Account to delete'));
        // go to grid
        $this->_redirect('*/*/');
    }

    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('enterprise_giftcardaccount/manage_giftcardaccount_grid', 'giftcardaccount.grid')
                ->toHtml()
        );
    }

    public function generateAction()
    {
        try {
            Mage::getModel('enterprise_giftcardaccount/pool')->generatePool();
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addException($e, Mage::helper('enterprise_giftcardaccount')->__('Unable to generate new code pool.'));
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('enterprise_giftcardaccount')->__('New code pool was generated successfully.'));
        //$this->_redirect('*/*/');
        $this->_redirectReferer('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/giftcardaccount');
    }
}