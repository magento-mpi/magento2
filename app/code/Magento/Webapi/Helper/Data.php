<?php
use \Zend\Server\Reflection\ReflectionMethod;

/**
 * Webapi module helper.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    /** @var \Magento\Webapi\Helper\Config */
    protected $_configHelper;

    /**
     * @param \Magento\Webapi\Helper\Config $configHelper
     * @param \Magento\Core\Helper\Context $context
     */
    public function __construct(\Magento\Webapi\Helper\Config $configHelper, \Magento\Core\Helper\Context $context)
    {
        parent::__construct($context);
        $this->_configHelper = $configHelper;
    }

    /**
     * Web API ACL resources tree root ID.
     */
    const RESOURCES_TREE_ROOT_ID = '__root__';

    /**
     * Reformat request data to be compatible with method specified interface: <br/>
     * - sort arguments in correct order <br/>
     * - set default values for omitted arguments
     * - instantiate objects of necessary classes
     *
     * @param string|object $classOrObject Resource class name
     * @param string $methodName Resource method name
     * @param array $requestData Data to be passed to method
     * @param \Magento\Webapi\Model\ConfigAbstract $apiConfig
     * @return array Array of prepared method arguments
     * @throws \Magento\Webapi\Exception
     */
    public function prepareMethodParams(
        $classOrObject,
        $methodName,
        $requestData,
        \Magento\Webapi\Model\ConfigAbstract $apiConfig
    ) {
        $methodReflection = self::createMethodReflection($classOrObject, $methodName);
        $methodData = $apiConfig->getMethodMetadata($methodReflection);
        $methodArguments = array();
        if (isset($methodData['interface']['in']['parameters'])
            && is_array($methodData['interface']['in']['parameters'])
        ) {
            foreach ($methodData['interface']['in']['parameters'] as $paramName => $paramData) {
                if (isset($requestData[$paramName])) {
                    $methodArguments[$paramName] = $this->_formatParamData(
                        $requestData[$paramName],
                        $paramData['type'],
                        $apiConfig
                    );
                } elseif (!$paramData['required']) {
                    $methodArguments[$paramName] = $paramData['default'];
                } else {
                    throw new \Magento\Webapi\Exception(__('Required parameter "%1" is missing.', $paramName),
                        \Magento\Webapi\Exception::HTTP_BAD_REQUEST);
                }
            }
        }
        return $methodArguments;
    }

    /**
     * Format $data according to specified $dataType recursively.
     *
     * Instantiate objects of proper classes and set data to its fields.
     *
     * @param mixed $data
     * @param string $dataType
     * @param \Magento\Webapi\Model\ConfigAbstract $apiConfig
     * @return mixed
     * @throws \LogicException If specified $dataType is invalid
     * @throws \Magento\Webapi\Exception If required fields do not have values specified in $data
     */
    protected function _formatParamData($data, $dataType, \Magento\Webapi\Model\ConfigAbstract $apiConfig)
    {
        if ($this->_configHelper->isTypeSimple($dataType) || is_null($data)) {
            $formattedData = $data;
        } elseif ($this->_configHelper->isArrayType($dataType)) {
            $formattedData = $this->_formatArrayData($data, $dataType, $apiConfig);
        } else {
            $formattedData = $this->_formatComplexObjectData($data, $dataType, $apiConfig);
        }
        return $formattedData;
    }

    /**
     * Format data of array type.
     *
     * @param array $data
     * @param string $dataType
     * @param \Magento\Webapi\Model\ConfigAbstract $apiConfig
     * @return array
     * @throws \Magento\Webapi\Exception If passed data is not an array
     */
    protected function _formatArrayData($data, $dataType, $apiConfig)
    {
        $itemDataType = $this->_configHelper->getArrayItemType($dataType);
        $formattedData = array();
        if (!is_array($data)) {
            throw new \Magento\Webapi\Exception(
                __('Data corresponding to "%1" type is expected to be an array.', $dataType),
                \Magento\Webapi\Exception::HTTP_BAD_REQUEST
            );
        }
        foreach ($data as $itemData) {
            $formattedData[] = $this->_formatParamData($itemData, $itemDataType, $apiConfig);
        }
        return $formattedData;
    }

    /**
     * Format data as object of the specified class.
     *
     * @param array|object $data
     * @param string $dataType
     * @param \Magento\Webapi\Model\ConfigAbstract $apiConfig
     * @return object Object of required data type
     * @throws \LogicException If specified $dataType is invalid
     * @throws \Magento\Webapi\Exception If required fields does not have values specified in $data
     */
    protected function _formatComplexObjectData($data, $dataType, $apiConfig)
    {
        $dataTypeMetadata = $apiConfig->getTypeData($dataType);
        $typeToClassMap = $apiConfig->getTypeToClassMap();
        if (!isset($typeToClassMap[$dataType])) {
            throw new \LogicException(sprintf('Specified data type "%s" does not match any class.', $dataType));
        }
        $complexTypeClass = $typeToClassMap[$dataType];
        if (is_object($data) && (get_class($data) == $complexTypeClass)) {
            /** In case of SOAP the object creation is performed by soap server. */
            return $data;
        }
        $complexDataObject = new $complexTypeClass();
        if (!is_array($data)) {
            throw new \Magento\Webapi\Exception(
                __('Data corresponding to "%1" type is expected to be an array.', $dataType),
                \Magento\Webapi\Exception::HTTP_BAD_REQUEST
            );
        }
        foreach ($dataTypeMetadata['parameters'] as $fieldName => $fieldMetadata) {
            if (isset($data[$fieldName])) {
                $fieldValue = $data[$fieldName];
            } elseif (($fieldMetadata['required'] == false)) {
                $fieldValue = $fieldMetadata['default'];
            } else {
                throw new \Magento\Webapi\Exception(__('Value of "%1" attribute is required.', $fieldName),
                    \Magento\Webapi\Exception::HTTP_BAD_REQUEST);
            }
            $complexDataObject->$fieldName = $this->_formatParamData(
                $fieldValue,
                $fieldMetadata['type'],
                $apiConfig
            );
        }
        return $complexDataObject;
    }

    /**
     * Create Zend method reflection object.
     *
     * @param string|object $classOrObject
     * @param string $methodName
     * @return \Zend\Server\Reflection\ReflectionMethod
     */
    public static function createMethodReflection($classOrObject, $methodName)
    {
        $methodReflection = new \ReflectionMethod($classOrObject, $methodName);
        $classReflection = new \ReflectionClass($classOrObject);
        $zendClassReflection = new \Zend\Server\Reflection\ReflectionClass($classReflection);
        $zendMethodReflection = new \Zend\Server\Reflection\ReflectionMethod($zendClassReflection, $methodReflection);
        return $zendMethodReflection;
    }
}
