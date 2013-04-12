<?php
/**
 * Abstract API service.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Mage_Core_Service_Abstract
{
    /** @var Mage_Core_Service_Manager */
    protected $_serviceManager;

    /** @var Mage_Core_Service_Definition */
    protected $_definition;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    public function __construct(
        Mage_Core_Service_Manager $manager,
        Mage_Core_Service_Definition $definition,
        Magento_ObjectManager $objectManager)
    {
        $this->_serviceManager = $manager;
        $this->_definition     = $definition;
        $this->_objectManager  = $objectManager;
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

        return $this->$serviceMethod($context);
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
        if (! $context instanceof Mage_Core_Service_Context) {
            $params  = $context;
            $context = $this->_objectManager->get('Mage_Core_Service_Context');
            if (is_string($params) || is_numeric($params)) {
                $params = array('id' => $params);
            }
            $context->setData($params);
        }

        if (!$context->getIsPrepared()) {
            $requestSchema = $this->_definition->getRequestSchema($serviceClass, $serviceMethod, $context->getRequestSchema());

            $requestParams = (array)Mage::app()->getRequest()->getParam($requestSchema->getDataNamespace());

            // TODO we need to have addData and _filter are working recursively and not as it is right now
            $context->addData($requestParams);
            $this->_filter($context, $requestSchema);

            // @todo how to declare and extract global variables such as `store_id`?

            // @todo how to apply ACL rules?

            $context->setIsPrepared(true);
        }

        return $context;
    }

    protected function _filter(Mage_Core_Service_Context $context, Varien_Object $requestSchema)
    {
        foreach ($context->getData() as $key => $value) {
            if (!$requestSchema->hasData($key)) {
                $context->unsetData($key);
            }
        }
    }

    /**
     * Prepare service response
     *
     * @param string $serviceClass
     * @param string $serviceMethod
     * @param mixed & $response
     * @param mixed $responseSchema [optional]
     * @param array $extraResponseSchemaParameters [optional]
     * @return bool
     */
    public function prepareResponse($serviceClass, $serviceMethod, & $response, $responseSchema = null, array $extraResponseSchemaParameters = array())
    {
        if (! $responseSchema instanceof Varien_Object) {
            $params = $responseSchema;
            $responseSchema = $this->_definition->getResponseSchema($serviceClass, $serviceMethod);
            $responseSchema->addData($params);
        }

        if (!empty($extraResponseSchemaParameters)) {
            $responseSchema->addData($extraResponseSchemaParameters);
        }

        $filtered = array();
        foreach ($responseSchema->getData() as $key => $element) {
            $filtered[$key] = $this->_fetchData($response, $key, $element, $responseSchema);
        }

        // @TODO @FIX this will be braking a lot of features till full refactoring ended up
        if ($responseSchema->getAsArray() || is_array($response)) {
            $response = $filtered;
        } elseif ($response instanceof Varien_Object) {
            $response->setData($filtered);
        } else {
            $response = $filtered;
        }
    }

    protected function _fetchData(& $response, $key, $element, $schema)
    {
        if (!empty($element['_elements'])) {
            $result = array();
            foreach ($element['_elements'] as $_key => $_element) {
                $result[$key] = $this->_fetchData($response, $_key, $_element, $schema);
            }
            return $result;
        }

        if (isset($elements['get_callback'])) {
            $result = call_user_func($elements['get_callback'], array(
                'data'   => $response,
                'node'   => $element,
                'schema' => $schema
            ));
        } else {
            if ($response instanceof Varien_Object) {
                $result = $response->getDataUsingMethod($key);
            } else {
                $result = $response[$key];
            }
        }

        return $result;
    }

    /**
     * Returns unique service identifier.
     *
     * @return string
     */
    abstract protected function _getServiceId();

    /**
     * Returns unique service method identifier.
     *
     * @param string $methodName
     * @return string
     */
    public function getMethodId($methodName)
    {
        return sprintf('%s/%s', $this->_getServiceId(), $methodName);
    }
}
