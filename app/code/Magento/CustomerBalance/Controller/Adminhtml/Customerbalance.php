<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Customer\Controller\RegistryConstants;

/**
 * Controller for Customer account -> Store Credit ajax tab and all its contents
 */
class Customerbalance extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\CustomerBalance\Model\Balance
     */
    protected $_balance;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\CustomerBalance\Model\Balance $balance
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\CustomerBalance\Model\Balance $balance,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_balanceFactory = $balance;
        $this->_customerFactory = $customerFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $this->_request = $request;
        if (!$this->_objectManager->get('Magento\CustomerBalance\Helper\Data')->isEnabled()) {
            if ($request->getActionName() != 'noroute') {
                $this->_forward('noroute');
                return $this->getResponse();
            }
        }
        return parent::dispatch($request);
    }

    /**
     * Customer balance form
     *
     * @return void
     */
    public function formAction()
    {
        $this->_initCustomer();
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Customer balance grid
     *
     * @return void
     */
    public function gridHistoryAction()
    {
        $this->_initCustomer();
        $this->_view->loadLayout();
        $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock(
                'Magento\CustomerBalance\Block\Adminhtml\Customer\Edit\Tab\Customerbalance\Balance\History\Grid'
            )->toHtml()
        );
    }

    /**
     * Delete orphan balances
     *
     * @return void
     */
    public function deleteOrphanBalancesAction()
    {
        $this->_balance->deleteBalancesByCustomerId((int)$this->getRequest()->getParam('id'));
        $this->_redirect('customer/index/edit/', array('_current' => true));
    }

    /**
     * Instantiate customer model
     *
     * @param string $idFieldName
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _initCustomer($idFieldName = 'id')
    {
        $customer = $this->_customerFactory->create()->load((int)$this->getRequest()->getParam($idFieldName));
        if (!$customer->getId()) {
            throw new \Magento\Framework\Model\Exception(__('Failed to initialize customer'));
        }
        $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER, $customer);
        $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customer->getId());

    }

    /**
     * Check is allowed customer management
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Customer::manage');
    }
}
