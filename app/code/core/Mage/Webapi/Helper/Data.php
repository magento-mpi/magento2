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
     * @throws Mage_Webapi_Exception
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
                //region TODO: Temporary workaround until implementation using resource configuration
                if ($parameterName == 'data') {
                    $customerData = new Mage_Customer_Webapi_Customer_DataStructure();
                    foreach ($requestData[$parameterName] as $fieldName => $fieldValue) {
                        $customerData->$fieldName = $fieldValue;
                    }
                    $requestData[$parameterName] = $customerData;
                }
                //endregion
                $preparedParams[$parameterName] = $requestData[$parameterName];
            } else {
                if ($parameter->isOptional()) {
                    $preparedParams[$parameterName] = $parameter->getDefaultValue();
                } else {
                    throw new Mage_Webapi_Exception($this->__('Required parameter "%s" is missing.', $parameterName),
                        Mage_Webapi_Exception::HTTP_BAD_REQUEST);
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
    // TODO: Remove if not used anymore
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

    /**
     * Convert singular form of word to plural.
     *
     * @param string $singular
     * @return string
     */
    public function convertSingularToPlural($singular)
    {
        $plural = $singular;
        $conversionMatrix = array(
            '/(x|ch|ss|sh)$/i' => "$1es",
            '/([^aeiouy]|qu)y$/i' => "$1ies",
            '/s$/i' => "s",
            '/$/' => "s"
        );
        foreach ($conversionMatrix as $singularPattern => $pluralPattern) {
            if (preg_match($singularPattern, $singular)) {
                $plural = preg_replace($singularPattern, $pluralPattern, $singular);
            }
        }
        return $plural;
    }
}
