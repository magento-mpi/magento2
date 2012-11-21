<?php
/**
 * Customer REST API controller
 *
 * @copyright {}
 */
class Mage_Customer_Webapi_CustomerController extends Mage_Webapi_Controller_ActionAbstract
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Controller_Request_Factory $requestFactory
     * @param Mage_Webapi_Controller_Response $response
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Mage_Webapi_Controller_Request_Factory $requestFactory,
        Mage_Webapi_Controller_Response $response,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Magento_ObjectManager $objectManager
    ) {
        parent::__construct($requestFactory, $response, $helperFactory);
        $this->_translationHelper = $this->_helperFactory->get('Mage_Customer_Helper_Data');
        $this->_objectManager = $objectManager;
    }

    /**
     * Create customer.
     *
     * @param Mage_Customer_Model_Webapi_CustomerData $data Customer create data.
     * @return Mage_Customer_Model_Customer created customer
     * @throws Mage_Webapi_Exception
     */
    public function createV1(Mage_Customer_Model_Webapi_CustomerData $data)
    {
        try {
            $customerService = $this->_prepareService();
            return $customerService->create(get_object_vars($data), array());
        } catch (Magento_Validator_Exception $e) {
            $this->_processException($e);
        }
    }

    /**
     * Get customer service layer with initialized parameters
     *
     * @return Mage_Customer_Service_Customer|mixed
     */
    protected function _prepareService()
    {
        /** @var Mage_Customer_Service_Customer $customerService */
        $customerService = $this->_objectManager->get('Mage_Customer_Service_Customer');
        $customerService->setIsAdminStore(true);
        $customerService->setBeforeSaveCallback($this->_getBeforeSaveCallback());
        $customerService->setAfterSaveCallback($this->_getAfterSaveCallback());
        return $customerService;
    }

    /**
     * Get closure of dispatcher before customer save
     *
     * @return closure
     */
    protected function _getBeforeSaveCallback()
    {
        $request = $this->getRequest();
        /** @var Mage_Core_Model_Event_Manager $eventManager */
        $eventManager = $this->_objectManager->get('Mage_Core_Model_Event_Manager');
        return function ($customer) use ($request, $eventManager) {
            $eventManager->dispatch('webapi_customer_prepare_save', array(
                'customer'  => $customer,
                'request'   => $request
            ));
        };
    }

    /**
     * Get closure of dispatcher after customer save
     *
     * @return closure
     */
    protected function _getAfterSaveCallback()
    {
        $request = $this->getRequest();
        /** @var Mage_Core_Model_Event_Manager $eventManager */
        $eventManager = $this->_objectManager->get('Mage_Core_Model_Event_Manager');
        return function ($customer) use ($request, $eventManager) {
            $eventManager->dispatch('webapi_customer_save_after', array(
                'customer' => $customer,
                'request'  => $request
            ));
        };
    }

    /**
     * Get customers list.
     *
     * @return Mage_Customer_Model_Webapi_CustomerData[] array of customer data objects
     */
    public function listV1()
    {
        $result = array();
        $customersData = $this->_getCollectionForRetrieve()->load()->toArray();
        $customersData = isset($customersData['items']) ? $customersData['items'] : $customersData;
        foreach ($customersData as $customerData) {
            $customerData['balance'] = rand(0, 100);
            $result[] = $this->_createCustomerDataObject($customerData);
        }
        return $result;
    }

    /**
     * Update customer.
     *
     * @param int $customerId
     * @param Mage_Customer_Model_Webapi_CustomerData $data
     * @throws Mage_Webapi_Exception
     */
    public function updateV1($customerId, Mage_Customer_Model_Webapi_CustomerData $data)
    {
        try {
            $customerService = $this->_prepareService();
            // todo: drop next string as soon as front controller of webapi will be able to return routers
            $customerService->setSendRemainderEmail(false);
            $customerService->update($customerId, get_object_vars($data), array());
        } catch (Mage_Customer_Exception $e) {
            $this->_processException($e);
        }
    }

    /**
     * Retrieve information about customer. Add last logged in datetime.
     *
     * @param int $customerId
     * @return Mage_Customer_Model_Webapi_CustomerData
     * @throws Mage_Webapi_Exception
     */
    public function getV1($customerId)
    {
        try {
            /** @var $log Mage_Log_Model_Customer */
            $log = Mage::getModel('Mage_Log_Model_Customer');
            $log->loadByCustomer($customerId);
            $data = $this->_get($customerId);
            $lastLoginAt = $log->getLoginAt();
            if (null !== $lastLoginAt) {
                $data['last_logged_in'] = $lastLoginAt;
            }
            $data['password'] = $data['password_hash'];
            $data['versioning_testing'] = 'Request was processed by ' . __METHOD__ . ' method.';
            return $this->_createCustomerDataObject($data);
        } catch (Mage_Customer_Exception $e) {
            $this->_processException($e);
        }
    }

    /**
     * Create customer data object based on associative array of customer data.
     *
     * @param array $data
     * @return Mage_Customer_Model_Webapi_CustomerData
     */
    protected function _createCustomerDataObject($data)
    {
        $customerData = new Mage_Customer_Model_Webapi_CustomerData();
        foreach ($data as $field => $value) {
            $customerData->$field = $value;
        }
        return $customerData;
    }

    /**
     * Method for versioning testing purposes.
     *
     * @param string $id
     * @param int $newParam
     * @return Mage_Customer_Model_Webapi_CustomerData
     * @throws Mage_Webapi_Exception
     */
    public function getV2($id, $newParam = null)
    {
        try {
            $customerData = $this->_get($id);
            $customerData['versioning_testing'] = 'Request was processed by ' . __METHOD__ . ' method.';
        } catch (Mage_Customer_Exception $e) {
            $this->_processException($e);
        }

        return $this->_createCustomerDataObject($customerData);
    }

    /**
     * Retrieve customer data by ID
     *
     * @param string $id
     * @return mixed
     */
    protected function _get($id)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $this->_loadCustomerById($id);
        $data = $customer->getData();
        $data['is_confirmed'] = (int)!(isset($data['confirmation']) && $data['confirmation']);
        $data['balance'] = rand(0, 100);
        return $data;
    }

    /**
     * Delete customer.
     *
     * @param string $id
     * @throws Mage_Webapi_Exception
     */
    public function deleteV1($id)
    {
        try {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->_loadCustomerById($id);
            $customer->delete();
        } catch (Mage_Customer_Exception $e) {
            $this->_processException($e);
        }
    }

    /**
     * Load customer by id.
     *
     * @param int $id
     * @return Mage_Customer_Model_Customer
     * @throws Mage_Webapi_Exception
     */
    protected function _loadCustomerById($id)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer')->load($id);
        if (!$customer->getId()) {
            throw new Mage_Webapi_Exception(
                $this->_translationHelper->__("Customer with id %s does not exist.", $id),
                Mage_Webapi_Exception::HTTP_NOT_FOUND
            );
        }
        return $customer;
    }

    /**
     * Retrieve collection instances
     *
     * @return Mage_Customer_Model_Resource_Customer_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        /** @var $collection Mage_Customer_Model_Resource_Customer_Collection */
        $collection = Mage::getResourceModel('Mage_Customer_Model_Resource_Customer_Collection');
        $this->_applyCollectionModifiers($collection);
        return $collection;
    }

    /**
     * Process models exceptions and convert them into Webapi bad request exception.
     *
     * @param Exception $e
     * @throws Mage_Webapi_Exception
     */
    protected function _processException($e)
    {
        throw new Mage_Webapi_Exception($e->getMessage(), Mage_Webapi_Exception::HTTP_BAD_REQUEST);
    }
}
