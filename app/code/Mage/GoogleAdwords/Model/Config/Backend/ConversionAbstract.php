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
abstract class Mage_GoogleAdwords_Model_Config_Backend_ConversionAbstract extends Magento_Core_Model_Config_Data
{
    /**
     * @var Magento_Validator_Composite_VarienObject
     */
    protected $_validatorComposite;

    /**
     * @var Mage_GoogleAdwords_Model_Validator_Factory
     */
    protected $_validatorFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Validator_Composite_VarienObjectFactory $validatorCompositeFactory
     * @param Mage_GoogleAdwords_Model_Validator_Factory $validatorFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Validator_Composite_VarienObjectFactory $validatorCompositeFactory,
        Mage_GoogleAdwords_Model_Validator_Factory $validatorFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null
    ) {
        parent::__construct($context, $resource, $resourceCollection);

        $this->_validatorFactory = $validatorFactory;
        $this->_validatorComposite = $validatorCompositeFactory->create();
    }
}
