<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Webservice Webapi data helper
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Request interpret adapters
     */
    const XML_PATH_Webapi_REQUEST_INTERPRETERS = 'global/webapi/request/interpreters';

    /**
     * Response render adapters
     */
    const XML_PATH_Webapi_RESPONSE_RENDERS     = 'global/webapi/response/renders';

    /**
     * Get interpreter type for Request body according to Content-type HTTP header
     *
     * @return array
     */
    public function getRequestInterpreterAdapters()
    {
        return (array) Mage::app()->getConfig()->getNode(self::XML_PATH_Webapi_REQUEST_INTERPRETERS);
    }

    /**
     * Get interpreter type for Request body according to Content-type HTTP header
     *
     * @return array
     */
    public function getResponseRenderAdapters()
    {
        return (array) Mage::app()->getConfig()->getNode(self::XML_PATH_Webapi_RESPONSE_RENDERS);
    }

    /**
     * Reformat request data to be compatible with method specified interface: <br/>
     * - sort arguments in correct order <br/>
     * - set default values for omitted arguments
     *
     * @param string|object $class
     * @param string $methodName
     * @param array $requestData Data to be passed to method
     * @return array Array of prepared method arguments
     * @throws RuntimeException
     */
    public function prepareMethodParams($class, $methodName, $requestData)
    {
        $method = new ReflectionMethod($class, $methodName);
        $reflectionParameters = $method->getParameters();
        $preparedParams = array();
        /** @var $parameter ReflectionParameter */
        foreach ($reflectionParameters as $parameter) {
            $parameterName = $parameter->getName();
            if (isset($requestData[$parameterName])) {
                $preparedParams[$parameterName] = $requestData[$parameterName];
            } else {
                if ($parameter->isOptional()) {
                    $preparedParams[$parameterName] = $parameter->getDefaultValue();
                } else {
                    throw new RuntimeException($this->__('Required parameter "%s" is missing.', $parameterName));
                }
            }
        }
        return $preparedParams;
    }
}
