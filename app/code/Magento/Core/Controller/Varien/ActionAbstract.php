<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract redirect/forward action class
 *
 * @category   Magento
 * @package    Magento_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Core_Controller_Varien_ActionAbstract
    implements Magento_Core_Controller_Varien_DispatchableInterface
{
    /**
     * @var Magento_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var Magento_Core_Controller_Response_Http
     */
    protected $_response;

    /**
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Core_Controller_Response_Http $response
     */
    public function __construct(
        Magento_Core_Controller_Request_Http $request,
        Magento_Core_Controller_Response_Http $response
    ) {
        $this->_request  = $request;
        $this->_response = $response;
    }

    /**
     * Retrieve request object
     *
     * @return Magento_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Retrieve response object
     *
     * @return Magento_Core_Controller_Response_Http
     */
    public function getResponse()
    {
        if (!$this->_response->getHeader('X-Frame-Options')) {
            $this->_response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        }
        return $this->_response;
    }

    /**
     * Retrieve full bane of current action current controller and
     * current module
     *
     * @param   string $delimiter
     * @return  string
     */
    public function getFullActionName($delimiter = '_')
    {
        return $this->getRequest()->getRequestedRouteName() . $delimiter .
            $this->getRequest()->getRequestedControllerName() . $delimiter .
            $this->getRequest()->getRequestedActionName();
    }
}
