<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Forward controller
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Controller_Varien_Action_Forward implements Mage_Core_Controller_Varien_DispatchableInterface
{
    /**
     * Request object
     *
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * Response object
     *
     * @var Zend_Controller_Response_Abstract
     */
    protected $_response;

    /**
     * Currently used area
     *
     * @var string
     */
    protected $_currentArea;

    /**
     * Constructor
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param array $invokeArgs
     */
    public function __construct(Zend_Controller_Request_Abstract $request,
        Zend_Controller_Response_Abstract $response, array $invokeArgs = array()
    ) {
        $this->_request  = $request;
        $this->_response = $response;
        if (false == isset($this->_currentArea)) {
            $this->_currentArea = isset($invokeArgs['areaCode']) ? $invokeArgs['areaCode'] : null;
        }
    }

    /**
     * Dispatch controller action
     *
     * @param string $action action name
     * @return void
     */
    public function dispatch($action)
    {

    }

    /**
     * @return Zend_Controller_Request_Abstract
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return Zend_Controller_Response_Abstract
     */
    public function getResponse()
    {
        return $this->_response;
    }
}
