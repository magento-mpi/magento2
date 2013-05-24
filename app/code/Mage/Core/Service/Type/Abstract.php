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
    /**
     * @var Mage_Core_Service_Manager
     */
    protected $_serviceManager;

    /**
     * @var Mage_Core_Service_Context
     */
    protected $_serviceContext;

    /**
     * @var $_serviceID string
     */
    protected $_serviceID = null;

    /**
     * @var $_serviceVersion string
     */
    protected $_serviceVersion = null;

    /**
     * @param Mage_Core_Service_Manager $serviceManager
     * @param Mage_Core_Service_Context $context
     */
    public function __construct(
        Mage_Core_Service_Manager $serviceManager,
        Mage_Core_Service_Context $context)
    {
        if (empty($this->_serviceID)) {
            $message = Mage::helper('core')->__('Empty Service ID for the service %s', get_class($this));
            throw new Mage_Core_Service_Exception($message);
        }

        $this->_serviceManager = $serviceManager;
        $this->_serviceContext = $context;
    }

    /**
     * Invoke service method
     *
     * @param string $serviceMethod
     * @param mixed $request [optional]
     * @param mixed $version [optional]
     * @return mixed (service execution response)
     */
    final public function invoke($serviceMethod, $request = null, $version = null)
    {
        if (false) {
            // check "before" plugins
        }

        if (false) {
            // check "instead" plugin
        } else {
            $result = $this->$serviceMethod($request, $version);
        }

        if (false) {
            // check "after" plugins
        }

        return $result;
    }

    /**
     * Prepare service request object
     *
     * @param string $serviceMethod
     * @param mixed $request [optional]
     * @return Magento_Data_Array $request
     */
    protected function _prepareRequest($serviceMethod, $request = null)
    {
        if (!$request instanceof Magento_Data_Array) {
            $request = new Magento_Data_Array($request);
        }

        if (!$request->getIsPrepared()) {
            $_requestSchema = $request->getRequestSchema() ? (array) $request->getRequestSchema() : array();
            $requestSchema  = $this->_serviceManager->getRequestSchema($this->_serviceID, $serviceMethod, $this->_serviceVersion, $_requestSchema);

            if ($requestSchema->getDataNamespace()) {
                $requestParams = (array) Mage::app()->getRequest()->getParam($requestSchema->getDataNamespace());
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

    /**
     * @param array & $data
     * @param Magento_Data_Schema $schema
     */
    public function parse(& $data, $schema)
    {
        $fields = $schema->getData('fields');
        foreach ($data as $key => & $value) {
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
                }
            }
        }
    }

    /**
     * @param array & $data
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

    /**
     * @param array & $data
     * @param Magento_Data_Schema $schema
     * @return void
     */
    public function validate(& $data, $schema)
    {
        $fields = $schema->getData('fields');
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $fields)) {
                $config = $schema->getData($key);
                if (isset($config['schema'])) {
                    $schema = $this->_serviceManager->getContentSchema($config['schema']);
                    $this->validate($value, $schema);
                } else {
                    $this->_validate($value, $config);
                }
            }
        }
    }

    protected function _validate($value, $config)
    {
        return true;
    }

    /**
     * Prepare collection for response
     *
     * @param string $serviceMethod
     * @param Varien_Data_Collection $collection
     * @param mixed $request
     * @return Magento_Data_Collection | array
     */
    protected function _prepareCollection($serviceMethod, $collection, $request)
    {
        $resultCollection = new Magento_Data_Collection();

        foreach ($collection->getItems() as $item) {
            $container = $this->_prepareModel($serviceMethod, $item, $request);
            $resultCollection->addItem($container);
        }

        if ($request->getAsArray()) {
            return $resultCollection->toArray();
        } else {
            return $resultCollection;
        }
    }

    /**
     * Prepare service response
     *
     * @param string $serviceMethod
     * @param mixed $model
     * @param mixed $request
     * @return bool
     */
    protected function _prepareModel($serviceMethod, $model, $request)
    {
        $responseSchema = $request->getResponseSchema();

        if (!$responseSchema instanceof Magento_Data_Schema) {
            $params = $responseSchema;
            $responseSchema = $this->_serviceManager->getResponseSchema($this->_serviceID, $serviceMethod, $this->_serviceVersion);
            if (!empty($params) && is_array($params)) {
                $responseSchema->addData($params);
            }
        }

        $responseSchema->setRequestedFields($request->getFields());

        $array = array();
        foreach ($responseSchema->getData('fields') as $key => $config) {
            $result = $this->_fetchValue($model, $key, $config);
            $array[$key] = $result;
        }

        if ($request->getAsArray()) {
            return $array;
        } else {
            return new Varien_Object($array);
        }
    }

    /**
     * @param Varien_Object $model
     * @param string $key
     * @param array $config
     * @return array|null
     */
    protected function _fetchValue($model, $key, $config)
    {
        if (isset($config['_elements'])) {
            $result = array();
            foreach ($config['_elements'] as $_key => $_config) {
                $result[$_key] = $this->_fetchValue($model, $_key, $_config);
            }
            return $result;
        }

        if (isset($config['get_callback'])) {
            if (is_string($config['get_callback'])) {
                if (strpos($config['get_callback'], '/') !== false) {
                    list ($method, $key) = explode('/', $config['get_callback']);
                    $result = $model->$method();
                    $result = is_array($result) && array_key_exists($key, $result) ? $result[$key] : null;
                } else {
                    $result = $model->$config['get_callback']();
                }
            } else {
                $callbackObject = $this->_serviceManager->getObject($config['get_callback'][0]);
                $result = $callbackObject->$config['get_callback'][1]($model);
            }
        } else {
            $field = !empty($config['field']) ? $config['field'] : $key;
            $result = $model->getDataUsingMethod($field);
        }

        return $result;
    }
}
