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
 * Customer REST API controller
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
// TODO: Change base class
class Mage_Customer_Rest_IndexController extends Mage_Api2_Controller_Rest_ActionAbstract
{
    public function __construct(Zend_Controller_Request_Abstract $request,
                                Zend_Controller_Response_Abstract $response, array $invokeArgs = array()
    ) {
        $this->_request = $request;
        $this->_response = $response;

        // TODO: Move to heigher level in hierarchy
//        Mage::app()->getFrontController()->setAction($this);

        $this->_construct();
    }

    /**
     * Create customer
     *
     * @param array $data
     * @return string
     */
    protected function _create(array $data)
    {
        /** @var $validator Mage_Api2_Model_Resource_Validator_Eav */
        $validator = Mage::getResourceModel('Mage_Api2_Model_Resource_Validator_Eav', array('resource' => $this));

        $data = $validator->filter($data);
        if (!$validator->isValidData($data)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            Mage::helper('Mage_Rest_Helper_Data')->critical(Mage_Rest_Helper_Data::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $customer->setData($data);

        try {
            $customer->save();
        } catch (Mage_Core_Exception $e) {
            $this->_error($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            Mage::helper('Mage_Rest_Helper_Data')->critical(Mage_Rest_Helper_Data::RESOURCE_INTERNAL_ERROR);
        }

        return $this->_getLocation($customer);
    }

    /**
     * Get customers list
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $data = $this->_getCollectionForRetrieve()->load()->toArray();
        return isset($data['items']) ? $data['items'] : $data;
    }

    /**
     * Update customer
     *
     * @param array $data
     * @throws Mage_Api2_Exception
     */
    protected function _update(array $data)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $this->_loadCustomerById($this->getRequest()->getParam('id'));
        /** @var $validator Mage_Api2_Model_Resource_Validator_Eav */
        $validator = Mage::getResourceModel('Mage_Api2_Model_Resource_Validator_Eav', array('resource' => $this));

        $data = $validator->filter($data);

        unset($data['website_id']); // website is not allowed to change

        if (!$validator->isValidData($data, true)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            Mage::helper('Mage_Rest_Helper_Data')->critical(Mage_Rest_Helper_Data::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        $customer->addData($data);

        try {
            $customer->save();
        } catch (Mage_Core_Exception $e) {
            $this->_error($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            Mage::helper('Mage_Rest_Helper_Data')->critical(Mage_Rest_Helper_Data::RESOURCE_INTERNAL_ERROR);
        }
    }

    /**
     * Load customer by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_Customer_Model_Customer
     */
    protected function _loadCustomerById($id)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer')->load($id);
        if (!$customer->getId()) {
            Mage::helper('Mage_Rest_Helper_Data')->critical(Mage_Rest_Helper_Data::RESOURCE_NOT_FOUND);
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
        $collection->addAttributeToSelect(array_keys(
            $this->getAvailableAttributes($this->getUserType(), Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ)
        ));

        $this->_applyCollectionModifiers($collection);
        return $collection;
    }

    /**
     * Retrieve information about customer
     * Add last logged in datetime
     *
     * @throws Mage_Api2_Exception
     * @return array
     */
    protected function _retrieve()
    {
        /** @var $log Mage_Log_Model_Customer */
        $log = Mage::getModel('Mage_Log_Model_Customer');
        $log->loadByCustomer($this->getRequest()->getParam('id'));

        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $this->_loadCustomerById($this->getRequest()->getParam('id'));
        $data = $customer->getData();
        $data['is_confirmed'] = (int)!(isset($data['confirmation']) && $data['confirmation']);

        $lastLoginAt = $log->getLoginAt();
        if (null !== $lastLoginAt) {
            $data['last_logged_in'] = $lastLoginAt;
        }
        return $data;
    }

    /**
     * Delete customer
     */
    protected function _delete()
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $this->_loadCustomerById($this->getRequest()->getParam('id'));

        try {
            $customer->delete();
        } catch (Mage_Core_Exception $e) {
            Mage::helper('Mage_Rest_Helper_Data')->critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            Mage::helper('Mage_Rest_Helper_Data')->critical(Mage_Rest_Helper_Data::RESOURCE_INTERNAL_ERROR);
        }
    }
}
