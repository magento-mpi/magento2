<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Adminhtml;

use Magento\Customer\Model\Address\Mapper;
use Magento\Customer\Service\V1\Data\CustomerBuilder;
use Magento\Customer\Service\V1\Data\AddressBuilder;
use Magento\Customer\Service\V1\Data\CustomerDetailsBuilder;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Customer\Service\V1\CustomerAddressServiceInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressDataBuilder;
use Magento\Framework\Message\Error;
use Magento\Customer\Controller\RegistryConstants;

/**
 * Class Index
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Validator
     */
    protected $_validator;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory = null;

    /** @var  CustomerBuilder */
    protected $_customerBuilder;

    /** @var  CustomerDetailsBuilder */
    protected $_customerDetailsBuilder;

    /** @var  AddressBuilder */
    protected $_addressBuilder;

    /** @var  AddressDataBuilder */
    protected $_addressDataBuilder;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $_addressFactory = null;

    /** @var \Magento\Newsletter\Model\SubscriberFactory */
    protected $_subscriberFactory;

    /**
     * @var \Magento\Customer\Helper\Data
     */
    protected $_dataHelper = null;

    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $_formFactory;

    /** @var CustomerAddressServiceInterface */
    protected $_addressService;

    /** @var CustomerAccountServiceInterface */
    protected $_customerAccountService;

    /** @var CustomerRepositoryInterface */
    protected $_customerRepository;

    /** @var  \Magento\Customer\Helper\View */
    protected $_viewHelper;

    /** @var \Magento\Framework\Math\Random */
    protected $_random;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $_extensibleDataObjectConverter;

    /**
     * @var Mapper
     */
    protected $addressMapper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Customer\Model\Metadata\FormFactory $formFactory
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param CustomerBuilder $customerBuilder
     * @param CustomerDetailsBuilder $customerDetailsBuilder
     * @param AddressBuilder $addressBuilder
     * @param AddressDataBuilder $addressDataBuilder
     * @param CustomerAddressServiceInterface $addressService
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $accountService
     * @param \Magento\Customer\Helper\View $viewHelper
     * @param \Magento\Customer\Helper\Data $helper
     * @param \Magento\Framework\Math\Random $random
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param Mapper $addressMapper
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\Metadata\FormFactory $formFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerBuilder $customerBuilder,
        CustomerDetailsBuilder $customerDetailsBuilder,
        AddressBuilder $addressBuilder,
        AddressDataBuilder $addressDataBuilder,
        CustomerAddressServiceInterface $addressService,
        CustomerAccountServiceInterface $accountService,
        \Magento\Customer\Helper\View $viewHelper,
        \Magento\Customer\Helper\Data $helper,
        \Magento\Framework\Math\Random $random,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        Mapper $addressMapper
    ) {
        $this->_fileFactory = $fileFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_customerFactory = $customerFactory;
        $this->_customerBuilder = $customerBuilder;
        $this->_customerDetailsBuilder = $customerDetailsBuilder;
        $this->_addressBuilder = $addressBuilder;
        $this->_addressDataBuilder = $addressDataBuilder;
        $this->_addressFactory = $addressFactory;
        $this->_subscriberFactory = $subscriberFactory;
        $this->_dataHelper = $helper;
        $this->_formFactory = $formFactory;
        $this->_addressService = $addressService;
        $this->_customerAccountService = $accountService;
        $this->_viewHelper = $viewHelper;
        $this->_random = $random;
        $this->_customerRepository = $customerRepository;
        $this->_extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->addressMapper = $addressMapper;
        parent::__construct($context);
    }

    /**
     * Customer initialization
     *
     * @param string $idFieldName
     * @return string customer id
     */
    protected function _initCustomer($idFieldName = 'id')
    {
        // Default title
        $this->_title->add(__('Customers'));

        $customerId = (int)$this->getRequest()->getParam($idFieldName);
        $customer = $this->_objectManager->create('Magento\Customer\Model\Customer');
        if ($customerId) {
            $customer->load($customerId);
            $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customerId);
        }

        // TODO: Investigate if any piece of code still relies on this; remove if not.
        $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER, $customer);
        return $customerId;
    }

    /**
     * Add errors messages to session.
     *
     * @param array|string $messages
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _addSessionErrorMessages($messages)
    {
        $messages = (array)$messages;
        $session = $this->_getSession();

        $callback = function ($error) use ($session) {
            if (!$error instanceof Error) {
                $error = new Error($error);
            }
            $this->messageManager->addMessage($error);
        };
        array_walk_recursive($messages, $callback);
    }

    /**
     * Helper function that handles mass actions by taking in a callable for handling a single customer action.
     *
     * @param callable $singleAction A single action callable that takes a customer ID as input
     * @param int[] $customerIds Array of customer Ids to perform the action upon
     * @return int Number of customers successfully acted upon
     */
    protected function actUponMultipleCustomers(callable $singleAction, $customerIds)
    {
        if (!is_array($customerIds)) {
            $this->messageManager->addError(__('Please select customer(s).'));
            return 0;
        }
        $customersUpdated = 0;
        foreach ($customerIds as $customerId) {
            try {
                $singleAction($customerId);
                $customersUpdated++;
            } catch (\Exception $exception) {
                $this->messageManager->addError($exception->getMessage());
            }
        }
        return $customersUpdated;
    }

    /**
     * Customer access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Customer::manage');
    }
}
