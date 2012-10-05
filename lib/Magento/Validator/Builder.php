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
 * Validator builder
 */
class Magento_Validator_Builder
{
    protected $_configuration = array();
    protected $_constraints;

    public function __construct($constraints)
    {
        $this->_constraints = $constraints;
    }

    /**
     * Create validator instance
     *
     * @return Magento_Validator
     */
    public function createValidator()
    {
        $validator = new Magento_Validator();
        foreach ($this->_constraints as $constraintData) {
            $validator->addValidator($this->_createConstraint($constraintData));
        }
        return $validator;
    }

    public function addConfiguration($key, array $configuration)
    {
        $this->_configuration[$key] = $configuration;
    }

    public function addConfigurations(array $configurations)
    {
        foreach ($configurations as $key => $configuration) {
            $this->addConfiguration($key, $configuration);
        }
    }

    /**
     * Create constraint from data
     *
     * @param array $data
     * @throws InvalidArgumentException
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

        if (Magento_Validator_Config::CONSTRAINT_TYPE_PROPERTY == $data['type']) {
            $result = new Magento_Validator_Constraint_Property($validator, $data['property'], $data['id']);
        } else {
            $result = new Magento_Validator_Constraint($validator, $data['id']);
        }

        return $result;
    }
}
