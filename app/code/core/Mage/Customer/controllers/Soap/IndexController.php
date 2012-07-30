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
// TODO: This controller is created for SOAP routing demonstration purposes. Implementation must be added before merge to master
class Mage_Customer_Soap_IndexController extends Mage_Core_Controller_Varien_Action
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

    public function create($customerData)
    {
        return 1;
    }

    public function info($customerId)
    {
        return array(
            'customer_id' => $customerId,
            'firstname' => 'aaa',
            'lastname'  => 'bbb',
        );
    }

    /**
     * Retrieve action method name
     *
     * @param string $action
     * @return string
     */
    public function getActionMethodName($action)
    {
        return $action;
    }
}
