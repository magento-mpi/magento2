<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax rule controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Controller\Adminhtml;

use Magento\Backend\App\Action;

class Rule extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /** @var \Magento\Tax\Service\V1\TaxRuleServiceInterface */
    protected $ruleService;

    /** @var \Magento\Tax\Service\V1\Data\TaxRuleBuilder */
    protected $ruleBuilder;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Tax\Service\V1\TaxRuleServiceInterface $ruleService
     * @param \Magento\Tax\Service\V1\Data\TaxRuleBuilder $ruleBuilder
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Tax\Service\V1\TaxRuleServiceInterface $ruleService,
        \Magento\Tax\Service\V1\Data\TaxRuleBuilder $ruleBuilder
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->ruleService = $ruleService;
        $this->ruleBuilder = $ruleBuilder;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return $this
     */
    public function indexAction()
    {
        $this->_title->add(__('Tax Rules'));
        $this->_initAction();
        $this->_view->renderLayout();

        return $this;
    }

    /**
     * Redirects to edit action
     *
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit action
     *
     * @return void
     */
    public function editAction()
    {
        $this->_title->add(__('Tax Rules'));

        $taxRuleId = $this->getRequest()->getParam('rule');
        $this->_coreRegistry->register('tax_rule_id', $taxRuleId);
        /** @var \Magento\Backend\Model\Session $backendSession */
        $backendSession = $this->_objectManager->get('Magento\Backend\Model\Session');
        if ($taxRuleId) {
            try {
                $taxRule = $this->ruleService->getTaxRule($taxRuleId);
                $pageTitle = sprintf("%s", $taxRule->getCode());
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $backendSession->unsRuleData();
                $this->messageManager->addError(__('This rule no longer exists.'));
                $this->_redirect('tax/*/');
                return;
            }
        } else {
            $pageTitle = __('New Tax Rule');
        }
        $this->_title->add($pageTitle);
        $data = $backendSession->getRuleData(true);
        if (!empty($data)) {
            $this->_coreRegistry->register('tax_rule_form_data', $data);
        }
        $breadcrumb = $taxRuleId ? __('Edit Rule') : __('New Rule');
        $this->_initAction()->_addBreadcrumb($breadcrumb, $breadcrumb);
        $this->_view->renderLayout();
    }

    /**
     * Save action
     *
     * @return void
     */
    public function saveAction()
    {
        $postData = $this->getRequest()->getPost();
        if ($postData) {
            $postData['calculate_subtotal'] = $this->getRequest()->getParam('calculate_subtotal', 0);
            $taxRule = $this->populateTaxRule($postData);
            try {
                if ($taxRule->getId()) {
                    $this->ruleService->updateTaxRule($taxRule);
                } else {
                    $taxRule = $this->ruleService->createTaxRule($taxRule);
                }

                $this->messageManager->addSuccess(__('The tax rule has been saved.'));

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('tax/*/edit', array('rule' => $taxRule->getId()));
                    return;
                }

                $this->_redirect('tax/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong saving this tax rule.'));
            }

            $this->_objectManager->get('Magento\Backend\Model\Session')->setRuleData($postData);
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
            return;
        }
        $this->getResponse()->setRedirect($this->getUrl('tax/rule'));
    }

    /**
     * Delete action
     *
     * @return void
     */
    public function deleteAction()
    {
        $ruleId = (int)$this->getRequest()->getParam('rule');
        try {
            $this->ruleService->deleteTaxRule($ruleId);
            $this->messageManager->addSuccess(__('The tax rule has been deleted.'));
            $this->_redirect('tax/*/');
            return;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->messageManager->addError(__('This rule no longer exists.'));
            $this->_redirect('tax/*/');
            return;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Something went wrong deleting this tax rule.'));
        }

        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
    }

    /**
     * Initialize action
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magento_Tax::sales_tax_rules'
        )->_addBreadcrumb(
            __('Tax'),
            __('Tax')
        )->_addBreadcrumb(
            __('Tax Rules'),
            __('Tax Rules')
        );
        return $this;
    }

    /**
     * Check if sales rule is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Tax::manage_tax');
    }

    /**
     * Initialize tax rule service object with form data.
     *
     * @param array $postData
     * @return \Magento\Tax\Service\V1\Data\TaxRule
     */
    protected function populateTaxRule($postData)
    {
        if (isset($postData['tax_calculation_rule_id'])) {
            $this->ruleBuilder->setId($postData['tax_calculation_rule_id']);
        }
        if (isset($postData['code'])) {
            $this->ruleBuilder->setCode($postData['code']);
        }
        if (isset($postData['tax_rate'])) {
            $this->ruleBuilder->setTaxRateIds($postData['tax_rate']);
        }
        if (isset($postData['tax_customer_class'])) {
            $this->ruleBuilder->setCustomerTaxClassIds($postData['tax_customer_class']);
        }
        if (isset($postData['tax_product_class'])) {
            $this->ruleBuilder->setProductTaxClassIds($postData['tax_product_class']);
        }
        if (isset($postData['priority'])) {
            $this->ruleBuilder->setPriority($postData['priority']);
        }
        if (isset($postData['calculate_subtotal'])) {
            $this->ruleBuilder->setCalculateSubtotal($postData['calculate_subtotal']);
        }
        if (isset($postData['position'])) {
            $this->ruleBuilder->setSortOrder($postData['position']);
        }
        return $this->ruleBuilder->create();
    }
}
