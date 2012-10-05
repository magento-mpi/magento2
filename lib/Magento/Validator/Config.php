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
     * Create validator based on entity and group.
     *
     * @param string $entityName
     * @param string $groupName
     * @return Magento_Validator
     * @throws InvalidArgumentException
     */
    public function createValidator($entityName, $groupName)
    {
        if (!isset($this->_data[$entityName])) {
            throw new InvalidArgumentException(sprintf('Unknown validation entity "%s"', $entityName));
        }

        if (!isset($this->_data[$entityName][$groupName])) {
            throw new InvalidArgumentException(sprintf('Unknown validation group "%s" in entity "%s"', $groupName,
                $entityName));
        }

        $result = new Magento_Validator();
        foreach ($this->_data[$entityName][$groupName] as $constraintData) {
            $result->addValidator($this->_createConstraint($constraintData));
        }

        return $result;
    }

    /**
     * Create constraint from data
     *
     * @param array $data
     * @return Magento_Validator_Constraint
     */
    protected function _createConstraint(array $data)
    {
        $validator = new $data['class'];

        if (!($validator instanceof Magento_Validator_Interface)) {
            throw new InvalidArgumentException(sprintf(
                'Constraint class "%s" must implement Magento_Validator_Interface', $data['class']
            ));
        }

        if (self::CONSTRAINT_TYPE_PROPERTY == $data['type']) {
            $result = new Magento_Validator_Constraint_Property($validator, $data['property'], $data['id']);
        } else {
            $result = new Magento_Validator_Constraint($validator, $data['id']);
        }

        return $result;
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

            $result[$group->getAttribute('name')] = $groupConstraints;
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
                        'type' => self::CONSTRAINT_TYPE_ENTITY,
                    );
                }
            }
        }

        return $rules;
    }

    /**
     * Get absolute path to validation.xsd
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return __DIR__ . '/validation.xsd';
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
            '/validation/entity/rules/rule/constraints/constraint' => 'class',
            '/validation/entity/groups/group' => 'name',
            '/validation/entity/groups/group/uses/use' => 'rule',
        );
    }
}
