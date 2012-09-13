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
     * @return Mage_Customer_Model_Customer
     */
    public function createV1(array $data)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $customer->setData($data);

        try {
            $customer->save();
        } catch (Mage_Core_Exception $e) {
            $this->_error($e->getMessage(), Mage_Api2_Controller_Front_Rest::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_INTERNAL_ERROR);
        }

        return $customer;
    }

    /**
     * Get customers list
     *
     * @return array
     */
    public function multiGetV1()
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
    public function updateV1(array $data)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $this->_loadCustomerById($this->getRequest()->getParam('id'));
        $customer->addData($data);
        try {
            $customer->save();
        } catch (Mage_Core_Exception $e) {
            $this->_error($e->getMessage(), Mage_Api2_Controller_Front_Rest::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_INTERNAL_ERROR);
        }
    }


    /**
     * Retrieve information about customer
     * Add last logged in datetime
     *
     * @throws Mage_Api2_Exception
     * @return array
     */
    public function getV1($customerId)
    {
        /** @var $log Mage_Log_Model_Customer */
        $log = Mage::getModel('Mage_Log_Model_Customer');
        $log->loadByCustomer($customerId);

        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $this->_loadCustomerById($customerId);
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
    public function deleteV1()
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $this->_loadCustomerById($this->getRequest()->getParam('id'));

        try {
            $customer->delete();
        } catch (Mage_Core_Exception $e) {
            Mage::helper('Mage_Api2_Helper_Rest')->critical($e->getMessage(), Mage_Api2_Controller_Front_Rest::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_INTERNAL_ERROR);
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
            Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_NOT_FOUND);
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
        // TODO: Implement attributes list fetch based on specified action
//        $collection->addAttributeToSelect();

        $this->_applyCollectionModifiers($collection);
        return $collection;
    }
}
