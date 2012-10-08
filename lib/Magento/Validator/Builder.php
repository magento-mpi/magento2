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
 * Magento Validator Builder
 */
class Magento_Validator_Builder
{
    /**
     * @var array
     */
    protected $_constraints;

    /**
     * Set constraints
     *
     * @param array $constraints
     */
    public function __construct(array $constraints)
    {
        $this->_constraints = $constraints;
    }

    /**
     * Create validator instance and configure it
     *
     * @return Magento_Validator
     */
    public function createValidator()
    {
        return $this->_createValidatorInstance();
    }

    /**
     * Get validator instance
     *
     * @return Magento_Validator
     */
    protected function _createValidatorInstance()
    {
        $validator = new Magento_Validator();
        foreach ($this->_constraints as $constraintData) {
            $validator->addValidator($this->_createConstraint($constraintData));
        }
        return $validator;
    }

    /**
     * Add constraint configuration
     *
     * @param string $alias
     * @param array $configuration
     * @return Magento_Validator_Builder
     */
    public function addConfiguration($alias, array $configuration)
    {
        foreach ($this->_constraints as &$constraint) {
            if ($constraint['alias'] != $alias) {
                continue;
            }
            if (!array_key_exists('options', $constraint)) {
                $constraint['options'] = array();
            }
            if (!array_key_exists('method', $configuration)) {
                if (array_key_exists('arguments', $configuration)) {
                    $constraint['options']['arguments'] = $configuration['arguments'];
                } elseif (array_key_exists('callback', $configuration)) {
                    if (!array_key_exists('callback', $constraint['options'])) {
                        $constraint['options'] = array();
                    }
                    $constraint['options']['callback'][] = $configuration['callback'];
                }
            } else {
                if (!array_key_exists('methods', $constraint['options'])) {
                    $constraint['options']['methods'] = array();
                }
                $constraint['options']['methods'][$configuration['method']]
                    = $configuration;
            }
        }

        return $this;
    }

    /**
     * Add constraints configuration
     *
     * @param array $configurations
     * @return Magento_Validator_Builder
     */
    public function addConfigurations(array $configurations)
    {
        foreach ($configurations as $alias => $concreteConfigs) {
            foreach ($concreteConfigs as $configuration) {
                $this->addConfiguration($alias, $configuration);
            }
        }
        return $this;
    }

    /**
     * Create constraint from data
     *
     * @param array $data
     * @return Magento_Validator_Constraint
     */
    protected function _createConstraint(array $data)
    {
        // Create validator instance
        $validator = $this->_createConstraintValidator($data);
        if (array_key_exists('options', $data) && is_array($data['options'])) {
            $this->_configureConstraintValidator($validator, $data['options']);
        }

        if (Magento_Validator_Config::CONSTRAINT_TYPE_PROPERTY == $data['type']) {
            $result = new Magento_Validator_Constraint_Property($validator, $data['property'], $data['alias']);
        } else {
            $result = new Magento_Validator_Constraint($validator, $data['alias']);
        }

        return $result;
    }

    /**
     * Create constraint validator instance
     *
     * @param array $data
     * @throws InvalidArgumentException
     * @return Magento_Validator_Interface
     */
    protected function _createConstraintValidator(array $data)
    {
        if (array_key_exists('options', $data)
            && is_array($data['options']) && array_key_exists('arguments', $data['options'])
        ) {
            $arguments = $this->_applyArgumentsCallback($data['options']['arguments']);
            $class = new ReflectionClass($data['class']);
            $validator = $class->newInstanceArgs($arguments);
        } else {
            $validator = new $data['class'];
        }

        // Check validator type
        if (!($validator instanceof Magento_Validator_Interface)) {
            throw new InvalidArgumentException(sprintf(
                'Constraint class "%s" must implement Magento_Validator_Interface', $data['class']
            ));
        }

        return $validator;
    }

    /**
     * Configure validator
     *
     * @param Magento_Validator_Interface $validator
     * @param array $options
     */
    protected function _configureConstraintValidator(Magento_Validator_Interface $validator, array $options)
    {
        // Call all validator methods according to configuration
        if (array_key_exists('methods', $options)) {
            foreach ($options['methods'] as $methodName => $methodData) {
                if (array_key_exists('arguments', $methodData)) {
                    if (method_exists($validator, $methodName)) {
                        $arguments = $this->_applyArgumentsCallback($methodData['arguments']);
                        call_user_func_array(array($validator, $methodName), $arguments);
                    }
                }
            }
        }

        // Call validator configurators if any
        if (array_key_exists('callback', $options)) {
            /** @var $callback Magento_Validator_Constraint_Option_Callback */
            foreach ($options['callback'] as $callback) {
                $callback->setArguments($validator);
                $callback->getValue();
            }
        }
    }

    /**
     * Apply all argument callback
     *
     * @param array $arguments
     * @return array
     */
    protected function _applyArgumentsCallback(array $arguments)
    {
        foreach ($arguments as &$argument) {
            if ($argument instanceof Magento_Validator_Constraint_OptionInterface) {
                /** @var $argument Magento_Validator_Constraint_OptionInterface */
                $argument = $argument->getValue();
            }
        }
        return $arguments;
    }
}
