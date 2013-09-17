<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_TargetRule_Controller_Adminhtml_Targetrule extends Magento_Adminhtml_Controller_Action
{

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Initial actions
     *
     * @return unknown
     */
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('Magento_TargetRule::catalog_targetrule');
        return $this;
    }

    /**
     * Index grid
     *
     */
    public function indexAction()
    {
        $this->_title(__('Related Products Rules'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Grid ajax action
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Create new target rule
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit action
     *
     */
    public function editAction()
    {
        $this->_title(__('Related Products Rule'));

        /* @var $model Magento_TargetRule_Model_Rule */
        $model  = $this->_objectManager->create('Magento_TargetRule_Model_Rule');
        $ruleId = $this->getRequest()->getParam('id', null);

        if ($ruleId) {
            $model->load($ruleId);
            if (!$model->getId()) {
                $this->_getSession()->addError(__('This rule no longer exists.'));
                $this->_redirect('*/*');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getName() : __('New Related Products Rule'));

        $data = $this->_objectManager->get('Magento_Adminhtml_Model_Session')->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_coreRegistry->register('current_target_rule', $model);

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Ajax conditions
     *
     */
    public function newConditionHtmlAction()
    {
        $this->conditionsHtmlAction('conditions');
    }

    public function newActionsHtmlAction()
    {
        $this->conditionsHtmlAction('actions');
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $redirectPath   = '*/*/';
        $redirectParams = array();

        $data = $this->getRequest()->getPost();

        if ($this->getRequest()->isPost() && $data) {
            /* @var $model Magento_TargetRule_Model_Rule */
            $model          = $this->_objectManager->create('Magento_TargetRule_Model_Rule');
            $redirectBack   = $this->getRequest()->getParam('back', false);
            $hasError       = false;

            try {
                $data = $this->_filterDates($data, array('from_date', 'to_date'));
                $ruleId = $this->getRequest()->getParam('rule_id');
                if ($ruleId) {
                    $model->load($ruleId);
                    if ($ruleId != $model->getId()) {
                        throw new Magento_Core_Exception(__('Please specify a correct rule.'));
                    }
                }

                $validateResult = $model->validateData(new Magento_Object($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->_getSession()->addError($errorMessage);
                    }
                    $this->_getSession()->setFormData($data);

                    $this->_redirect('*/*/edit', array('id'=>$model->getId()));
                    return;
                }

                $data['conditions'] = $data['rule']['conditions'];
                $data['actions']    = $data['rule']['actions'];
                unset($data['rule']);

                $model->loadPost($data);
                $model->save();

                $this->_getSession()->addSuccess(
                    __('You saved the rule.')
                );

                if ($redirectBack) {
                    $this->_redirect('*/*/edit', array(
                        'id'       => $model->getId(),
                        '_current' => true,
                    ));
                    return;
                }
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $hasError = true;
            } catch (Zend_Date_Exception $e) {
                $this->_getSession()->addError(__('Invalid date.'));
                $hasError = true;
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    __('An error occurred while saving Product Rule.')
                );

                $this->_objectManager->get('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
                $this->_objectManager->get('Magento_Adminhtml_Model_Session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
                return;
            }

            if ($hasError) {
                $this->_getSession()->setFormData($data);
            }

            if ($hasError || $redirectBack) {
                $redirectPath = '*/*/edit';
                $redirectParams['id'] = $model->getId();
            }
        }
        $this->_redirect($redirectPath, $redirectParams);
    }

    /**
     * Delete target rule
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = $this->_objectManager->create('Magento_TargetRule_Model_Rule');
                $model->load($id);
                $model->delete();
                $this->_objectManager->get('Magento_Adminhtml_Model_Session')
                    ->addSuccess(__('You deleted the rule.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                $this->_objectManager->get('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_objectManager->get('Magento_Adminhtml_Model_Session')
            ->addError(__("We can't find a page to delete."));
        $this->_redirect('*/*/');
    }

    /**
     * Generate elements for condition forms
     *
     * @param string $prefix Form prefix
     */
    protected function conditionsHtmlAction($prefix)
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule($this->_objectManager->create('Magento_TargetRule_Model_Rule'))
            ->setPrefix($prefix);
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
     * Check is allowed access to targeted product rules management
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_TargetRule::targetrule');
    }

}
