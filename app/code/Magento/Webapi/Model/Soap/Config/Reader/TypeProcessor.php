<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model\Soap\Config\Reader;

use Zend\Code\Reflection\ClassReflection;

/**
 * Type processor of config reader properties
 */
class TypeProcessor
{
    /** @var \Magento\Webapi\Helper\Data */
    protected $_helper;

    /**
     * Array of types data.
     *
     * @var array <pre>array(
     *     $complexTypeName => array(
     *         'documentation' => $typeDocumentation
     *         'parameters' => array(
     *             $firstParameter => array(
     *                 'type' => $type,
     *                 'required' => $isRequired,
     *                 'default' => $defaultValue,
     *                 'documentation' => $parameterDocumentation
     *             ),
     *             ...
     *         )
     *     ),
     *     ...
     * )</pre>
     */
    protected $_types = array();

    /**
     * Types class map.
     *
     * @var array <pre>array(
     *     $complexTypeName => $interfaceName,
     *     ...
     * )</pre>
     */
    protected $_typeToClassMap = array();

    /**
     * Construct type processor.
     *
     * @param \Magento\Webapi\Helper\Data $helper
     */
    public function __construct(\Magento\Webapi\Helper\Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Retrieve processed types data.
     *
     * @return array
     */
    public function getTypesData()
    {
        return $this->_types;
    }

    /**
     * Retrieve data type details for the given type name.
     *
     * @param string $typeName
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getTypeData($typeName)
    {
        if (!isset($this->_types[$typeName])) {
            throw new \InvalidArgumentException(sprintf('Data type "%s" is not declared.', $typeName));
        }
        return $this->_types[$typeName];
    }

    /**
     * Add or update type data in config.
     *
     * @param string $typeName
     * @param array $data
     */
    public function setTypeData($typeName, $data)
    {
        if (!isset($this->_types[$typeName])) {
            $this->_types[$typeName] = $data;
        } else {
            $this->_types[$typeName] = array_merge_recursive($this->_types[$typeName], $data);
        }
    }

    /**
     * Retrieve mapping of complex types defined in WSDL to real data classes.
     *
     * @return array
     */
    public function getTypeToClassMap()
    {
        return $this->_typeToClassMap;
    }

    /**
     * Process type name.
     * In case parameter type is a complex type (class) - process its properties.
     *
     * @param string $type
     * @return string
     * @throws \LogicException
     */
    public function process($type)
    {
        $typeName = $this->normalizeType($type);
        if (!$this->isTypeSimple($typeName)) {
            if ((!$this->isArrayType($type) && !class_exists($type))
                || !class_exists(str_replace('[]', '', $type))
            ) {
                throw new \LogicException(
                    sprintf('Class "%s" does not exist. Please note that namespace must be specified.', $type)
                );
            }
            $complexTypeName = $this->translateTypeName($type);
            if (!isset($this->_types[$complexTypeName])) {
                $this->_processComplexType($type);
                if (!$this->isArrayType($complexTypeName)) {
                    $this->_typeToClassMap[$complexTypeName] = $type;
                }
            }
            $typeName = $complexTypeName;
        }

        return $typeName;
    }

    /**
     * Retrieve complex type information from class public properties.
     *
     * @param string $class
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function _processComplexType($class)
    {
        $typeName = $this->translateTypeName($class);
        $this->_types[$typeName] = array();
        if ($this->isArrayType($class)) {
            $this->process($this->getArrayItemType($class));
        } else {
            if (!class_exists($class)) {
                throw new \InvalidArgumentException(
                    sprintf('Could not load the "%s" class as parameter type.', $class)
                );
            }
            $reflection = new ClassReflection($class);
            $docBlock = $reflection->getDocBlock();
            $this->_types[$typeName]['documentation'] = $docBlock ? $this->_getDescription($docBlock) : '';
            /** @var \Zend\Code\Reflection\MethodReflection $methodReflection */
            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $methodReflection) {
                $this->_processMethod($methodReflection, $typeName);
            }
        }

        return $this->_types[$typeName];
    }

    /**
     * Collect metadata for virtual field corresponding to current method if it is a getter (used in WSDL generation).
     *
     * @param \Zend\Code\Reflection\MethodReflection $methodReflection
     * @param string $typeName
     * @throws \InvalidArgumentException
     */
    protected function _processMethod(\Zend\Code\Reflection\MethodReflection $methodReflection, $typeName)
    {
        if (strpos($methodReflection->getName(), 'get') === 0) {
            $methodDocBlock = $methodReflection->getDocBlock();
            if (!$methodDocBlock) {
                throw new \InvalidArgumentException('Each getter must have description with @return annotation.');
            }
            $returnAnnotations = $methodDocBlock->getTags('return');
            if (empty($returnAnnotations)) {
                throw new \InvalidArgumentException('Getter return type must be specified using @return annotation.');
            }
            /** @var \Zend\Code\Reflection\DocBlock\Tag\ReturnTag $returnAnnotation */
            $returnAnnotation = current($returnAnnotations);
            $returnType = $returnAnnotation->getType();
            if (preg_match('/^(.+)\|null$/', $returnType, $matches)) {
                /** If return value is optional, alternative return type should be set to null */
                $returnType = $matches[1];
                $isRequired = false;
            } else {
                $isRequired = true;
            }
            $fieldName = $this->_helper->dtoGetterNameToFieldName($methodReflection->getName());
            $this->_types[$typeName]['parameters'][$fieldName] = array(
                'type' => $this->process($returnType),
                'required' => $isRequired,
                // TODO: Reconsider default values declaration strategy
                'default' => null,
                'documentation' => $returnAnnotation->getDescription()
            );
        }
    }

    /**
     * Get short and long description from docblock and concatenate.
     *
     * @param \Zend\Code\Reflection\DocBlockReflection $doc
     * @return string
     */
    protected function _getDescription(\Zend\Code\Reflection\DocBlockReflection $doc)
    {
        $shortDescription = $doc->getShortDescription();
        $longDescription = $doc->getLongDescription();

        $description = rtrim($shortDescription);
        $longDescription = str_replace(array("\n", "\r"), '', $longDescription);
        if (!empty($longDescription) && !empty($description)) {
            $description .= " ";
        }
        $description .= ltrim($longDescription);

        return $description;
    }

    /**
     * Normalize short type names to full type names.
     *
     * @param string $type
     * @return string
     */
    public function normalizeType($type)
    {
        $normalizationMap = array(
            'str' => 'string',
            'integer' => 'int',
            'bool' => 'boolean',
        );

        return isset($normalizationMap[$type]) ? $normalizationMap[$type] : $type;
    }

    /**
     * Check if given type is a simple type.
     *
     * @param string $type
     * @return bool
     */
    public function isTypeSimple($type)
    {
        if ($this->isArrayType($type)) {
            $type = $this->getArrayItemType($type);
        }

        return in_array($type, array('string', 'int', 'float', 'double', 'boolean'));
    }

    /**
     * Check if given type is an array of type items.
     * Example:
     * <pre>
     *  ComplexType[] -> array of ComplexType items
     *  string[] -> array of strings
     * </pre>
     *
     * @param string $type
     * @return bool
     */
    public function isArrayType($type)
    {
        return (bool)preg_match('/(\[\]$|^ArrayOf)/', $type);
    }

    /**
     * Get item type of the array.
     * Example:
     * <pre>
     *  ComplexType[] => ComplexType
     *  string[] => string
     *  int[] => integer
     * </pre>
     *
     * @param string $arrayType
     * @return string
     */
    public function getArrayItemType($arrayType)
    {
        return $this->normalizeType(str_replace('[]', '', $arrayType));
    }

    /**
     * Translate complex type class name into type name.
     *
     * Example:
     * <pre>
     *  Magento_Customer_Service_CustomerData => CustomerData
     *  Magento_Catalog_Service_ProductData => CatalogProductData
     * </pre>
     *
     * @param string $class
     * @return string
     * @throws \InvalidArgumentException
     */
    public function translateTypeName($class)
    {
        //TODO: Is 'Entity' also needed in the regex pattern
        if (preg_match('/\\\\?(.*)\\\\(.*)\\\\Service\\\\\2?(.*)/', $class, $matches)) {
            $moduleNamespace = $matches[1] == 'Magento' ? '' : $matches[1];
            $moduleName = $matches[2];
            $typeNameParts = explode('\\', $matches[3]);

            return ucfirst($moduleNamespace . $moduleName . implode('', $typeNameParts));
        }
        throw new \InvalidArgumentException(sprintf('Invalid parameter type "%s".', $class));
    }

    /**
     * Translate array complex type name.
     *
     * Example:
     * <pre>
     *  ComplexTypeName[] => ArrayOfComplexTypeName
     *  string[] => ArrayOfString
     * </pre>
     *
     * @param string $type
     * @return string
     */
    public function translateArrayTypeName($type)
    {
        return 'ArrayOf' . ucfirst($this->getArrayItemType($type));
    }
}
