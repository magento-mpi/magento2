<?php
/**
 * Google AdWords Conversion Abstract Backend model
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
abstract class Mage_GoogleAdwords_Model_Config_Backend_ConversionAbstract extends Mage_Core_Model_Config_Data
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
     * @param Mage_Core_Model_Context $context
     * @param Magento_Validator_Composite_VarienObjectFactory $validatorCompositeFactory
     * @param Mage_GoogleAdwords_Model_Validator_Factory $validatorFactory
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     */
    public function __construct(
        Mage_Core_Model_Context $context,
        Magento_Validator_Composite_VarienObjectFactory $validatorCompositeFactory,
        Mage_GoogleAdwords_Model_Validator_Factory $validatorFactory,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null
    ) {
        parent::__construct($context, $resource, $resourceCollection);

        $this->_validatorFactory = $validatorFactory;
        $this->_validatorComposite = $validatorCompositeFactory->create();
    }
}
