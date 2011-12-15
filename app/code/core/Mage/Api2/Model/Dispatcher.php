<?php

class Mage_Api2_Model_Dispatcher
{
    const RESOURCE_CLASS_TEMPLATE = ':resource_:api_:user_V:version';

    /**
     * @param Mage_Api2_Model_Request $request
     * @param Zend_Controller_Response_Http $response
     */
    public function dispatch(Mage_Api2_Model_Request $request, Zend_Controller_Response_Http $response)
    {
        $class = $this->buildClassName($request);

        //comment this to leave the job for autoloader
        $this->anticipateAutoLoader($class);

        /** @var $model Mage_Api2_Model_Resource */
        $model = new $class($request, $response);   //Fatal error: Class '%s' not found in %s on line %d
                                                    //which can not be caught if autoloader used
        $model->dispatch();
    }

    /**
     * Build resource class name using request params
     *
     * @param Mage_Api2_Model_Request $request
     * @return string
     */
    protected function buildClassName(Mage_Api2_Model_Request $request)
    {
        $accessKey = $request->getAccessKey();
        $user = new Mage_OAuth_Model_User($accessKey);
        $userType = $user->getType('guest');

        $resource = $request->getParam('model');     //set in Mage_Api2_Model_Router::setRequestParams
        $apiType = ucfirst($request->getApiType());
        $userType = ucfirst($userType);
        $version = $request->getVersion();

        $replace = array(
            ':resource' => $resource,
            ':api'      => $apiType,
            ':user'     => $userType,
            ':version'  => $version,
        );
        $class = strtr(Mage_Api2_Model_Dispatcher::RESOURCE_CLASS_TEMPLATE, $replace);

        return $class;
    }

    /**
     * Replace autoload process to catch possible fatal error.
     * Also requires Mage::setIsDeveloperMode(true); to catch it.
     * 
     * @throws Mage_Api2_Exception
     * @param $class
     * @return void
     */
    protected function anticipateAutoLoader($class)
    {
        $classFile = str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $class))).'.php';
        try {
            include $classFile;
        } catch (Exception $e) {
            throw new Mage_Api2_Exception(sprintf('File "%s" could not be loaded', $classFile), 500);
        }
    }
}
