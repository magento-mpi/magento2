<?php
/**
 * Validation configuration files handler
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Validator;

class Config extends \Magento\Config\AbstractXml
{
    /**#@+
     * Constraints types
     */
    const CONSTRAINT_TYPE_ENTITY = 'entity';
    const CONSTRAINT_TYPE_PROPERTY = 'property';
    /**#@-*/

    /**
     * @var string
     */
    protected $_defaultBuilderClass = 'Magento\Validator\Builder';

    /**
     * @var \Magento\Validator\UniversalFactory
     */
    protected $_builderFactory;

    /**
     * @param array $configFiles
     * @param \Magento\Validator\UniversalFactory $builderFactory
     */
    public function __construct(
        array $configFiles,
        \Magento\Validator\UniversalFactory $builderFactory
    ) {
        parent::__construct($configFiles);
        $this->_builderFactory = $builderFactory;
    }

    /**
     * Create validator builder instance based on entity and group.
     *
     * @param string $entityName
     * @param string $groupName
     * @param array|null $builderConfig
     * @throws \InvalidArgumentException
     * @return \Magento\Validator\Builder
     */
    public function createValidatorBuilder($entityName, $groupName, array $builderConfig = null)
    {
        if (!isset($this->_data[$entityName])) {
            throw new \InvalidArgumentException(sprintf('Unknown validation entity "%s"', $entityName));
        }

        if (!isset($this->_data[$entityName][$groupName])) {
            throw new \InvalidArgumentException(sprintf('Unknown validation group "%s" in entity "%s"', $groupName,
                $entityName));
        }

        $builderClass = isset($this->_data[$entityName][$groupName]['builder'])
            ? $this->_data[$entityName][$groupName]['builder'] : $this->_defaultBuilderClass;

        if (!class_exists($builderClass)) {
            throw new \InvalidArgumentException(sprintf('Builder class "%s" was not found', $builderClass));
        }

        $builder = $this->_builderFactory->create(
            $builderClass,
            array('constraints' => $this->_data[$entityName][$groupName]['constraints'])
        );
        if (!$builder instanceof \Magento\Validator\Builder) {
            throw new \InvalidArgumentException(
                sprintf('Builder "%s" must extend \Magento\Validator\Builder', $builderClass)
            );
        }
        if ($builderConfig) {
            $builder->addConfigurations($builderConfig);
        }
        return $builder;
    }

    /**
     * Create validator based on entity and group.
     *
     * @param string $entityName
     * @param string $groupName
     * @param array|null $builderConfig
     * @return \Magento\Validator
     */
    public function createValidator($entityName, $groupName, array $builderConfig = null)
    {
        return $this
            ->createValidatorBuilder($entityName, $groupName, $builderConfig)
            ->createValidator();
    }

    /**
     * Extract configuration data from the DOM structure
     *
     * @param \DOMDocument $dom
     * @return array
     */
    protected function _extractData(\DOMDocument $dom)
    {
        $result = array();

        /** @var \DOMElement $entity */
        foreach ($dom->getElementsByTagName('entity') as $entity) {
            $result[$entity->getAttribute('name')] = $this->_extractEntityGroupsConstraintsData($entity);
        }
        return $result;
    }

    /**
     * Extract constraints associated with entity group using rules
     *
     * @param \DOMElement $entity
     * @return array
     */
    protected function _extractEntityGroupsConstraintsData(\DOMElement $entity)
    {
        $result = array();
        $rulesConstraints = $this->_extractRulesConstraintsData($entity);

        /** @var \DOMElement $group */
        foreach ($entity->getElementsByTagName('group') as $group) {
            $groupConstraints = array();

            /** @var \DOMElement $use */
            foreach ($group->getElementsByTagName('use') as $use) {
                $ruleName = $use->getAttribute('rule');
                if (isset($rulesConstraints[$ruleName])) {
                    $groupConstraints = array_merge($groupConstraints, $rulesConstraints[$ruleName]);
                }
            }

            $result[$group->getAttribute('name')] = array(
                'constraints' => $groupConstraints
            );
            if ($group->hasAttribute('builder')) {
                $result[$group->getAttribute('name')]['builder'] = $group->getAttribute('builder');
            }
        }

        unset($groupConstraints);
        unset($rulesConstraints);

        return $result;
    }

    /**
     * Extract constraints associated with rules
     *
     * @param \DOMElement $entity
     * @return array
     */
    protected function _extractRulesConstraintsData(\DOMElement $entity)
    {
        $rules = array();
        /** @var \DOMElement $rule */
        foreach ($entity->getElementsByTagName('rule') as $rule) {
            $ruleName = $rule->getAttribute('name');

            /** @var \DOMElement $propertyConstraints */
            foreach ($rule->getElementsByTagName('property_constraints') as $propertyConstraints) {
                /** @var \DOMElement $property */
                foreach ($propertyConstraints->getElementsByTagName('property') as $property) {
                    /** @var \DOMElement $constraint */
                    foreach ($property->getElementsByTagName('constraint') as $constraint) {
                        $rules[$ruleName][] = array(
                            'alias' => $constraint->getAttribute('alias'),
                            'class' => $constraint->getAttribute('class'),
                            'options' => $this->_extractConstraintOptions($constraint),
                            'property' => $property->getAttribute('name'),
                            'type' => self::CONSTRAINT_TYPE_PROPERTY,
                        );
                    }
                }
            }

            /** @var \DOMElement $entityConstraints */
            foreach ($rule->getElementsByTagName('entity_constraints') as $entityConstraints) {
                /** @var \DOMElement $constraint */
                foreach ($entityConstraints->getElementsByTagName('constraint') as $constraint) {
                    $rules[$ruleName][] = array(
                        'alias' => $constraint->getAttribute('alias'),
                        'class' => $constraint->getAttribute('class'),
                        'options' => $this->_extractConstraintOptions($constraint),
                        'type' => self::CONSTRAINT_TYPE_ENTITY,
                    );
                }
            }
        }

        return $rules;
    }

    /**
     * Extract constraint options.
     *
     * @param \DOMElement $constraint
     * @return array|null
     */
    protected function _extractConstraintOptions(\DOMElement $constraint)
    {
        if (!$constraint->hasChildNodes()) {
            return null;
        }
        $options = array();
        $children = $this->_collectChildren($constraint);

        /**
         * Read constructor arguments
         *
         * <constraint class="Constraint">
         *     <argument>
         *         <option name="minValue">123</option>
         *         <option name="maxValue">234</option>
         *     </argument>
         *     <argument>0</argument>
         *     <argument>
         *         <callback class="Class" method="method" />
         *     </argument>
         * </constraint>
         */
        $arguments = $this->_readArguments($children);
        if ($arguments) {
            $options['arguments'] = $arguments;
        }

        /**
         * Read constraint configurator callback
         *
         * <constraint class="Constraint">
         *     <callback class="Magento\Customer\Helper\Data" method="configureValidator"/>
         * </constraint>
         */
        $callback = $this->_readCallback($children);
        if ($callback) {
            $options['callback'] = $callback;
        }

        /**
         * Read constraint method configuration
         */
        $methods = $this->_readMethods($children);
        if ($methods) {
            $options['methods'] = $methods;
        }
        return $options;
    }

    /**
     * Get element children.
     *
     * @param \DOMElement $element
     * @return array
     */
    protected function _collectChildren($element)
    {
        $children = array();
        /** @var $node \DOMElement */
        foreach ($element->childNodes as $node) {
            if (!$node instanceof \DOMElement) {
                continue;
            }
            $nodeName = strtolower($node->nodeName);
            if (!array_key_exists($nodeName, $children)) {
                $children[$nodeName] = array();
            }
            $children[$nodeName][] = $node;
        }
        return $children;
    }

    /**
     * Get arguments.
     *
     * @param array $children
     * @return array|null
     */
    protected function _readArguments($children)
    {
        if (array_key_exists('argument', $children)) {
            $arguments = array();
            /** @var $node \DOMElement */
            foreach ($children['argument'] as $node) {
                $nodeChildren = $this->_collectChildren($node);
                $callback = $this->_readCallback($nodeChildren);
                $options = $this->_readOptions($nodeChildren);
                if ($callback) {
                    $arguments[] = $callback[0];
                } elseif ($options) {
                    $arguments[] = $options;
                } else {
                    $argument = $node->textContent;
                    $arguments[] = new \Magento\Validator\Constraint\Option(trim($argument));
                }

            }
            return $arguments;
        }
        return null;
    }

    /**
     * Get callback rules.
     *
     * @param array $children
     * @return array|null
     */
    protected function _readCallback($children)
    {
        if (array_key_exists('callback', $children)) {
            $callbacks = array();
            /** @var $callbackData \DOMElement */
            foreach ($children['callback'] as $callbackData) {
                $callbacks[] = new \Magento\Validator\Constraint\Option\Callback(array(
                    trim($callbackData->getAttribute('class')),
                    trim($callbackData->getAttribute('method'))
                ), null, true);
            }
            return $callbacks;
        }
        return null;
    }

    /**
     * Get options array.
     *
     * @param array $children
     * @return array|null
     */
    protected function _readOptions($children)
    {
        if (array_key_exists('option', $children)) {
            $data = array();
            /** @var $option \DOMElement */
            foreach ($children['option'] as $option) {
                $value = trim($option->textContent);
                if ($option->hasAttribute('name')) {
                    $data[$option->getAttribute('name')] = $value;
                } else {
                    $data[] = $value;
                }
            }
            return new \Magento\Validator\Constraint\Option($data);
        }
        return null;
    }

    /**
     * Get methods configuration.
     *
     * Example of method configuration:
     * <constraint class="Constraint">
     *     <method name="setMaxValue">
     *         <argument>
     *             <option name="minValue">123</option>
     *             <option name="maxValue">234</option>
     *         </argument>
     *         <argument>0</argument>
     *         <argument>
     *             <callback class="Class" method="method" />
     *         </argument>
     *     </method>
     * </constraint>
     *
     * @param array $children
     * @return array|null
     */
    protected function _readMethods($children)
    {
        if (array_key_exists('method', $children)) {
            $methods = array();
            /** @var $method \DOMElement */
            foreach ($children['method'] as $method) {
                $children = $this->_collectChildren($method);
                $methodName = $method->getAttribute('name');
                $methodOptions = array(
                    'method' => $methodName
                );
                $arguments = $this->_readArguments($children);
                if ($arguments) {
                    $methodOptions['arguments'] = $arguments;
                }
                $methods[$methodName] = $methodOptions;
            }
            return $methods;
        }
        return null;
    }

    /**
     * Get absolute path to validation.xsd
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return __DIR__ . '/etc/validation.xsd';
    }

    /**
     * Get initial XML of a valid document.
     *
     * @return string
     */
    protected function _getInitialXml()
    {
        return '<?xml version="1.0" encoding="UTF-8"?><validation></validation>';
    }

    /**
     * Define id attributes for entities
     *
     * @return array
     */
    protected function _getIdAttributes()
    {
        return array(
            '/validation/entity' => 'name',
            '/validation/entity/rules/rule' => 'name',
            '/validation/entity/rules/rule/entity_constraints/constraint' => 'class',
            '/validation/entity/rules/rule/property_constraints/property/constraint' => 'class',
            '/validation/entity/rules/rule/property_constraints/property' => 'name',
            '/validation/entity/groups/group' => 'name',
            '/validation/entity/groups/group/uses/use' => 'rule',
        );
    }
}
