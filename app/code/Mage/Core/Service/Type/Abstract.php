<?php
/**
 * Abstract API service.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Mage_Core_Service_Type_Abstract
{
    /** @var Mage_Core_Service_Manager */
    protected $_serviceManager;

    /** @var Mage_Core_Service_Context */
    protected $_serviceContext;

    /**
     * @param Mage_Core_Service_Manager $manager
     * @param Mage_Core_Service_Context $context
     */
    public function __construct(
        Mage_Core_Service_Manager $manager,
        Mage_Core_Service_Context $context)
    {
        $this->_serviceManager = $manager;
        $this->_serviceContext = $context;
    }

    /**
     * Call service method (alternative approach)
     *
     * @param string $serviceMethod
     * @param mixed $request [optional]
     * @param mixed $version [optional]
     * @return mixed (service execution response)
     */
    final public function call($serviceMethod, $request = null, $version = null)
    {
        // implement ACL and other routine procedures here (debugging, profiling, etc)
        $this->authorize(get_class($this), $serviceMethod);

        return $this->$serviceMethod($request, $version);
    }

    public function authorize($serviceClass, $serviceMethod)
    {
        $user = $this->_serviceContext->getUser();
        $acl  = $this->_serviceContext->getAcl();

        if ($user && $acl) {
            try {
                $result = $acl->isAllowed($user->getAclRole(), $serviceClass . '/' . $serviceMethod);
            } catch (Exception $e) {
                try {
                    if (!$acl->has($serviceClass . '/' . $serviceMethod)) {
                        $result = $acl->isAllowed($user->getAclRole(), null);
                    }
                } catch (Exception $e) {
                    $result = false;
                }
            }
        }

        if (false === $result) {
            throw new Mage_Core_Service_Exception($serviceClass . '/' . $serviceMethod, Mage_Core_Service_Exception::HTTP_FORBIDDEN);
        }

        return $result;
    }

    /**
     * Prepare service request object
     *
     * @param string $serviceClass
     * @param string $serviceMethod
     * @param mixed $request [optional]
     * @return Magento_Data_Array $request
     */
    public function prepareRequest($serviceClass, $serviceMethod, $request = null)
    {
        if (!$request instanceof Magento_Data_Array) {
            $request = new Magento_Data_Array($request);
        }

        if (!$request->getIsPrepared()) {
            $requestSchema = $request->getRequestSchema();
            if (!$requestSchema instanceof Magento_Data_Schema) {
                $requestSchema = $this->_serviceManager->getRequestSchema($serviceClass, $serviceMethod, $request->getVersion(), $requestSchema);
            }

            if ($requestSchema->getDataNamespace()) {
                $requestParams = (array)Mage::app()->getRequest()->getParam($requestSchema->getDataNamespace());
                if (!empty($requestParams)) {
                    $request->addData($requestParams);
                }
            }

            $this->parse($request, $requestSchema);

            $this->filter($request, $requestSchema);

            $this->validate($request, $requestSchema);

            $request->setIsPrepared(true);
        }

        return $request;
    }

    /**
     * @param Varien_Object $data
     * @param Magento_Data_Schema $schema
     */
    public function parse(& $data, $schema)
    {
        $fields = $schema->getData('fields');
        foreach ($data->getData() as $key => $value) {
            if (array_key_exists($key, $fields)) {
                if (isset($fields[$key]['content_type'])) {
                    switch ($fields[$key]['content_type']) {
                        case 'json':
                            $value = json_decode($value, true);
                            break;
                        case 'xml':
                            $value = array(); // convert from xml to assoc array
                            break;
                        case 'list':
                            $value = explode(',', $value);
                            break;
                    }

                    $data->setDataUsingMethod($key, $value);
                }
            }
        }
    }

    /**
     * @param Varien_Object $data
     * @param Magento_Data_Schema $schema
     *
     * @return void
     */
    public function filter(& $data, $schema)
    {
        $fields = $schema->getData('fields');
        $requestedFields = $schema->getRequestedFields();
        if (!empty($requestedFields)) {
            $requestedFields = array_flip($requestedFields);
            $fields = array_intersect_key($fields, $requestedFields);
        }
        foreach ($data->getData() as $key => $value) {
            if (array_key_exists($key, $fields)) {
                $config = $fields[$key];
                if (isset($config['schema'])) {
                    $schema = $this->_serviceManager->getContentSchema($config['schema']);
                    $this->filter($value, $schema);

                    $data->setDataUsingMethod($key, $value);
                }
            } else {
                $data->unsetData($key, $value);
            }
        }
    }

    /**
     * @param Varien_Object $data
     * @param Magento_Data_Schema $schema
     *
     * @return void
     */
    public function validate(& $data, $schema)
    {
        $fields = $schema->getData('fields');
        foreach ($data->getData() as $key => $value) {
            if (array_key_exists($key, $fields)) {
                $config = $schema->getData($key);
                if (isset($config['schema'])) {
                    $schema = $this->_serviceManager->getContentSchema($config['schema']);
                    $this->validate($value, $schema);
                } else {
                    $this->_validate($value, $schema->getData($key));
                }

                $data->setDataUsingMethod($key, $value);
            }
        }
    }

    protected function _validate(& $value, $schema)
    {
        return true;
    }

    /**
     * Prepare service response
     *
     * @param string $serviceClass
     * @param string $serviceMethod
     * @param mixed & $response
     * @param mixed $request
     * @return bool
     */
    public function prepareResponse($serviceClass, $serviceMethod, & $response, $request)
    {
        $responseSchema = $request->getResponseSchema();

        if (!$responseSchema instanceof Magento_Data_Schema) {
            $params = $responseSchema;
            $responseSchema = $this->_serviceManager->getResponseSchema($serviceClass, $serviceMethod, $request->getVersion());
            if (!empty($params) && is_array($params)) {
                $responseSchema->addData($params);
            }
        }

        $responseSchema->setRequestedFields($request->getFields());

        if (!$response instanceof Varien_Object) {
            $data = new Varien_Object($response);
        } else {
            $data = & $response;
        }

        $this->filter($data, $responseSchema);
        $this->validate($data, $responseSchema);
        $this->prepare($data, $responseSchema);

        if (!$response instanceof Varien_Object) {
            $response = $data->getData();
        }
    }

    /**
     * @param Varien_Object $data
     * @param Magento_Data_Schema $schema
     */
    public function prepare(& $data, $schema)
    {
        $fields = $schema->getData('fields');
        foreach ($data->getData() as $key => $value) {
            $config = $fields[$key];
            if (isset($config['content_type'])) {
                switch ($config['accept_type']) {
                    case 'json':
                        $value = json_encode($value);
                        break;
                    case 'xml':
                        $value = '<value />'; // convert to xml string
                        break;
                }
            }
            $data->setDataUsingMethod($key, $value);
        }

        $this->applySchema($data, $schema);
    }

    /**
     * @param Varien_Object $data
     * @param Magento_Data_Schema $schema
     * @return Varien_Object $data
     */
    public function applySchema(& $data, $schema)
    {
        foreach ($schema->getData('fields') as $key => $config) {
            $result = $this->_fetchValue($data, $key, $config, $schema);
            $data->setData($key, $result);
        }
    }

    protected function _fetchValue($data, $key, $config, $schema)
    {
        if (isset($config['_elements'])) {
            $result = array();
            foreach ($config['_elements'] as $_key => $_config) {
                $result[$_key] = $this->_fetchValue($data, $_key, $_config, $schema);
            }
            $data->setDataUsingMethod($key, $result);
            return;
        }

        if (isset($config['get_callback'])) {
            if (is_string($config['get_callback'])) {
                if (strpos($config['get_callback'], '/') !== false) {
                    list ($method, $key) = explode('/', $config['get_callback']);
                    $result = $data->$method();
                    $result = array_key_exists($key, $result) ? $result[$key] : null;
                } else {
                    $result = $data->$config['get_callback']();
                }
            } else {
                $callbackObject = $this->_serviceManager->getObject($config['get_callback'][0]);
                $result = $callbackObject->$config['get_callback'][1]($data);
            }
        } else {
            $result = $data->getDataUsingMethod($key);
        }

        return $result;
    }
}
