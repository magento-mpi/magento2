<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Controller;

use Magento\Framework\App\RequestInterface;

/**
 * Customers newsletter subscription controller
 */
class Manage extends \Magento\Framework\App\Action\Action
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Core\App\Action\FormKeyValidator
     */
    protected $_formKeyValidator;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * @var \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder
     */
    protected $_customerDetailsBuilder;

    /**
     * @var \Magento\Customer\Api\Data\CustomerDataBuilder
     */
    protected $_customerBuilder;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
     * @param \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder
     * @param \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Core\App\Action\FormKeyValidator $formKeyValidator,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService,
        \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder,
        \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
        $this->_formKeyValidator = $formKeyValidator;
        $this->_customerSession = $customerSession;
        $this->_customerAccountService = $customerAccountService;
        $this->_customerDetailsBuilder = $customerDetailsBuilder;
        $this->_customerBuilder = $customerBuilder;
        $this->_subscriberFactory = $subscriberFactory;
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_customerSession->authenticate($this)) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }
}
