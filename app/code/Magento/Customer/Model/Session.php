<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model;

use Magento\App\ResponseInterface;
use Magento\Customer\Model\Config\Share;
use Magento\Customer\Model\Resource\Customer as ResourceCustomer;
use Magento\Customer\Service\V1\Dto\Customer as CustomerDto;

/**
 * Customer session model
 */
class Session extends \Magento\Session\SessionManager
{
    /**
     * Customer object
     *
     * @var CustomerDto
     */
    protected $_customer;

    /**
     * Customer model
     *
     * @var Customer
     */
    protected $_customerModel;

    /**
     * Flag with customer id validations result
     *
     * @var bool|null
     */
    protected $_isCustomerIdChecked = null;

    /**
     * Customer data
     *
     * @var \Magento\Customer\Helper\Data|null
     */
    protected $_customerData = null;

    /**
     * Core url
     *
     * @var \Magento\Core\Helper\Url|null
     */
    protected $_coreUrl = null;

    /**
     * @var Share
     */
    protected $_configShare;

    /**
     * @var \Magento\Core\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Customer\Service\V1\CustomerServiceInterface
     */
    protected $_customerService;

    /**
     * @var  \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\UrlFactory
     */
    protected $_urlFactory;

    /**
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Core\Model\Store\StorageInterface
     */
    protected $_storeManager;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var \Magento\Customer\Service\V1\Dto\Customer
     */
    protected $_customerDataObject;

