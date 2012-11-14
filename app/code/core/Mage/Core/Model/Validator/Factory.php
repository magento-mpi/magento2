<?php
/**
 * Magento validator config factory
 *
 * @copyright {}
 */
class Mage_Core_Model_Validator_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Mage_Core_Model_Translate
     */
    protected $_translator;

    /**
     * Validator config files
     *
     * @var array
     */
    protected $_configFiles = null;

    /**
     * Initialize dependencies
     *
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Config $config
     * @param Mage_Core_Model_Translate $translator
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Config $config,
        Mage_Core_Model_Translate $translator
    ) {
        $this->_objectManager = $objectManager;
        $this->_config = $config;
        $this->_translator = $translator;

        $this->_initializeDefaultTranslator();
    }

    /**
     * Create and set default translator to Magento_Validator_ValidatorAbstract.
     */
    protected function _initializeDefaultTranslator()
    {
        $this->_configFiles = $this->_config->getModuleConfigurationFiles('validation.xml');

        $translateAdapter = $this->_translator;
        $objectManager = $this->_objectManager;
        // Pass translations to Mage_Core_Model_Translate from validators
        $translatorCallback = function () use ($translateAdapter, $objectManager) {
            /** @var Mage_Core_Model_Translate $translateAdapter */
            $args = func_get_args();
            $expr = $objectManager->create('Mage_Core_Model_Translate_Expr');
            $expr->setText($args[0]);
            array_unshift($args, $expr);
            return $translateAdapter->translate($args);
        };
        /** @var Magento_Translate_Adapter $translator */
        $translator = $this->_objectManager->create('Magento_Translate_Adapter');
        $translator->setOptions(array('translator' => $translatorCallback));
        Magento_Validator_ValidatorAbstract::setDefaultTranslator($translator);
    }

    /**
     * Create validator config object.
     *
     * Will instantiate Magento_Validator_Config
     *
     * @return Magento_Validator_Config
     */
    public function createValidatorConfig()
    {
        return $this->_objectManager
            ->create('Magento_Validator_Config', array('configFiles' => $this->_configFiles));
    }
}
