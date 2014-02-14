<?php
/**
 * Depersonalize customer session data
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Converter;

/**
 * Class DepersonalizePlugin
 */
class DepersonalizePlugin
{

    /**
     * @var \Magento\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var \Magento\Event\Manager
     */
    protected $eventManager;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $request;

    /**
     * @param \Magento\View\LayoutInterface $layout
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Event\Manager $eventManager
     * @param \Magento\App\RequestInterface $request
     */
    public function __construct(
        \Magento\View\LayoutInterface $layout,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Event\Manager $eventManager,
        \Magento\App\RequestInterface $request
    ) {
        $this->layout = $layout;
        $this->customer = $customerFactory->create();
        $this->request = $request;
        $this->eventManager = $eventManager;

    }

    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\Customer\Model\Customer|mixed
     */
    public function aroundGetCustomerModel(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        if (!$this->request->isAjax() && $this->layout->isCacheable()) {
            try{
                $customer = $invocationChain->proceed($arguments);
                $this->customer->setGroupId($customer->getGroupId());
            } catch(\Magento\Exception\NoSuchEntityException $e) {
                $customer = $this->customer;
            }
            $this->eventManager->dispatch('before_depersonalize_customer', array('customer' => $customer));
        } else {
            $customer = $invocationChain->proceed($arguments);
        }
        return $customer;
    }
}