    /**
     * @var \Magento\Customer\Model\Converter
     */
    protected $_converter;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Session\SidResolverInterface $sidResolver
     * @param \Magento\Session\Config\ConfigInterface $sessionConfig
     * @param \Magento\Session\SaveHandlerInterface $saveHandler
     * @param \Magento\Session\ValidatorInterface $validator
     * @param \Magento\Session\StorageInterface $storage
     * @param Share $configShare
     * @param \Magento\Core\Helper\Url $coreUrl
     * @param \Magento\Customer\Helper\Data $customerData
     * @param ResourceCustomer $customerResource
     * @param CustomerFactory $customerFactory
     * @param \Magento\UrlFactory $urlFactory
     * @param \Magento\Core\Model\Session $session
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Converter $converter
     * @param \Magento\App\ResponseInterface $response
     * @param \Magento\Customer\Service\V1\CustomerServiceInterface $customerService
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
     * @param null $sessionName
     * @param array $data
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\Session\SidResolverInterface $sidResolver,
        \Magento\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Session\SaveHandlerInterface $saveHandler,
        \Magento\Session\ValidatorInterface $validator,
        \Magento\Session\StorageInterface $storage,
        Share $configShare,
        \Magento\Core\Helper\Url $coreUrl,
        \Magento\Customer\Helper\Data $customerData,
        ResourceCustomer $customerResource,
        CustomerFactory $customerFactory,
        \Magento\UrlFactory $urlFactory,
        \Magento\Core\Model\Session $session,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Converter $converter,
        \Magento\App\ResponseInterface $response,
        \Magento\Customer\Service\V1\CustomerServiceInterface $customerService,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService,
        $sessionName = null,
        array $data = array()
    ) {
        $this->_coreUrl = $coreUrl;
        $this->_customerData = $customerData;
        $this->_configShare = $configShare;
        $this->_customerResource = $customerResource;
        $this->_customerFactory = $customerFactory;
        $this->_urlFactory = $urlFactory;
        $this->_session = $session;
        $this->_customerService = $customerService;
        $this->_customerAccountService = $customerAccountService;
        $this->_eventManager = $eventManager;
        $this->_storeManager = $storeManager;
        $this->response = $response;
        parent::__construct($request, $sidResolver, $sessionConfig, $saveHandler, $validator, $storage);
        $this->start($sessionName);
        $this->_converter = $converter;
        $this->_eventManager->dispatch('customer_session_init', array('customer_session' => $this));
    }

    /**
     * Retrieve customer sharing configuration model
     *
     * @return Share
     */
    public function getCustomerConfigShare()
    {
        return $this->_configShare;
    }

    /**
     * Set customer object and setting customer id in session
     *
     * @param   CustomerDto $customer
     * @return  $this
     */
    public function setCustomerDto(CustomerDto $customer)
    {
        $this->_customer = $customer;
        $this->response->setVary('customer_group', $customer->getGroupId());
        $this->setCustomerId($customer->getCustomerId());
        return $this;
    }

    /**
     * Retrieve customer model object
     *
     * @deprecated
     * @return CustomerDto
     */
    public function getCustomerDto()
    {
        /*** XXX: shouldn't this be CustomerDto? ***/
        if ($this->_customer instanceof Customer) {
            return $this->_customer;
        }

        if ($this->getCustomerId()) {
            $this->_customer = $this->_customerService->getCustomer($this->getCustomerId());
        }

        return $this->_customer;
    }

    /**
     * Returns Customer data object with the customer information
     *
     * @return \Magento\Customer\Service\V1\Dto\Customer
     */
    public function getCustomerData()
    {
        /* TODO refactor this after all usages of the setCustomer is refactored */
        return $this->_converter->createCustomerFromModel($this->getCustomer());
    }

    /**
     * Set Customer data object with the customer information
     *
     * @param \Magento\Customer\Service\V1\Dto\Customer $customerData
     * @return $this
     */
    public function setCustomerData(\Magento\Customer\Service\V1\Dto\Customer $customerData)
    {
        $this->setId($customerData->getCustomerId());
        $this->_converter->updateCustomerModel($this->getCustomer(), $customerData);
        return $this;
    }


    /**
     * Set customer model and the customer id in session
     *
     * @param   Customer $customerModel
     * @return  $this
     */
    public function setCustomer(Customer $customerModel)
    {
        $this->_customerModel = $customerModel;
        $this->response->setVary('customer_group', $customerModel->getGroupId());
        $this->setCustomerId($customerModel->getId());
        if ((!$customerModel->isConfirmationRequired()) && $customerModel->getConfirmation()) {
            $customerModel->setConfirmation(null)->save();
        }

        return $this;
    }

    /**
     * Retrieve customer model object
     *
     * @return Customer
     * @deprecated use getCustomerId() instead
     */
    public function getCustomer()
    {
        if ($this->_customerModel === null) {
            $this->_customerModel = $this->_customerFactory->create()->load($this->getCustomerId());
        }

        return $this->_customerModel;
    }

    /**
     * Set customer id
     *
     * @param int|null $id
     * @return $this
     */
    public function setCustomerId($id)
    {
        $this->storage->setData('customer_id', $id);
        return $this;
    }

    /**
     * Retrieve customer id from current session
     *
     * @return int|null
     */
    public function getCustomerId()
    {

        if ($this->storage->getData('customer_id')) {
            return $this->storage->getData('customer_id');
        }
        return null;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->getCustomerId();
    }

    /**
     * @param int $customerId
     * @return $this
     */
    public function setId($customerId)
    {
        return $this->setCustomerId($customerId);
    }

    /**
     * Set customer group id
     *
     * @param int|null $id
     * @return $this
     */
    public function setCustomerGroupId($id)
    {
        $this->storage->setData('customer_group_id', $id);
        return $this;
    }

    /**
     * Get customer group id
     * If customer is not logged in system, 'not logged in' group id will be returned
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        if ($this->storage->getData('customer_group_id')) {
            return $this->storage->getData('customer_group_id');
        }
        if ($this->getCustomerDto()) {
            $customerGroupId = $this->getCustomerDto()->getGroupId();
            $this->setCustomerGroupId($customerGroupId);
            return $customerGroupId;
        }
        return Group::NOT_LOGGED_IN_ID;
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return (bool)$this->getCustomerId() && (bool)$this->checkCustomerId($this->getId());
    }

    /**
     * Check exists customer (light check)
     *
     * @param int $customerId
     * @return bool
     */
    public function checkCustomerId($customerId)
    {
        if ($this->_isCustomerIdChecked === $customerId) {
            return true;
        }

        try {
            $this->_customerService->getCustomer($customerId);
            $this->_isCustomerIdChecked = $customerId;
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Customer authorization
     *
     * @param   string $username
     * @param   string $password
     * @return  bool
     */
    public function login($username, $password)
    {
        try {
            $customer = $this->_customerAccountService->authenticate($username, $password);
            $this->setCustomerDtoAsLoggedIn($customer);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param Customer $customer
     * @return $this
     */
    public function setCustomerAsLoggedIn($customer)
    {
        $this->setCustomer($customer);
        $this->_eventManager->dispatch('customer_login', array('customer' => $customer));
        $this->regenerateId();
        return $this;
    }

    /**
     * @param CustomerDto $customer
     * @return $this
     */
    public function setCustomerDtoAsLoggedIn($customer)
    {
        $this->setCustomerDto($customer);
        $this->_eventManager->dispatch('customer_login', array('customer' => $this->getCustomer()));
        return $this;
    }

    /**
     * Authorization customer by identifier
     *
     * @param   int $customerId
     * @return  bool
     */
    public function loginById($customerId)
    {
        try {
            $customer = $this->_customerService->getCustomer($customerId);
            $this->setCustomerDtoAsLoggedIn($customer);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Logout customer
     *
     * @return $this
     */
    public function logout()
    {
        if ($this->isLoggedIn()) {
            $this->_eventManager->dispatch('customer_logout', array('customer' => $this->getCustomer()));
            $this->_logout();
        }
        return $this;
    }

    /**
     * Authenticate controller action by login customer
     *
     * @param   \Magento\App\Action\Action $action
     * @param   bool $loginUrl
     * @return  bool
     */
    public function authenticate(\Magento\App\Action\Action $action, $loginUrl = null)
    {
        if ($this->isLoggedIn()) {
            return true;
        }
        $this->setBeforeAuthUrl($this->_createUrl()->getUrl('*/*/*', array('_current' => true)));
        if (isset($loginUrl)) {
            $action->getResponse()->setRedirect($loginUrl);
        } else {
            $arguments = $this->_customerData->getLoginUrlParams();
            if ($this->_session->getCookieShouldBeReceived() && $this->_createUrl()->getUseSession()) {
                $arguments += array('_query' => array(
                    $this->sidResolver->getSessionIdQueryParam($this->_session) => $this->_session->getSessionId()
                ));
            }
            $action->getResponse()->setRedirect(
                $this->_createUrl()->getUrl(\Magento\Customer\Helper\Data::ROUTE_ACCOUNT_LOGIN, $arguments)
            );
        }

        return false;
    }

    /**
     * Set auth url
     *
     * @param string $key
     * @param string $url
     * @return $this
     */
    protected function _setAuthUrl($key, $url)
    {
        $url = $this->_coreUrl->removeRequestParam($url, $this->sidResolver->getSessionIdQueryParam($this));
        // Add correct session ID to URL if needed
        $url = $this->_createUrl()->getRebuiltUrl($url);
        return $this->storage->setData($key, $url);
    }

    /**
     * Logout without dispatching event
     *
     * @return $this
     */
    protected function _logout()
    {
        $this->_customer = null;
        $this->_customerModel = null;
        $this->setCustomerId(null);
        $this->setCustomerGroupId(\Magento\Customer\Service\V1\CustomerGroupServiceInterface::NOT_LOGGED_IN_ID);
        $this->destroy(array('clear_storage' => false));
        return $this;
    }

    /**
     * Set Before auth url
     *
     * @param string $url
     * @return $this
     */
    public function setBeforeAuthUrl($url)
    {
        return $this->_setAuthUrl('before_auth_url', $url);
    }

    /**
     * Set After auth url
     *
     * @param string $url
     * @return $this
     */
    public function setAfterAuthUrl($url)
    {
        return $this->_setAuthUrl('after_auth_url', $url);
    }

    /**
     * Reset core session hosts after reseting session ID
     *
     * @param bool $deleteOldSession
     * @return $this
     */
    public function regenerateId($deleteOldSession = true)
    {
        parent::regenerateId($deleteOldSession);
        $this->_cleanHosts();
        return $this;
    }

    /**
     * @return \Magento\UrlInterface
     */
    protected function _createUrl()
    {
        return $this->_urlFactory->create();
    }
}
