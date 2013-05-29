<?php
/**
 * Google AdWords Color Backend model
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Mage_GoogleAdwords_Model_Config_Backend_Color extends Mage_Core_Model_Config_Data
{
    /**
     * @var Magento_Validator_Composite_VarienObject
     */
    protected $_validator;

    /**
     * @var Magento_Validator_BuilderFactory
     */
    protected $_validatorBuilderFactory;

    /**
     * @var Mage_GoogleAdwords_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Core_Model_Context $context
     * @param Magento_Validator_Composite_VarienObject $validator
     * @param Magento_Validator_BuilderFactory $validatorBuilderFactory
     * @param Mage_GoogleAdwords_Helper_Data $helper
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     */
    public function __construct(
        Mage_Core_Model_Context $context,
        Magento_Validator_Composite_VarienObject $validator,
        Magento_Validator_BuilderFactory $validatorBuilderFactory, //add Factory
        Mage_GoogleAdwords_Helper_Data $helper,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null
    ) {
        parent::__construct($context, $resource, $resourceCollection);
        $this->_validatorBuilderFactory = $validatorBuilderFactory;

        $this->_validator = $validator;
        $this->_helper = $helper;
    }

    /**
     * Retrieve color validator
     *
     * @return Magento_Validator
     */
    protected function _getValidator()
    {
        $message = $this->_helper->__('Conversion Color value is not valid "%s". Please set hexadecimal 6-digit value.',
            $this->getValue());
        /** @var Magento_Validator_Builder $builder */
        $builder = $this->_validatorBuilderFactory->create(array(
                array(
                    array(
                        'alias' => 'Regex',
                        'type' => '',
                        'class' => 'Magento_Validator_Regex',
                        'options' => array(
                            'arguments' => array('/^[0-9a-f]{6}$/i'),
                            'methods' => array(
                                array(
                                    'method' => 'setMessages',
                                    'arguments' => array(
                                        array(
                                            Magento_Validator_Regex::NOT_MATCH => $message,
                                            Magento_Validator_Regex::INVALID => $message,
                                        )
                                    )
                                )
                            ),
                        ),
                    )
                ),
            )
        );
        return $builder->createValidator();
    }

    /**
     * Validation rule conversion color
     *
     * @return Zend_Validate_Interface|null
     */
    protected function _getValidationRulesBeforeSave()
    {
        $this->_validator->addRule($this->_getValidator(), 'conversion_color');
        return $this->_validator;
    }

    /**
     * Get tested value
     *
     * @return string
     */
    public function getConversionColor()
    {
        return $this->getValue();
    }
}
