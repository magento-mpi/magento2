<?php
/**
 * Factory of REST request interpreters.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Request_Rest_Interpreter_Factory
{
    /**
     * Request interpret adapters.
     */
    const XML_PATH_WEBAPI_REQUEST_INTERPRETERS = 'global/webapi/rest/request/interpreters';

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /** @var Mage_Core_Model_Config */
    protected $_applicationConfig;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Config $applicationConfig
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Config $applicationConfig
    ) {
        $this->_objectManager = $objectManager;
        $this->_applicationConfig = $applicationConfig;
    }

    /**
     * Retrieve proper interpreter for the specified content type.
     *
     * @param string $contentType
     * @return Mage_Webapi_Controller_Request_Rest_InterpreterInterface
     * @throws LogicException|Mage_Webapi_Exception
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
            throw new Mage_Webapi_Exception(
                __('Server cannot understand Content-Type HTTP header media type "%1"', $contentType),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST
            );
        }

        $interpreter = $this->_objectManager->get($interpreterClass);
        if (!$interpreter instanceof Mage_Webapi_Controller_Request_Rest_InterpreterInterface) {
            throw new LogicException(
                'The interpreter must implement "Mage_Webapi_Controller_Request_Rest_InterpreterInterface".');
        }
        return $interpreter;
    }
}
