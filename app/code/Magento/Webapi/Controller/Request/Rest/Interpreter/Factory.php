<?php
/**
 * Factory of REST request interpreters
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Request_Rest_Interpreter_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_interpreters;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param array $interpreters
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        array $interpreters = array()
    ) {
        $this->_objectManager = $objectManager;
        $this->_interpreters = $interpreters;
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
        if (empty($this->_interpreters)) {
            throw new LogicException('Request interpreter adapter is not set.');
        }
        foreach ($this->_interpreters as $interpreterMetadata) {
            $interpreterType = $interpreterMetadata['type'];
            if ($interpreterType == $contentType) {
                $interpreterClass = $interpreterMetadata['model'];
                break;
            }
        }

        if (!isset($interpreterClass) || empty($interpreterClass)) {
            throw new Magento_Webapi_Exception(
                __('Server cannot understand Content-Type HTTP header media type "%1"', $contentType),
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
