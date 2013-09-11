<?php
/**
 * Factory of REST request interpreters.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Request\Rest\Interpreter;

class Factory
{
    /**
     * Request interpret adapters.
     */
    const XML_PATH_WEBAPI_REQUEST_INTERPRETERS = 'global/webapi/rest/request/interpreters';

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /** @var \Magento\Core\Model\Config */
    protected $_applicationConfig;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\Config $applicationConfig
     */
    public function __construct(\Magento\ObjectManager $objectManager, \Magento\Core\Model\Config $applicationConfig)
    {
        $this->_objectManager = $objectManager;
        $this->_applicationConfig = $applicationConfig;
    }

    /**
     * Retrieve proper interpreter for the specified content type.
     *
     * @param string $contentType
     * @return \Magento\Webapi\Controller\Request\Rest\InterpreterInterface
     * @throws \LogicException|\Magento\Webapi\Exception
     */
    public function get($contentType)
    {
        $interpretersMetadata = (array)$this->_applicationConfig->getNode(self::XML_PATH_WEBAPI_REQUEST_INTERPRETERS);
        if (empty($interpretersMetadata) || !is_array($interpretersMetadata)) {
            throw new \LogicException('Request interpreter adapter is not set.');
        }
        foreach ($interpretersMetadata as $interpreterMetadata) {
            $interpreterType = (string)$interpreterMetadata->type;
            if ($interpreterType == $contentType) {
                $interpreterClass = (string)$interpreterMetadata->model;
                break;
            }
        }

        if (!isset($interpreterClass) || empty($interpreterClass)) {
            throw new \Magento\Webapi\Exception(
                __('Server cannot understand Content-Type HTTP header media type "%1"', $contentType),
                \Magento\Webapi\Exception::HTTP_BAD_REQUEST
            );
        }

        $interpreter = $this->_objectManager->get($interpreterClass);
        if (!$interpreter instanceof \Magento\Webapi\Controller\Request\Rest\InterpreterInterface) {
            throw new \LogicException(
                'The interpreter must implement "\Magento\Webapi\Controller\Request\Rest\InterpreterInterface".');
        }
        return $interpreter;
    }
}
