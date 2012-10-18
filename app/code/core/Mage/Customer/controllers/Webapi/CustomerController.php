<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer REST API controller.
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Webapi_CustomerController extends Mage_Webapi_Controller_ActionAbstract
{
    public function __construct(Mage_Webapi_Controller_RequestAbstract $request,
        Mage_Webapi_Controller_Response $response, Mage_Core_Helper_Abstract $translationHelper = null
    ) {
        $translationHelper = $translationHelper ? $translationHelper : Mage::helper('Mage_Customer_Helper_Data');
        parent::__construct($request, $response, $translationHelper);
    }

    /**
     * Create customer.
     *
     * @param Mage_Customer_Webapi_Customer_DataStructure $data Customer create data.
     * @param string $optional may be not passed.
     * @return int ID of created customer
     * @throws Mage_Webapi_Exception
     */
    public function createV1(Mage_Customer_Webapi_Customer_DataStructure $data, $optional = null)
    {
        try {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('Mage_Customer_Model_Customer');
            $customer->setData(get_object_vars($data));
            $customer->save();
        } catch (Mage_Customer_Exception $e) {
            $this->_processException($e);
        } catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $this->_processException($e);
        }

        return $customer->getId();
    }

    /**
     * Get customers list.
     *
     * @return Mage_Customer_Webapi_Customer_DataStructure
     */
    public function listV1()
    {
        $data = $this->_getCollectionForRetrieve()->load()->toArray();
        return isset($data['items']) ? $data['items'] : $data;
    }

    /**
     * Update customer.
     *
     * @param int $id
     * @param Mage_Customer_Webapi_Customer_DataStructure $data
     * @throws Mage_Webapi_Exception
     */
    public function updateV1($id, Mage_Customer_Webapi_Customer_DataStructure $data)
    {
        try {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->_loadCustomerById($id);
            $customer->addData($data);
            $customer->save();
        } catch (Mage_Customer_Exception $e) {
            $this->_processException($e);
        }
    }

    /**
     * Retrieve information about customer. Add last logged in datetime.
     *
     * @param string $id
     * @return Mage_Customer_Webapi_Customer_DataStructure
     * @throws Mage_Webapi_Exception
     */
    public function getV1($id)
    {
        try {
            /** @var $log Mage_Log_Model_Customer */
            $log = Mage::getModel('Mage_Log_Model_Customer');
            $log->loadByCustomer($id);
            $data = $this->_get($id);
            $lastLoginAt = $log->getLoginAt();
            if (null !== $lastLoginAt) {
                $data['last_logged_in'] = $lastLoginAt;
            }
            $data['versioning_testing'] = 'Request was processed by ' . __METHOD__ . ' method.';
        } catch (Mage_Customer_Exception $e) {
            $this->_processException($e);
        }

        return $data;
    }

    /**
     * Method for versioning testing purposes.
     *
     * @param string $id
     * @return Mage_Customer_Webapi_Customer_DataStructure
     * @throws Mage_Webapi_Exception
     */
    public function getV2($id)
    {
        try {
            $customerData = $this->_get($id);
            $customerData['versioning_testing'] = 'Request was processed by ' . __METHOD__ . ' method.';
        } catch (Mage_Customer_Exception $e) {
            $this->_processException($e);
        }

        return $customerData;
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
