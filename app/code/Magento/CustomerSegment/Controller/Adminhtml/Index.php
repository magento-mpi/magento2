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
namespace Magento\CustomerSegment\Controller\Adminhtml;

use Magento\Backend\App\Action;

class Index extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\CustomerSegment\Model\ConditionFactory
     */
    protected $_conditionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\CustomerSegment\Model\ConditionFactory $conditionFactory,
        \Magento\Core\Model\Registry $coreRegistry
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
     * @throws \Magento\Core\Exception
     * @return \Magento\CustomerSegment\Model\Segment
     */
    protected function _initSegment($requestParam = 'id', $requireValidId = false)
    {
        $segmentId = $this->getRequest()->getParam($requestParam, 0);
        $segment = $this->_objectManager->create('Magento\CustomerSegment\Model\Segment');
        if ($segmentId || $requireValidId) {
            $segment->load($segmentId);
            if (!$segment->getId()) {
                throw new \Magento\Core\Exception(__('You requested the wrong customer segment.'));
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
        $this->_title->add(__('Customer Segments'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_CustomerSegment::customer_customersegment');
        $this->_view->renderLayout();
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
        $this->_title->add(__('Customer Segments'));

        try {
            $model = $this->_initSegment();
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('customersegment/*/');
            return;
        }

        $this->_title->add($model->getId() ? $model->getName() : __('New Segment'));

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $model->getConditions()->setJsFormObject('segment_conditions_fieldset');

        $this->_initAction();

        $block =  $this->_view->getLayout()->createBlock('Magento\CustomerSegment\Block\Adminhtml\Customersegment\Edit')
            ->setData('form_action_url', $this->getUrl('customersegment/*/save'));

        $this->_view->getLayout()->getBlock('head')
            ->setCanLoadExtJs(true)
            ->setCanLoadRulesJs(true);

        $this->_addBreadcrumb(
                $model->getId() ? __('Edit Segment') : __('New Segment'),
                $model->getId() ? __('Edit Segment') : __('New Segment'))
            ->_addContent($block)
            ->_addLeft(
                $this->_view->getLayout()->createBlock(
                    'Magento\CustomerSegment\Block\Adminhtml\Customersegment\Edit\Tabs')
            );
        $this->_view->renderLayout();
    }

    /**
     * Match segment customers action
     */
    public function matchAction()
    {
        try {
            $model = $this->_initSegment();
            if ($model->getApplyTo() != \Magento\CustomerSegment\Model\Segment::APPLY_TO_VISITORS) {
                $model->matchCustomers();
            }
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('customersegment/*/');
            return;
        } catch (\Exception $e) {
            $this->messageManager->addException($e,
                __('Segment Customers matching error')
            );
            $this->_redirect('customersegment/*/');
            return;
        }
        $this->_redirect('customersegment/*/edit', array('id' => $model->getId(), 'active_tab' => 'customers_tab'));
    }

    /**
     * Init active menu and set breadcrumb
     *
     * @return \Magento\CustomerSegment\Controller\Adminhtml\Index
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_CustomerSegment::customer_customersegment')
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

        $segment = $this->_objectManager->create('Magento\CustomerSegment\Model\Segment');
        $segment->setApplyTo((int) $this->getRequest()->getParam('apply_to'));

        $model = $this->_conditionFactory->create($type)
            ->setId($id)
            ->setType($type)
            ->setRule($segment)
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

                $validateResult = $model->validateData(new \Magento\Object($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addError($errorMessage);
                    }
                    $this->_getSession()->setFormData($data);

                    $this->_redirect('customersegment/*/edit', array('id' => $model->getId()));
                    return;
                }

                if (array_key_exists('rule', $data)){
                    $data['conditions'] = $data['rule']['conditions'];
                    unset($data['rule']);
                }

                $model->loadPost($data);
                $this->_session->setPageData($model->getData());
                $model->save();
                if ($model->getApplyTo() != \Magento\CustomerSegment\Model\Segment::APPLY_TO_VISITORS) {
                    $model->matchCustomers();
                }

                $this->messageManager->addSuccess(__('You saved the segment.'));
                $this->_session->setPageData(false);

                if ($redirectBack) {
                    $this->_redirect('customersegment/*/edit', array(
                        'id'       => $model->getId(),
                        '_current' => true,
                    ));
                    return;
                }

            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_session->setPageData($data);
                $this->_redirect('customersegment/*/edit', array('id' => $this->getRequest()->getParam('segment_id')));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__("We're unable to save the segment."));
                $this->_objectManager->get('Magento\Logger')->logException($e);
            }
        }
        $this->_redirect('customersegment/*/');
    }

    /**
     * Delete customer segment
     */
    public function deleteAction()
    {
        try {
            $model = $this->_initSegment('id', true);
            $model->delete();
            $this->messageManager->addSuccess(__('You deleted the segment.'));
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('customersegment/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(__("We're unable to delete the segement."));
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
        $this->_redirect('customersegment/*/');
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
            && $this->_objectManager->get('Magento\CustomerSegment\Helper\Data')->isEnabled();
    }

    /**
     * Date range chooser action
     */
    public function chooserDaterangeAction()
    {
        $block = $this->_view->getLayout()->createBlock('Magento\CatalogRule\Block\Adminhtml\Promo\Widget\Chooser\Daterange');
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
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    /**
     * Grid ajax action in chooser mode
     */
    public function chooserGridAction()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
