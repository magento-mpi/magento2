<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice api2 abstract
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Server
{
    /**#@+
     * Api2 types
     */
    const API_TYPE_REST = 'rest';
    const API_TYPE_SOAP = 'soap';
    /**#@-*/

    /**#@+
     * HTTP Response Codes
     */
    const HTTP_OK             = 200;
    const HTTP_BAD_REQUEST    = 400;
    const HTTP_UNAUTHORIZED   = 401;
    const HTTP_FORBIDDEN      = 403;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_INTERNAL_ERROR = 500;
    /**#@- */

    /**
     * Run server, the only public method of the server
     *
     * @return void
     */
    public function run()
    {
        /** @var $request Mage_Api2_Model_Request */
        $request = Mage::getSingleton('api2/request');
        /** @var $response Mage_Api2_Model_Response */
        $response = Mage::getSingleton('api2/response');

        try {
            $this->_authenticate($request)
                ->_route($request)
                ->_allow($request)
                ->_dispatch($request, $response);
        } catch (Exception $e) {
            $this->_catchCritical($request, $response, $e);
        }

        $response->sendResponse();
    }

    /**
     * Authenticate api2 user access key by OAuth module
     *
     * @param Mage_Api2_Model_Request $request
     * @throws Mage_Api2_Exception
     * @return Mage_Api2_Model_Server
     */
    protected function _authenticate(Mage_Api2_Model_Request $request)
    {
        $accessKey = $request->getAccessKey();

        /** @var $authManager Mage_Api2_Model_Auth */
        $authManager = Mage::getModel('api2/auth');
        if (!$authManager->authenticate($accessKey)) {
            throw new Mage_Api2_Exception('Session expired or invalid.', self::HTTP_UNAUTHORIZED);
        }

        return $this;
    }

    /**
     * Set all routes of the given api type to Route object
     * Find route that match current URL, set parameters of the route to Request object
     *
     * @param Mage_Api2_Model_Request $request
     * @return Mage_Api2_Model_Server
     */
    protected function _route(Mage_Api2_Model_Request $request)
    {
        $apiType = $request->getApiType();

        /** @var $router Mage_Api2_Model_Router */
        $router = Mage::getModel('api2/router');
        $router->setRoutes($this->_getConfig()->getRoutes($apiType))->route($request);

        return $this;
    }

    protected function _allow(Mage_Api2_Model_Request $request)
    {
        $accessKey = $request->getAccessKey();
        $resourceType = $request->getResourceType();
        $operation = $request->getOperation();

        /** @var $globalAcl Mage_Api2_Model_Acl_Global */
        $globalAcl = Mage::getModel('api2/acl_global');
        $isAllowed = $globalAcl->isAllowed($accessKey, $resourceType, $operation);

        if (!$isAllowed) {
            throw new Mage_Api2_Exception('Authorization error.', self::HTTP_FORBIDDEN);
        }

        return $this;
    }

    /**
     * Load class file, instantiate resource class, set parameters to the instance, run resource internal dispatch
     * method
     *
     * @param Mage_Api2_Model_Request $request
     * @param Mage_Api2_Model_Response $response
     * @return Zend_Controller_Response_Http
     */
    protected function _dispatch(Mage_Api2_Model_Request $request, Mage_Api2_Model_Response $response)
    {
        /** @var $dispatcher Mage_Api2_Model_Dispatcher */
        $dispatcher = Mage::getModel('api2/dispatcher');
        $dispatcher->dispatch($request, $response);
        
        return $response;
    }

    /**
     * Get api2 config instance
     *
     * @return Mage_Api2_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getModel('api2/config');
    }

    /**
     * Process thrown exception
     * Generate and set HTTP response code, error message to Response object
     *
     * @param Mage_Api2_Model_Request $request
     * @param Zend_Controller_Response_Http $response
     * @param Exception $critical
     * @return Mage_Api2_Model_Server
     */
    protected function _catchCritical(Mage_Api2_Model_Request $request, Zend_Controller_Response_Http $response,
        Exception $critical)
    {
        //if developer mode is set $critical can be without a Code, it will result in a
        //Zend_Controller_Response_Exception('Invalid HTTP response code')

        //if exception is not Mage_Api2_Exception we can't be sure it contains valid HTTP error code, so we change it to 500;
        $code = ($critical instanceof Mage_Api2_Exception) ? $critical->getCode() : self::HTTP_INTERNAL_ERROR;

        try {
            //add last error to stack and get the stack
            $response->setException($critical);
            $exceptions = $response->getException();

            //render content
            $renderer = Mage_Api2_Model_Renderer::factory($request);
            $errorContent = $renderer->renderErrors($code, $exceptions);

            //set HTTP Code of last error, Content-Type and Body
            $response->setHttpResponseCode($code);
            $response->setHeader('Content-Type',
                sprintf('%s; charset=%s', $renderer->getMimeType(), $request->getAcceptCharset())
            );
            $response->setBody($errorContent);
        } catch (Exception $e) {
            
            //tunnelling of 406(Not acceptable) error
            $status = ($e->getCode()==self::HTTP_NOT_ACCEPTABLE)    //$e->getCode() can result in one more loop
                    ?self::HTTP_NOT_ACCEPTABLE                      // of try..catch
                    :self::HTTP_INTERNAL_ERROR;
            
            //if error appeared in "error rendering" process then show it in plain text
            $response->setHttpResponseCode($status);
            $response->setHeader('Content-Type', 'text/plain; charset=UTF-8');
            $response->setBody($e->getMessage());
        }

        return $this;
    }
}
