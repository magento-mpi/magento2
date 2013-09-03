<?php
/**
 * Google AdWords Validator Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_GoogleAdwords_Model_Validator_Factory
{
    /**
     * @var Magento_Validator_BuilderFactory
     */
    protected $_validatorBuilderFactory;

    /**
     * @param Magento_Validator_BuilderFactory $validatorBuilderFactory
     */
    public function __construct(Magento_Validator_BuilderFactory $validatorBuilderFactory)
    {
        $this->_validatorBuilderFactory = $validatorBuilderFactory;
    }

    /**
     * Create color validator
     *
     * @param string $currentColor
     * @return \Magento\Validator
     */
    public function createColorValidator($currentColor)
    {
        $message = __('Conversion Color value is not valid "%1". Please set hexadecimal 6-digit value.',
            $currentColor);
        /** @var \Magento\Validator\Builder $builder */
        $builder = $this->_validatorBuilderFactory->create(array(
            'constraints' => array(
                array(
                    'alias' => 'Regex',
                    'type' => '',
                    'class' => '\Magento\Validator\Regex',
                    'options' => array(
                        'arguments' => array('/^[0-9a-f]{6}$/i'),
                        'methods' => array(
                            array(
                                'method' => 'setMessages',
                                'arguments' => array(
                                    array(
                                        \Magento\Validator\Regex::NOT_MATCH => $message,
                                        \Magento\Validator\Regex::INVALID => $message,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ));
        return $builder->createValidator();
    }

    /**
     * Create Conversion id validator
     *
     * @param int|string $currentId
     * @return \Magento\Validator
     */
    public function createConversionIdValidator($currentId)
    {
        $message = __('Conversion Id value is not valid "%1". Conversion Id should be an integer.',
            $currentId);
        /** @var \Magento\Validator\Builder $builder */
        $builder = $this->_validatorBuilderFactory->create(array(
            'constraints' => array(
                array(
                    'alias' => 'Int',
                    'type' => '',
                    'class' => '\Magento\Validator\Int',
                    'options' => array(
                        'methods' => array(
                            array(
                                'method' => 'setMessages',
                                'arguments' => array(
                                    array(
                                        \Magento\Validator\Int::NOT_INT => $message,
                                        \Magento\Validator\Int::INVALID => $message,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ));
        return $builder->createValidator();
    }
}
