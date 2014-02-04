<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Core\Model\Registry;

/**
 * Reminder grid and edit controller
 */
class Reminder extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * Remainder Rule Factory
     *
     * @var \Magento\Reminder\Model\RuleFactory
     */
    protected $_ruleFactory;

    /**
     * Rule Condition Factory
     *
     * @var \Magento\Reminder\Model\Rule\ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * @var \Magento\Core\Filter\Date
     */
    protected $_dateFilter;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param Registry $coreRegistry
     * @param \Magento\Reminder\Model\RuleFactory $ruleFactory
     * @param \Magento\Reminder\Model\Rule\ConditionFactory $conditionFactory
     * @param \Magento\Core\Filter\Date $dateFilter
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Registry $coreRegistry,
        \Magento\Reminder\Model\RuleFactory $ruleFactory,
        \Magento\Reminder\Model\Rule\ConditionFactory $conditionFactory,
        \Magento\Core\Filter\Date $dateFilter
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_ruleFactory = $ruleFactory;
        $this->_conditionFactory = $conditionFactory;
        $this->_dateFilter = $dateFilter;
    }

    /**
     * Init active menu and set breadcrumb
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Reminder::promo_reminder')
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
     * @return \Magento\Reminder\Model\Rule
     * @throws \Magento\Core\Exception
     */
    protected function _initRule($requestParam = 'id')
    {
        $ruleId = $this->getRequest()->getParam($requestParam, 0);
        $rule = $this->_ruleFactory->create();
        if ($ruleId) {
            $rule->load($ruleId);
            if (!$rule->getId()) {
                throw new \Magento\Core\Exception(__('Please correct the reminder rule you requested.'));
            }
        }
        $this->_coreRegistry->register('current_reminder_rule', $rule);
        return $rule;
    }

    /**
     * Rules list
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title->add(__('Email Reminders'));
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Reminder::promo_reminder');
        $this->_view->renderLayout();
    }

    /**
     * Create new rule
     *
     * @return void
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit reminder rule
     *
     * @return void
     */
    public function editAction()
    {
        $this->_title->add(__('Email Reminders'));

        try {
            $model = $this->_initRule();
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('adminhtml/*/');
            return;
        }

        $this->_title->add($model->getId() ? $model->getName() : __('New Reminder Rule'));

        // set entered data if was error when we do save
        $data = $this->_getSession()->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');

        $this->_initAction();

        $this->_view->getLayout()->getBlock('adminhtml_reminder_edit')
            ->setData('form_action_url', $this->getUrl('adminhtml/*/save'));

        $this->_view->getLayout()->getBlock('head')
            ->setCanLoadExtJs(true)
            ->setCanLoadRulesJs(true);

        $caption = $model->getId() ? __('Edit Rule') : __('New Reminder Rule');
        $this->_addBreadcrumb($caption, $caption);
        $this->_view->renderLayout();
    }

    /**
     * Add new condition
     *
     * @return void
     */
    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = $this->_conditionFactory->create($type)
            ->setId($id)
            ->setType($type)
            ->setRule($this->_ruleFactory->create())
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof \Magento\Rule\Model\Condition\AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    /**
     * Save reminder rule
     *
     * @return void
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            try {
                $redirectBack = $this->getRequest()->getParam('back', false);

                $model = $this->_initRule('rule_id');

                $inputFilter = new \Zend_Filter_Input(
                    array('from_date' => $this->_dateFilter, 'to_date' => $this->_dateFilter), array(), $data);
                $data = $inputFilter->getUnescaped();

                $validateResult = $model->validateData(new \Magento\Object($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addError($errorMessage);
                    }
                    $this->_getSession()->setFormData($data);

                    $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
                    return;
                }

                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);


                $model->loadPost($data);
                $this->_getSession()->setPageData($model->getData());
                $model->save();

                $this->messageManager->addSuccess(__('You saved the reminder rule.'));
                $this->_getSession()->setPageData(false);

                if ($redirectBack) {
                    $this->_redirect('adminhtml/*/edit', array(
                        'id'       => $model->getId(),
                        '_current' => true,
                    ));
                    return;
                }

            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setPageData($data);
                $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We could not save the reminder rule.'));
                $this->_objectManager->get('Magento\Logger')->logException($e);
            }
        }
        $this->_redirect('adminhtml/*/');
    }

    /**
     * Delete reminder rule
     *
     * @return void
     */
    public function deleteAction()
    {
        try {
            $model = $this->_initRule();
            $model->delete();
            $this->messageManager->addSuccess(__('You deleted the reminder rule.'));
        }
        catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We could not delete the reminder rule.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
        $this->_redirect('adminhtml/*/');
    }

    /**
     * Match reminder rule and send emails for matched customers
     *
     * @return void
     */
    public function runAction()
    {
        try {
            $model = $this->_initRule();
            $model->sendReminderEmails();
            $this->messageManager->addSuccess(__('You matched the reminder rule.'));
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Reminder rule matching error.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
        $this->_redirect('adminhtml/*/edit', array('id' => $model->getId(), 'active_tab' => 'matched_customers'));
    }

    /**
     *  Customer grid ajax action
     *
     * @return void
     */
    public function customerGridAction()
    {
        if ($this->_initRule('rule_id')) {
            $block = $this->_view->getLayout()->createBlock('Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab\Customers');
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
        return $this->_authorization->isAllowed('Magento_Reminder::magento_reminder') &&
            $this->_objectManager->get('Magento\Reminder\Helper\Data')->isEnabled();
    }
}
