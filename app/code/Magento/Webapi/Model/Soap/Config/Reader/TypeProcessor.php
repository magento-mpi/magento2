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
    /** @var \Magento\Webapi\Helper\Config */
    protected $_helper;

    /**
     * Array of types data.
     *
     * @var array
     */
    protected $_types;

    /**
     * Types class map.
     *
     * @var array
     */
    protected $_typeToClassMap;

    /**
     * Construct type processor.
     *
     * @param \Magento\Webapi\Helper\Config $helper
     */
    public function __construct(\Magento\Webapi\Helper\Config $helper)
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
     * Retrieve processed types to class map.
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
     */
    public function process($type)
    {
        $typeName = $this->_helper->normalizeType($type);
        if (!$this->_helper->isTypeSimple($typeName)) {
            $complexTypeName = $this->_helper->translateTypeName($type);
            if (!isset($this->_types[$complexTypeName])) {
                $this->_processComplexType($type);
                if (!$this->_helper->isArrayType($complexTypeName)) {
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
        $typeName = $this->_helper->translateTypeName($class);
        $this->_types[$typeName] = array();
        if ($this->_helper->isArrayType($class)) {
            $this->process($this->_helper->getArrayItemType($class));
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
}
