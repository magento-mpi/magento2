<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Controller for Customer account -> Store Credit ajax tab and all its contents
 *
 */
namespace Magento\CustomerBalance\Controller\Adminhtml;

use Magento\Backend\App\Action;

class Customerbalance extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
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
     * @param \Magento\CustomerBalance\Model\Balance $balance
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\CustomerBalance\Model\Balance $balance,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_balanceFactory = $balance;
        $this->_customerFactory = $customerFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @param \Magento\App\RequestInterface $request
     * @return $this|mixed
     */
    public function dispatch(\Magento\App\RequestInterface $request)
    {
        $this->_request = $request;
        if (!$this->_objectManager->get('Magento\CustomerBalance\Helper\Data')->isEnabled()) {
            if ($request->getActionName() != 'noroute') {
                return $this->_forward('noroute');
            }
        }
        return parent::dispatch($request);
    }

    /**
     * Customer balance form
     */
    public function formAction()
    {
        $this->_initCustomer();
        $this->_layoutServices->loadLayout();
        $this->renderLayout();
    }

    /**
     * Customer balance grid
     *
     */
    public function gridHistoryAction()
    {
        $this->_initCustomer();
        $this->_layoutServices->loadLayout();
        $this->getResponse()->setBody(
            $this->_layoutServices->getLayout()->createBlock(
                'Magento\CustomerBalance\Block\Adminhtml\Customer\Edit\Tab\Customerbalance\Balance\History\Grid'
            )->toHtml()
        );
    }

    /**
     * Delete orphan balances
     *
     */
    public function deleteOrphanBalancesAction()
    {
        $this->_balance->deleteBalancesByCustomerId(
            (int)$this->getRequest()->getParam('id')
        );
        $this->_redirect('customer/index/edit/', array('_current' => true));
    }

    /**
     * Instantiate customer model
     *
     * @param string $idFieldName
     * @throws \Magento\Core\Exception
     */
    protected function _initCustomer($idFieldName = 'id')
    {
        $customer = $this->_customerFactory->create()->load((int)$this->getRequest()->getParam($idFieldName));
        if (!$customer->getId()) {
            throw new \Magento\Core\Exception(__('Failed to initialize customer'));
        }
        $this->_coreRegistry->register('current_customer', $customer);
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
