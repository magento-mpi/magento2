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
     * Web API ACL resources tree root ID
     */
    const RESOURCES_TREE_ROOT_ID = '__root__';

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

    /**
     * Convert objects and arrays to array recursively.
     *
     * @param  array|object $data
     */
    public function toArray(&$data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        if (is_array($data)) {
            foreach ($data as &$value) {
                if (is_array($value) or is_object($value)) {
                    $this->toArray($value);
                }
            }
        }
    }
}
