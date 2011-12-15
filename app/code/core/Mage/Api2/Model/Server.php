<?php


class Mage_Api2_Model_Server
{
    const API_TYPE_REST = 'rest';
    const API_TYPE_SOAP = 'soap';

    /**
     * Run server, the only public method of the server
     */
    public function run()
    {
        /** @var $request Mage_Api2_Model_Request */
        $request = Mage::getSingleton('api2/request');
        /** @var $response Mage_Api2_Model_Response */
        $response = Mage::getSingleton('api2/response');

        try {
            $this->authenticate($request);
            $this->route($request);
            $this->allow($request);
            $this->dispatch($request, $response);
        } catch (Exception $e) {
            $this->catchCritical($request, $response, $e);
        }

        $response->sendResponse();
    }

    protected function authenticate(Mage_Api2_Model_Request $request)
    {
        $accessKey = $request->getAccessKey();

        /** @var $authManager Mage_Api2_Model_Auth */
        $authManager = Mage::getModel('api2/auth');
        if (!$authManager->authenticate($accessKey)) {
            throw new Mage_Api2_Exception('Session expired or invalid.', 401);
        }
    }

    protected function route(Mage_Api2_Model_Request $request)
    {
        $apiType = $request->getApiType();

        /** @var $router Mage_Api2_Model_Router */
        $router = Mage::getModel('api2/router');
        $router->setRoutes($this->getConfig()->getRoutes($apiType));
        $router->route($request);
    }

    protected function allow(Mage_Api2_Model_Request $request)
    {
        $accessKey = $request->getAccessKey();
        $resourceType = $request->getResourceType();
        $operation = $request->getOperation();

        /** @var $globalAcl Mage_Api2_Model_Acl_Global */
        $globalAcl = Mage::getModel('api2/acl_global');
        $isAllowed = $globalAcl->isAllowed($accessKey, $resourceType, $operation);

        if (!$isAllowed) {
            throw new Mage_Api2_Exception('Authorization error.', 403);
        }
    }

    protected function dispatch(Mage_Api2_Model_Request $request, Zend_Controller_Response_Http $response)
    {
        /** @var $dispatcher Mage_Api2_Model_Dispatcher */
        $dispatcher = Mage::getModel('api2/dispatcher');
        $dispatcher->dispatch($request, $response);

        /*if ($response->getBody()=='') {
            $response->setBody('Empty body');
        }*/
        
        return $response;
    }

    protected function getConfig()
    {
        /** @var $config Mage_Api2_Model_Config */
        $config = Mage::getModel('api2/config');

        return $config;
    }

    protected function catchCritical(Mage_Api2_Model_Request $request, Zend_Controller_Response_Http $response, Exception $critical)
    {
        //if developer mode is set $critical can be without a Code, it will result in a Zend_Controller_Response_Exception('Invalid HTTP response code')
        $code = ($critical instanceof Mage_Api2_Exception)  ?$critical->getCode()   :500;
        /*$code = $critical->getCode();
        $code = ($code>100 && $code<599)  ?$code   :500;*/

        try {
            //add last error to stack and get the stack
            $response->setException($critical);
            $exceptions = $response->getException();

            //render content
            $renderType = $request->getAcceptType();
            $renderer = Mage_Api2_Model_Renderer::factory($renderType);
            $errorContent = $renderer->renderErrors($code, $exceptions);

            //set HTTP Code of last error, Content-Type and Body
            $response->setHttpResponseCode($code);
            $response->setHeader('Content-Type', sprintf('%s; charset=%s', $renderType, $request->getEncoding()));
            $response->setBody($errorContent);
        } catch (Exception $e) {
            //if error appeared in "error rendering" process then show it in plain text
            $response->setHttpResponseCode(500);  //$e->getCode() can result in one more loop of try..catch
            $response->setHeader('Content-Type', 'text/plain; charset=UTF-8');
            $response->setBody($e->getMessage());
        }
    }

}
