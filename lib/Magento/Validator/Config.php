<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Validator
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Validation configuration files handler
 */
class Magento_Validator_Config extends Magento_Config_XmlAbstract
{
    /**
     * Constraints types
     */
    const CONSTRAINT_TYPE_ENTITY = 'entity';
    const CONSTRAINT_TYPE_PROPERTY = 'property';

    /**
     * @var array
     */
    protected $_validatorBuilders = array();

    /**
     * Get absolute path to validation.xsd
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return __DIR__ . '/validation.xsd';
    }

    public function getData()
    {
        return $this->_data;
    }

    /**
     * Get validator builder instance
     *
     * @param $entityName
     * @param $groupName
     * @param array $builderConfig
     * @return Magento_Validator_Builder
     */
    public function getValidatorBuilder($entityName, $groupName, array $builderConfig = null)
    {
        $builderKey = $entityName . '/' . $groupName;
        if (!array_key_exists($builderKey, $this->_validatorBuilders)) {
            if (array_key_exists('builder', $this->_data[$entityName][$groupName])) {
                $builderClass = $this->_data[$entityName][$groupName]['builder'];
            } else {
                $builderClass =  'Magento_Validator_Builder';
            }
            $this->_validatorBuilders[$builderKey] =
                new $builderClass($this->_data[$entityName][$groupName]['constraints']);
        }
        if ($builderConfig) {
            $this->_validatorBuilders[$builderKey]->addConfigurations($builderConfig);
        }
        return $this->_validatorBuilders[$builderKey];
    }

    /**
     * Create validator based on entity and group.
     *
     * @param string $entityName
     * @param string $groupName
     * @param array $builderConfig
     * @throws InvalidArgumentException
     * @return Magento_Validator
     */
    public function createValidator($entityName, $groupName, array $builderConfig = null)
    {
        if (!isset($this->_data[$entityName])) {
            throw new InvalidArgumentException(sprintf('Unknown validation entity "%s"', $entityName));
        }

        if (!isset($this->_data[$entityName][$groupName])) {
            throw new InvalidArgumentException(sprintf('Unknown validation group "%s" in entity "%s"', $groupName,
                $entityName));
        }

        return $this
            ->getValidatorBuilder($entityName, $groupName, $builderConfig)
            ->createValidator();
    }

    /**
     * Extract configuration data from the DOM structure
     *
     * @param DOMDocument $dom
     * @return array
     */
    protected function _extractData(DOMDocument $dom)
    {
        $result = array();

        /** @var DOMElement $entity */
        foreach ($dom->getElementsByTagName('entity') as $entity) {
            $result[$entity->getAttribute('name')] = $this->_extractEntityGroupsConstraintsData($entity);
        }
        return $result;
    }

    /**
     * Extract constraints associated with entity group using rules
     *
     * @param DOMElement $entity
     * @return array
     */
    protected function _extractEntityGroupsConstraintsData(DOMElement $entity)
    {
        $result = array();
        $rulesConstraints = $this->_extractRulesConstraintsData($entity);

        /** @var DOMElement $group */
        foreach ($entity->getElementsByTagName('group') as $group) {
            $groupConstraints = array();

            /** @var DOMElement $use */
            foreach ($group->getElementsByTagName('use') as $use) {
                $ruleName = $use->getAttribute('rule');
                if (isset($rulesConstraints[$ruleName])) {
                    $groupConstraints = array_merge($groupConstraints, $rulesConstraints[$ruleName]);
                }
            }

            $result[$group->getAttribute('name')] = array(
                'constraints' => $groupConstraints
            );
            $autoLoader = Magento_Autoload::getInstance();
            if ($group->hasAttribute('builder') && $autoLoader->classExists($group->getAttribute('builder'))) {
                $result[$group->getAttribute('name')]['builder'] = (string)$group->getAttribute('builder');
            }
        }

        unset($groupConstraints);
        unset($rulesConstraints);

        return $result;
    }

