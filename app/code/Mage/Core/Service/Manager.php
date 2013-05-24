<?php

class Mage_Core_Service_Manager extends Varien_Object
{
    const AREA_SERVICES = 'services';

    /**
     * @var Magento_ObjectManager_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Service_Context
     */
    protected $_serviceContext;

    /** @var Mage_Core_Service_Config */
    protected $_config;

    /**
     * @var array $_requestSchemas
     */
    protected $_requestSchemas = array();

    /**
     * @var array $_responseSchemas
     */
    protected $_responseSchemas = array();

    /**
     * @var array $_contentSchemas
     */
    protected $_contentSchemas = array();

    protected $_services = array();

    /**
     * @param Magento_ObjectManager_ObjectManager $objectManager
     * @param Mage_Core_Service_Context $serviceContext
     * @param Mage_Core_Service_Config $config
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Core_Service_Context $serviceContext,
        Mage_Core_Service_Config $config)
    {
        $this->_objectManager = $objectManager;
        $this->_serviceContext = $serviceContext;
        $this->_config = $config;
    }

    /**
     * Call a service method
     *
     * @param string $serviceReferenceId
     * @param string $serviceMethod
     * @param mixed $request [optional]
     * @param mixed $version [optional]
     * @return mixed (service execution response)
     */
    public function call($serviceReferenceId, $serviceMethod, $request = null, $version = null)
    {
        $service = $this->createServiceInstance($serviceReferenceId, $serviceMethod, $version);

        // implement ACL, debugging, profiling, etc

        $response = $service->$serviceMethod($request, $version);

        return $response;
    }

    /**
     * Create service instance.
     *
     * @param string $serviceReferenceId
     * @param string $serviceMethod [optional]
     * @param string $version [optional]
     * @return object
     */
    public function createServiceInstance($serviceReferenceId, $serviceMethod = null, $version = null)
    {
        $className = $this->_config->getServiceClassByServiceName($serviceReferenceId, $serviceMethod, $version);

        if (!isset($this->_services[$className])) {
            $this->_services[$className] = $this->_objectManager->create($className);
        }

        return $this->_services[$className];
    }

    /**
     * Retrieve a service helper instance
     *
     * @param string $serviceHelperClassRef
     * @return Mage_Core_Service_Helper_Abstract $serviceHelper
     */
    public function getServiceHelper($serviceHelperClassRef)
    {
        return $this->_objectManager->create($serviceHelperClassRef);
    }

    /**
     * Retrieve an object instance of given class name reference
     *
     * @param string $classRef
     * @return $object
     */
    public function getObject($classRef)
    {
        return $this->_objectManager->create($classRef);
    }

    /**
     * @param string $serviceReferenceID
     * @param string $serviceMethod [optional]
     * @param string $version [optional]
     * @param array $extraParameters [optional]
     * @return Magento_Data_Schema $requestSchema
     */
    public function getRequestSchema($serviceReferenceID, $serviceMethod = null, $version = null, array $extraParameters = array())
    {
        $hash = $serviceReferenceID . '::' . $serviceMethod . '::' . $version;
        if (!isset($this->_requestSchemas[$hash])) {
            $schemaFile = "app/schema/{$this->_serviceContext->getRequestContext()}/{$this->_serviceContext->getSchema()}/{$serviceReferenceID}.php";
            $schema     = $this->getContentSchema($schemaFile);
            if (null !== $serviceMethod) {
                $resultSchema = isset($schema['methods'][$serviceMethod]['request']) ? $schema['methods'][$serviceMethod]['request'] : array();
            } else {
                $resultSchema = $schema;
            }

            if (!empty($extraParameters)) {
                $resultSchema = array_merge($resultSchema, $extraParameters);
            }

            $this->_requestSchemas[$hash] = new Magento_Data_Schema();
            $this->_requestSchemas[$hash]->load($resultSchema);
        }

        return $this->_requestSchemas[$hash];
    }

    /**
     * @param string $serviceReferenceID
     * @param string $serviceMethod [optional]
     * @param string $version [optional]
     * @return Magento_Data_Schema $responseSchema
     */
    public function getResponseSchema($serviceReferenceID, $serviceMethod = null, $version = null)
    {
        $hash = $serviceReferenceID . '::' . $serviceMethod . '::' . $version;
        if (!isset($this->_responseSchemas[$hash])) {
            $schemaFile = "app/schema/{$this->_serviceContext->getRequestContext()}/{$this->_serviceContext->getSchema()}/{$serviceReferenceID}.php";
            $schema     = $this->getContentSchema($schemaFile);

            if (null !== $serviceMethod) {
                $resultSchema = isset($schema['methods'][$serviceMethod]['response']) ? $schema['methods'][$serviceMethod]['response'] : array();
            } else {
                $resultSchema = $schema;
            }

            $this->_responseSchemas[$hash] = new Magento_Data_Schema();
            $this->_responseSchemas[$hash]->load($resultSchema);
        }

        return $this->_responseSchemas[$hash];
    }

    /**
     * @param mixed $schemaFile
     * @return Magento_Data_Schema $contentSchema
     */
    public function getContentSchema($schemaFile)
    {
        if (!isset($this->_contentSchemas[$schemaFile])) {
            $this->_contentSchemas[$schemaFile] = new Magento_Data_Schema();
            $this->_contentSchemas[$schemaFile]->load($schemaFile);
        }

        return $this->_contentSchemas[$schemaFile];
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
}
