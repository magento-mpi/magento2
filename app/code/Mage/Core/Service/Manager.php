<?php

class Mage_Core_Service_Manager extends Varien_Object
{
    /** @var Magento_ObjectManager */
    protected $_objectManager;

    /** @var Mage_Core_Service_Definition */
    protected $_definition;

    public function __construct(Magento_ObjectManager $objectManager, Mage_Core_Service_Definition $definition)
    {
        $this->_objectManager = $objectManager;
        $this->_definition    = $definition;
    }

    /**
     * Call service method
     *
     * @param string $serviceClass
     * @param string $serviceMethod
     * @param mixed $context [optional]
     * @param mixed $responseSchema [optional]
     * @return mixed (service execution response)
     */
    public function call($serviceClass, $serviceMethod, $context = null, $responseSchema = null)
    {
        $context  = $this->prepareContext($serviceClass, $serviceMethod, $context);

        $service  = $this->getService($serviceClass);

        $response = $service->$serviceMethod($context);

        $this->prepareResponse($serviceClass, $serviceMethod, $response, $responseSchema, $context->getResponseSchema());

        return $response;
    }

    /**
     * Look up for service model
     *
     * @param string $serviceClass
     * @return Mage_Core_Service_Abstract $service
     */
    public function getService($serviceClass)
    {
        $service = $this->_objectManager->get($serviceClass);
        return $service;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function prepareContext($serviceClass, $serviceMethod, $context)
    {
        if (! $context instanceof Mage_Core_Service_Context || !$context->getIsPrepared()) {
            $context = $this->_prepareContext($serviceClass, $serviceMethod, $context);
        }
        return $context;
    }

    /**
     * Prepare service context object
     *
     * @param string $serviceClass
     * @param string $serviceMethod
     * @param mixed $context [optional]
     * @return Mage_Core_Service_Context $context
     */
    protected function _prepareContext($serviceClass, $serviceMethod, $context = null)
    {
        if (! $context instanceof Mage_Core_Service_Context) {
            $params  = $context;
            $context = $this->_objectManager->get('Mage_Core_Service_Context');
            if (is_string($params) || is_numeric($params)) {
                $params = array('id' => $params);
            }
            $context->setData($params);
        }

        $requestSchema = $this->_definition->getRequestSchema($serviceClass, $serviceMethod, $context->getRequestSchema());

        $requestParams = (array)Mage::app()->getRequest()->getParam($requestSchema->getDataNamespace());

        // TODO we need to have addData and _filter are working recursively and not as it is right now
        $context->addData($requestParams);
        $this->_filter($context, $requestSchema);

        // @todo how to declare and extract global variables such as `store_id`?

        // @todo how to apply ACL rules?

        $context->setIsPrepared(true);

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
}
