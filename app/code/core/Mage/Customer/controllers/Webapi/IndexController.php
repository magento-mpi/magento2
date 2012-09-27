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
class Mage_Customer_Webapi_IndexController extends Mage_Webapi_Controller_ActionAbstract
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
     * @param array $data
     * @return Mage_Customer_Model_Customer
     */
    public function createV1(array $data)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $customer->setData($data);
        $customer->save();
        return $customer;
    }

    /**
     * Get customers list.
     *
     * @return array
     */
    public function listV1()
    {
        $data = $this->_getCollectionForRetrieve()->load()->toArray();
        return isset($data['items']) ? $data['items'] : $data;
    }

    /**
     * Update customer.
     *
     * @param string $id
     * @param array $data
     * @throws Mage_Webapi_Exception
     */
    public function updateV1($id, array $data)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $this->_loadCustomerById($id);
        $customer->addData($data);
        $customer->save();
    }

    /**
     * Retrieve information about customer. Add last logged in datetime.
     *
     * @param string $id
     * @throws Mage_Webapi_Exception
     * @return array
     */
    public function getV1($id)
    {
        /** @var $log Mage_Log_Model_Customer */
        $log = Mage::getModel('Mage_Log_Model_Customer');
        $log->loadByCustomer($id);
        $data = $this->_get($id);
        $lastLoginAt = $log->getLoginAt();
        if (null !== $lastLoginAt) {
            $data['last_logged_in'] = $lastLoginAt;
        }
        $data['versioning_testing'] = 'Request was processed by ' . __METHOD__ . ' method.';
        return $data;
    }

    /**
     * Method for versioning testing purposes.
     *
     * @param string $id
     * @return array
     */
    public function getV2($id)
    {
        $customerData = $this->_get($id);
        $customerData['versioning_testing'] = 'Request was processed by ' . __METHOD__ . ' method.';
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
     */
    public function deleteV1($id)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $this->_loadCustomerById($id);
        $customer->delete();
    }

    /**
     * Load customer by id.
     *
     * @param int $id
     * @throws Mage_Webapi_Exception
     * @return Mage_Customer_Model_Customer
     * @throws RuntimeException
     */
    protected function _loadCustomerById($id)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer')->load($id);
        if (!$customer->getId()) {
            throw new RuntimeException($this->_translationHelper->__("Customer with id %s does not exist.", $id),
                Mage_Webapi_Controller_FrontAbstract::EXCEPTION_CODE_RESOURCE_NOT_FOUND);
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
}
