<?php
/**
 * Factory of REST request interpreters.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Request_Rest_Interpreter_Factory
{
    /**
     * Request interpret adapters.
     */
    const XML_PATH_WEBAPI_REQUEST_INTERPRETERS = 'global/webapi/rest/request/interpreters';

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /** @var Magento_Core_Model_Config */
    protected $_applicationConfig;

    /** @var Magento_Webapi_Helper_Data */
    protected $_helper;

    /** @var Magento_Core_Model_Factory_Helper */
    protected $_helperFactory;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_Config $applicationConfig
     * @param Magento_Core_Model_Factory_Helper $helperFactory
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Core_Model_Config $applicationConfig,
        Magento_Core_Model_Factory_Helper $helperFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->_applicationConfig = $applicationConfig;
        $this->_helperFactory = $helperFactory;
        $this->_helper = $this->_helperFactory->get('Magento_Webapi_Helper_Data');
    }

    /**
     * Retrieve proper interpreter for the specified content type.
     *
     * @param string $contentType
     * @return Magento_Webapi_Controller_Request_Rest_InterpreterInterface
     * @throws LogicException|Magento_Webapi_Exception
     */
    public function get($contentType)
    {
        $interpretersMetadata = (array)$this->_applicationConfig->getNode(self::XML_PATH_WEBAPI_REQUEST_INTERPRETERS);
        if (empty($interpretersMetadata) || !is_array($interpretersMetadata)) {
            throw new LogicException('Request interpreter adapter is not set.');
        }
        foreach ($interpretersMetadata as $interpreterMetadata) {
            $interpreterType = (string)$interpreterMetadata->type;
            if ($interpreterType == $contentType) {
                $interpreterClass = (string)$interpreterMetadata->model;
                break;
            }
        }

        if (!isset($interpreterClass) || empty($interpreterClass)) {
            throw new Magento_Webapi_Exception(
                $this->_helper->__('Server cannot understand Content-Type HTTP header media type "%s"', $contentType),
                Magento_Webapi_Exception::HTTP_BAD_REQUEST
            );
        }

        $interpreter = $this->_objectManager->get($interpreterClass);
        if (!$interpreter instanceof Magento_Webapi_Controller_Request_Rest_InterpreterInterface) {
            throw new LogicException(
                'The interpreter must implement "Magento_Webapi_Controller_Request_Rest_InterpreterInterface".');
        }
        return $interpreter;
    }
}
