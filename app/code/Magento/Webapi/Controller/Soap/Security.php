<?php
/**
 * Wrapper for SOAP security mechanisms: authentication and authorization.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Soap_Security
{
    const HEADER_SECURITY = 'Security';

    /**
     * List of headers passed in the request
     *
     * @var array
     */
    protected $_requestHeaders = array(self::HEADER_SECURITY);

    /**
     * WS-Security UsernameToken object from request.
     *
     * @var stdClass
     */
    protected $_usernameToken;

    /** @var Magento_Webapi_Helper_Data */
    protected $_helper;

    /** @var Magento_Webapi_Controller_Soap_Authentication */
    protected $_authentication;

    /** @var Magento_Webapi_Model_Authorization */
    protected $_authorization;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Helper_Data $helper
     * @param Magento_Webapi_Controller_Soap_Authentication $authentication
     * @param Magento_Webapi_Model_Authorization $authorization
     */
    public function __construct(
        Magento_Webapi_Helper_Data $helper,
        Magento_Webapi_Controller_Soap_Authentication $authentication,
        Magento_Webapi_Model_Authorization $authorization
    ) {
        $this->_helper = $helper;
        $this->_authentication = $authentication;
        $this->_authorization = $authorization;
    }

    /**
     * Check if current operation is authenticated and authorized.
     *
     * @param string $header SOAP operation name or security header
     * @param array $arguments Arguments that come from SOAP server along with header
     * @return bool If current header is security header - return false, otherwise return true
     * @throws Magento_Webapi_Exception In case when authentication failed
     * TODO: Remove warnings suppression below after authentication and authorization enabling
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function checkPermissions($header, $arguments)
    {
        // TODO: Uncomment authentication
        // if (is_null($this->_usernameToken)) {
        //     throw new Mage_Webapi_Exception(
        //         $this->_helper->__('WS-Security UsernameToken is not found in SOAP-request.'),
        //         Mage_Webapi_Exception::HTTP_UNAUTHORIZED
        //     );
        // }
        // $this->_authentication->authenticate($this->_usernameToken);
        // TODO: Enable authorization
        // $this->_authorization->checkServiceAcl($header, $method);
        return $this;
    }

    /**
     * Check if current header is SOAP security header
     *
     * @param string $header
     * @return bool
     */
    public function isSecurityHeader($header)
    {
        return in_array($header, $this->_requestHeaders) ? true : false;
    }

    /**
     * Process SOAP security header.
     *
     * @param string $header
     * @param array $arguments
     */
    public function processSecurityHeader($header, $arguments)
    {
        switch ($header) {
            case self::HEADER_SECURITY:
                foreach ($arguments as $argument) {
                    // @codingStandardsIgnoreStart
                    if (is_object($argument) && isset($argument->UsernameToken)) {
                        $this->_usernameToken = $argument->UsernameToken;
                    }
                    // @codingStandardsIgnoreEnd
                }
                break;
        }
    }

    /**
     * Set request headers
     *
     * @param array $requestHeaders
     */
    public function setRequestHeaders(array $requestHeaders)
    {
        $this->_requestHeaders = $requestHeaders;
    }
}
