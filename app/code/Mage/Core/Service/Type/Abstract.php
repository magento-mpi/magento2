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
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Mage_Core_Service_Manager $manager,
        Mage_Core_Service_Context $context)
    {
        $this->_serviceManager = $manager;
        $this->_serviceContext = $manager;
    }

    /**
     * Call service method (alternative approach)
     *
     * @param string $serviceMethod
     * @param mixed $context [optional]
     * @return mixed (service execution response)
     */
    final public function call($serviceMethod, $context = null)
    {
        // implement ACL and other routine procedures here (debugging, profiling, etc)
        $this->authorize(get_class($this), $serviceMethod);

        return $this->$serviceMethod($context);
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

            $data = $request->getData();

            $this->parse($data, $requestSchema);

            $this->filter($data, $requestSchema);

            $this->validate($data, $requestSchema);

            $request->setData($data);

            $request->setIsPrepared(true);
        }

        return $request;
    }

    public function parse(& $data, $schema)
    {
        if (is_array($data)) {
            $fields = $schema->getData('fields');
            foreach ($data as $key => $value) {
                if (array_key_exists($key, $fields)) {
                    $config = $schema->getData($key);
                    if (isset($config['content_type'])) {
                        switch ($config['content_type']) {
                            case 'json':
                                $value = json_decode($value, true);
                                break;
                            case 'xml':
                                $value = array(); // convert from xml to assoc array
                                break;
                        }

                        $data[$key] = $value;
                    }
                }
            }
        }
    }

    /**
     * @param mixed $data
     * @param mixed $schema
     *
     * @return void
     */
    public function filter(& $data, $schema)
    {
        if (is_array($data)) {
            $fields = $schema->getData('fields');
            foreach ($data as $key => & $value) {
                if (array_key_exists($key, $fields)) {
                    $config = $fields[$key];
                    if (isset($config['schema'])) {
                        $schema = $this->_serviceManager->getContentSchema($config['schema']);
                        $this->filter($value, $schema);

                        $data[$key] = $value;
                    }
                } else {
                    unset($data[$key]);
                }
            }
        }
    }

    /**
     * @param mixed $data
     * @param mixed $schema
     *
     * @return void
     */
    public function validate(& $data, $schema)
    {
        if (is_array($data)) {
            $fields = $schema->getData('fields');
            foreach ($data as $key => $value) {
                if (array_key_exists($key, $fields)) {
                    $config = $schema->getData($key);
                    if (isset($config['schema'])) {
                        $schema = $this->_serviceManager->getContentSchema($config['schema']);
                        $this->validate($value, $schema);
                    } else {
                        $this->_validate($value, $schema->getData($key));
                    }

                    $data[$key] = $value;
                }
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
     * @param mixed $context
     * @return bool
     */
    public function prepareResponse($serviceClass, $serviceMethod, & $response, $context)
    {
        $responseSchema = $context->getResponseSchema();

        if (!$responseSchema instanceof Magento_Data_Schema) {
            $params = $responseSchema;
            $responseSchema = $this->_serviceManager->getResponseSchema($serviceClass, $serviceMethod, $context->getVersion());
            if (!empty($params) && is_array($params)) {
                $responseSchema->addData($params);
            }
        }

        if ($response instanceof Varien_Object) {
            $data = $response->getData();
        } else {
            $data = & $response;
        }

        $this->filter($data, $responseSchema);
        $this->validate($data, $responseSchema);
        $this->prepare($data, $responseSchema);

        if ($response instanceof Varien_Object) {
            $response->setData($data);
        }
    }

    public function prepare(& $data, $schema)
    {
        $fields = $schema->getData('fields');
        foreach ($data as $key => & $value) {
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
        }

        $data = $this->applySchema($data, $schema);
    }

    public function applySchema($data, $schema)
    {
        $result = array();
        foreach ($schema->getData('fields') as $key => $config) {
            $result[$key] = $this->_fetchValue($key, $config, $data, $schema);
        }
        return $result;
    }

    protected function _fetchValue($key, $config, $data, $schema)
    {
        if (isset($config['_elements'])) {
            $result = array();
            foreach ($config['_elements'] as $_key => $_config) {
                $result[$_key] = $this->_fetchValue($_key, $_config, $data, $schema);
            }
            return $result;
        }

        if (isset($config['get_callback'])) {
            $result = call_user_func($config['get_callback'], array(
                'data'   => $data,
                'config' => $config,
                'schema' => $schema
            ));
        } else {
            if ($data instanceof Varien_Object) {
                $result = $data->getDataUsingMethod($key);
            } else {
                $result = $data[$key];
            }
        }

        return $result;
    }
}