    /**
     * Extract constraints associated with rules
     *
     * @param DOMElement $entity
     * @return array
     */
    protected function _extractRulesConstraintsData(DOMElement $entity)
    {
        $rules = array();
        /** @var DOMElement $rule */
        foreach ($entity->getElementsByTagName('rule') as $rule) {
            $ruleName = $rule->getAttribute('name');

            /** @var DOMElement $propertyConstraints */
            foreach ($rule->getElementsByTagName('property_constraints') as $propertyConstraints) {
                /** @var DOMElement $property */
                foreach ($propertyConstraints->getElementsByTagName('property') as $property) {
                    /** @var DOMElement $constraint */
                    foreach ($property->getElementsByTagName('constraint') as $constraint) {
                        $rules[$ruleName][] = array(
                            'id' => $constraint->getAttribute('id'),
                            'class' => $constraint->getAttribute('class'),
                            'options' => $this->_extractRuleOptions($rule),
                            'property' => $property->getAttribute('name'),
                            'type' => self::CONSTRAINT_TYPE_PROPERTY,
                        );
                    }
                }
            }

            /** @var DOMElement $entityConstraints */
            foreach ($rule->getElementsByTagName('entity_constraints') as $entityConstraints) {
                /** @var DOMElement $constraint */
                foreach ($entityConstraints->getElementsByTagName('constraint') as $constraint) {
                    $rules[$ruleName][] = array(
                        'id' => $constraint->getAttribute('id'),
                        'class' => $constraint->getAttribute('class'),
                        'options' => $this->_extractRuleOptions($rule),
                        'type' => self::CONSTRAINT_TYPE_ENTITY,
                    );
                }
            }
        }

        return $rules;
    }

    /**
     * Extract rule options
     *
     * @param DOMElement $rule
     * @return array|null
     */
    protected function _extractRuleOptions(DOMElement $rule)
    {
        $options = null;

        if ($rule->hasChildNodes()) {
            $options = array();

            /**
             * Read constructor arguments
             *
             * <constraint class="Constraint">
             *     <option name="minValue">123</option>
             * </constraint>
             */
            $arguments = $this->_readRuleArguments($rule);
            if ($arguments) {
                $options[] = array(
                    'arguments' => $arguments
                );
            }

            /**
             * Read constraint configurator callback
             *
             * <constraint class="Constraint">
             *     <callback class="Mage_Customer_Helper_Data" method="configureValidator"/>
             * </constraint>
             */
            $callback = $this->_readRuleCallback($rule);
            if ($callback) {
                $options[] = array(
                    'callback' => $callback
                );
            }

            /**
             * Read constraint method configuration
             */
            $methods = $rule->getElementsByTagName('method');
            if ($methods->length > 0) {
                /** @var $method DOMNode */
                foreach ($methods as $method) {
                    $methodOptions = array(
                        'method' => $method->attributes->getNamedItem('name')
                    );

                    /**
                     * <constraint class="Constraint">
                     *     <method name="setMaxValue">
                     *         <option name="minValue">123</option>
                     *     </method>
                     * </constraint>
                     */
                    $arguments = $this->_readRuleArguments($rule);
                    if ($arguments) {
                        $options[] = array(
                            'arguments' => $arguments
                        );
                    }

                    /**
                     * <constraint class="Constraint">
                     *     <method name="setMaxValue">
                     *        <callback class="Mage_Customer_Helper_Data" method="getMaxValue"/>
                     *     </method>
                     * </constraint>
                     */
                    $callback = $this->_readRuleCallback($method);
                    if ($callback) {
                        $methodOptions['callback'] = $callback;
                    }

                    $options[] = $methodOptions;
                }
            }
        }
        return $options;
    }

    /**
     * Get arguments
     *
     * @param DOMElement $parent
     * @return array|null
     */
    protected function _readRuleArguments(DOMElement $parent)
    {
        $nodes = $parent->getElementsByTagName('option');
        if ($nodes->length > 0) {
            $arguments = array();
            /** @var $node DOMNode */
            foreach ($nodes as $node) {
                $arguments[] = $node->textContent;
            }
            return $arguments;
        }
        return null;
    }

    /**
     * Get callback rules
     *
     * @param DOMElement $parent
     * @return array|null
     */
    protected function _readRuleCallback(DOMElement $parent)
    {
        $callback = $parent->getElementsByTagName('callback');
        if ($callback->length > 0) {
            return array(
                'class' => $callback->item(0)->attributes->getNamedItem('name'),
                'method' => $callback->item(0)->attributes->getNamedItem('method')
            );
        }
        return null;
    }

    /**
     * Get initial XML of a valid document
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
