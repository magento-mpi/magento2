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

    /** @var Mage_Core_Service_Definition */
    protected $_definition;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Mage_Core_Service_Manager $manager
     * @param Mage_Core_Service_Definition $definition
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Mage_Core_Service_Manager $manager,
        Mage_Core_Service_Definition $definition,
        Magento_ObjectManager $objectManager)
    {
        $this->_serviceManager = $manager;
        $this->_definition = $definition;
        $this->_objectManager = $objectManager;
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
        $user = $this->_serviceManager->getUser();
        $acl  = $this->_serviceManager->getAcl();

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
     * Prepare service context object
     *
     * @param string $serviceClass
     * @param string $serviceMethod
     * @param mixed $context [optional]
     * @return Mage_Core_Service_Context $context
     */
    public function prepareContext($serviceClass, $serviceMethod, $context = null)
    {
        if (!$context instanceof Mage_Core_Service_Context) {
            $params  = $context;
            $context = $this->_objectManager->get('Mage_Core_Service_Context');
            $context->setData($params);
        } else {
            $params = $context->getData();
        }

        if (!$context->getIsPrepared()) {
            $requestSchema = $context->getRequestSchema();
            if (!$requestSchema instanceof Mage_Core_Service_DataSchema) {
                $params = $requestSchema;
                $requestSchema = $this->_definition->getRequestSchema($serviceClass, $serviceMethod, $context->getVersion());
                if (!empty($params) && is_array($params)) {
                    $requestSchema->addData($params);
                }
            }

            if ($requestSchema->getDataNamespace()) {
                $requestParams = (array)Mage::app()->getRequest()->getParam($requestSchema->getDataNamespace());
                if (!empty($requestParams)) {
                    $params = array_merge($requestParams, $params);
                }
            }

            $this->parse($params, $requestSchema);

            $this->filter($params, $requestSchema);

            $this->validate($params, $requestSchema);

            $context->setData($params);

            $context->setIsPrepared(true);
        }

        return $context;
    }

    public function parse(& $data, $schema)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
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

    /**
     * @param mixed $data
     * @param mixed $schema
     *
     * @return void
     */
    public function filter(& $data, $schema)
    {
        if (is_array($data)) {
            foreach ($data as $key => & $value) {
                $config = $schema->getData($key);
                if (!$schema->hasData($key)) {
                    unset($data[$key]);
                } else {
                    if (isset($config['schema'])) {
                        $schema = $this->_definition->getDataSchema($config['schema']);
                        $this->filter($value, $schema);

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
    public function validate(& $data, $schema)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $config = $schema->getData($key);
                if (isset($config['schema'])) {
                    $schema = $this->_definition->getDataSchema($config['schema']);
                    $this->validate($value, $schema);
                } else {
                    $this->_validate($value, $schema->getData($key));
                }

                $data[$key] = $value;
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

        if (!$responseSchema instanceof Mage_Core_Service_DataSchema) {
            $params = $responseSchema;
            $responseSchema = $this->_definition->getResponseSchema($serviceClass, $serviceMethod, $context->getVersion());
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
        foreach ($data as $key => & $value) {
            $config = $schema->getData($key);
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
        foreach ($schema->getData() as $key => $config) {
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
