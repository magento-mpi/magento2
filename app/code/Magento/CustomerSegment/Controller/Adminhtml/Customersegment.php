<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer segments grid and edit controller
 */
class Magento_CustomerSegment_Controller_Adminhtml_Customersegment extends Magento_Adminhtml_Controller_Action
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_CustomerSegment_Model_ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * @param Magento_CustomerSegment_Model_ConditionFactory $conditionFactory
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_CustomerSegment_Model_ConditionFactory $conditionFactory,
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_conditionFactory = $conditionFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Initialize proper segment model
     *
     * @param string $requestParam
     * @param bool $requireValidId
     * @throws Magento_Core_Exception
     * @return Magento_CustomerSegment_Model_Segment
     */
    protected function _initSegment($requestParam = 'id', $requireValidId = false)
    {
        $segmentId = $this->getRequest()->getParam($requestParam, 0);
        $segment = $this->_objectManager->create('Magento_CustomerSegment_Model_Segment');
        if ($segmentId || $requireValidId) {
            $segment->load($segmentId);
            if (!$segment->getId()) {
                throw new Magento_Core_Exception(__('You requested the wrong customer segment.'));
            }
        }
        $this->_coreRegistry->register('current_customer_segment', $segment);
        return $segment;
    }

    /**
     * Segments list
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title(__('Customer Segments'));

        $this->loadLayout();
        $this->_setActiveMenu('Magento_CustomerSegment::customer_customersegment');
        $this->renderLayout();
    }

    /**
     * Create new customer segment
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit customer segment
     */
    public function editAction()
    {
        $this->_title(__('Customer Segments'));

        try {
            $model = $this->_initSegment();
        } catch (Magento_Core_Exception $e) {
            $this->_session->addError($e->getMessage());
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($model->getId() ? $model->getName() : __('New Segment'));

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $model->getConditions()->setJsFormObject('segment_conditions_fieldset');

        $this->_initAction();

        $block =  $this->getLayout()->createBlock('Magento_CustomerSegment_Block_Adminhtml_Customersegment_Edit')
            ->setData('form_action_url', $this->getUrl('*/*/save'));

        $this->getLayout()->getBlock('head')
            ->setCanLoadExtJs(true)
            ->setCanLoadRulesJs(true);

        $this->_addBreadcrumb(
                $model->getId() ? __('Edit Segment') : __('New Segment'),
                $model->getId() ? __('Edit Segment') : __('New Segment'))
            ->_addContent($block)
            ->_addLeft(
                $this->getLayout()->createBlock('Magento_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tabs'))
            ->renderLayout();
    }

    /**
     * Match segment customers action
     */
    public function matchAction()
    {
        try {
            $model = $this->_initSegment();
            if ($model->getApplyTo() != Magento_CustomerSegment_Model_Segment::APPLY_TO_VISITORS) {
                $model->matchCustomers();
            }
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/');
            return;
        } catch (Exception $e) {
            $this->_getSession()->addException($e,
                __('Segment Customers matching error')
            );
            $this->_redirect('*/*/');
            return;
        }
        $this->_redirect('*/*/edit', array('id' => $model->getId(), 'active_tab' => 'customers_tab'));
    }

    /**
     * Init active menu and set breadcrumb
     *
     * @return Magento_CustomerSegment_Controller_Adminhtml_Customersegment
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_CustomerSegment::customer_customersegment')
            ->_addBreadcrumb(
                __('Segments'),
                __('Segments')
            );
        return $this;
    }

    /**
     * Add new condition
     */
    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $segment = $this->_objectManager->create('Magento_CustomerSegment_Model_Segment');
        $segment->setApplyTo((int) $this->getRequest()->getParam('apply_to'));

        $model = $this->_conditionFactory->create($type)
            ->setId($id)
            ->setType($type)
            ->setRule($segment)
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
     * Save customer segment
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            try {
                $redirectBack = $this->getRequest()->getParam('back', false);

                $model = $this->_initSegment('segment_id');

                // Sanitize apply_to property
                if (array_key_exists('apply_to', $data)) {
                    $data['apply_to'] = (int)$data['apply_to'];
                }

                $validateResult = $model->validateData(new Magento_Object($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->_getSession()->addError($errorMessage);
                    }
                    $this->_getSession()->setFormData($data);

                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }

                if (array_key_exists('rule', $data)){
                    $data['conditions'] = $data['rule']['conditions'];
                    unset($data['rule']);
                }

                $model->loadPost($data);
                $this->_session->setPageData($model->getData());
                $model->save();
                if ($model->getApplyTo() != Magento_CustomerSegment_Model_Segment::APPLY_TO_VISITORS) {
                    $model->matchCustomers();
                }

                $this->_session->addSuccess(__('You saved the segment.'));
                $this->_session->setPageData(false);

                if ($redirectBack) {
                    $this->_redirect('*/*/edit', array(
                        'id'       => $model->getId(),
                        '_current' => true,
                    ));
                    return;
                }

            } catch (Magento_Core_Exception $e) {
                $this->_session->addError($e->getMessage());
                $this->_session->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('segment_id')));
                return;
            } catch (Exception $e) {
                $this->_session->addError(__("We're unable to save the segment."));
                $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete customer segment
     */
    public function deleteAction()
    {
        try {
            $model = $this->_initSegment('id', true);
            $model->delete();
            $this->_session->addSuccess(__('You deleted the segment.'));
        } catch (Magento_Core_Exception $e) {
            $this->_session->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        } catch (Exception $e) {
            $this->_session->addError(__("We're unable to delete the segement."));
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        }
        $this->_redirect('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization
            ->isAllowed('Magento_CustomerSegment::customersegment')
            && $this->_objectManager->get('Magento_CustomerSegment_Helper_Data')->isEnabled();
    }

    /**
     * Date range chooser action
     */
    public function chooserDaterangeAction()
    {
        $block = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Promo_Widget_Chooser_Daterange');
        if ($block) {
            // set block data from request
            $block->setTargetElementId($this->getRequest()->getParam('value_element_id'));
            $selectedValues = $this->getRequest()->getParam('selected');
            if (!empty($selectedValues) && is_array($selectedValues) && 1 === count($selectedValues)) {
                $block->setRangeValue(array_shift($selectedValues));
            }

            $this->getResponse()->setBody($block->toHtml());
        }
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
     * Grid ajax action in chooser mode
     */
    public function chooserGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
