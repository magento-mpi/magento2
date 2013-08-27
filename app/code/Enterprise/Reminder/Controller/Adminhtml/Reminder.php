<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reminder grid and edit controller
 */
class Enterprise_Reminder_Controller_Adminhtml_Reminder extends Magento_Adminhtml_Controller_Action
{
    /**
     * Init active menu and set breadcrumb
     *
     * @return Enterprise_Reminder_Controller_Adminhtml_Reminder
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Enterprise_Reminder::promo_reminder')
            ->_addBreadcrumb(
                __('Reminder Rules'),
                __('Reminder Rules')
            );
        return $this;
    }

    /**
     * Initialize proper rule model
     *
     * @param string $requestParam
     * @return Enterprise_Reminder_Model_Rule
     */
    protected function _initRule($requestParam = 'id')
    {
        $ruleId = $this->getRequest()->getParam($requestParam, 0);
        $rule = Mage::getModel('Enterprise_Reminder_Model_Rule');
        if ($ruleId) {
            $rule->load($ruleId);
            if (!$rule->getId()) {
                Mage::throwException(__('Please correct the reminder rule you requested.'));
            }
        }
        Mage::register('current_reminder_rule', $rule);
        return $rule;
    }

    /**
     * Rules list
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title(__('Email Reminders'));
        $this->loadLayout();
        $this->_setActiveMenu('Enterprise_Reminder::promo_reminder');
        $this->renderLayout();
    }

    /**
     * Create new rule
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit reminder rule
     */
    public function editAction()
    {
        $this->_title(__('Email Reminders'));

        try {
            $model = $this->_initRule();
        } catch (Magento_Core_Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($model->getId() ? $model->getName() : __('New Reminder Rule'));

        // set entered data if was error when we do save
        $data = Mage::getSingleton('Magento_Adminhtml_Model_Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');

        $this->_initAction();

        $this->getLayout()->getBlock('adminhtml_reminder_edit')
            ->setData('form_action_url', $this->getUrl('*/*/save'));

        $this->getLayout()->getBlock('head')
            ->setCanLoadExtJs(true)
            ->setCanLoadRulesJs(true);

        $caption = $model->getId() ? __('Edit Rule') : __('New Reminder Rule');
        $this->_addBreadcrumb($caption, $caption)->renderLayout();
    }

    /**
     * Add new condition
     */
    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('Enterprise_Reminder_Model_Rule'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Magento_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    /**
     * Save reminder rule
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            try {
                $redirectBack = $this->getRequest()->getParam('back', false);

                $model = $this->_initRule('rule_id');

                $data = $this->_filterDates($data, array('from_date', 'to_date'));

                $validateResult = $model->validateData(new Magento_Object($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->_getSession()->addError($errorMessage);
                    }
                    $this->_getSession()->setFormData($data);

                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }

                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);


                $model->loadPost($data);
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->setPageData($model->getData());
                $model->save();

                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(__('You saved the reminder rule.'));
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->setPageData(false);

                if ($redirectBack) {
                    $this->_redirect('*/*/edit', array(
                        'id'       => $model->getId(),
                        '_current' => true,
                    ));
                    return;
                }

            } catch (Magento_Core_Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__('We could not save the reminder rule.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete reminder rule
     */
    public function deleteAction()
    {
        try {
            $model = $this->_initRule();
            $model->delete();
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(__('You deleted the reminder rule.'));
        }
        catch (Magento_Core_Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $model->getId()));
            return;
        } catch (Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__('We could not delete the reminder rule.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/');
    }

    /**
     * Match reminder rule and send emails for matched customers
     */
    public function runAction()
    {
        try {
            $model = $this->_initRule();
            $model->sendReminderEmails();
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(__('You matched the reminder rule.'));
        } catch (Magento_Core_Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addException($e, __('Reminder rule matching error.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/edit', array('id' => $model->getId(), 'active_tab' => 'matched_customers'));
    }

    /**
     *  Customer grid ajax action
     */
    public function customerGridAction()
    {
        if ($this->_initRule('rule_id')) {
            $block = $this->getLayout()->createBlock('Enterprise_Reminder_Block_Adminhtml_Reminder_Edit_Tab_Customers');
            $this->getResponse()->setBody($block->toHtml());
        }
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Enterprise_Reminder::enterprise_reminder') &&
            $this->_objectManager->get('Enterprise_Reminder_Helper_Data')->isEnabled();
    }
}
