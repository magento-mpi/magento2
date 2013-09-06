<?php
/**
 * Google AdWords Conversion Abstract Backend model
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @SuppressWarnings(PHPMD.LongVariable)
 */
abstract class Magento_GoogleAdwords_Model_Config_Backend_ConversionAbstract extends Magento_Core_Model_Config_Value
{
    /**
     * @var \Magento\Validator\Composite\VarienObject
     */
    protected $_validatorComposite;

    /**
     * @var Magento_GoogleAdwords_Model_Validator_Factory
     */
    protected $_validatorFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Validator_Composite_VarienObjectFactory $validatorCompositeFactory
     * @param Magento_GoogleAdwords_Model_Validator_Factory $validatorFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Validator_Composite_VarienObjectFactory $validatorCompositeFactory,
        Magento_GoogleAdwords_Model_Validator_Factory $validatorFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null
    ) {
        parent::__construct($context, $resource, $resourceCollection);

        $this->_validatorFactory = $validatorFactory;
        $this->_validatorComposite = $validatorCompositeFactory->create();
    }
}
