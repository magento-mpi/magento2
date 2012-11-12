<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_ValidatorFactory
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
     * Validator config files
     *
     * @var array
     */
    protected $_validatorConfigFiles = null;

    /**
     * Initialize dependencies
     *
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Config $config
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Config $config
    ) {
        $this->_objectManager = $objectManager;
        $this->_config = $config;
    }

    /**
     * Get validator config object.
     *
     * Will instantiate Magento_Validator_Config
     *
     * @return Magento_Validator_Config
     */
    public function create()
    {
        if (is_null($this->_validatorConfigFiles)) {
            $this->_validatorConfigFiles = $this->_config->getModuleConfigurationFiles('validation.xml');

            $translateAdapter = $this->_getTranslator();
            $translatorCallback = function () use ($translateAdapter) {
                /** @var Mage_Core_Model_Translate $translateAdapter */
                $args = func_get_args();
                $expr = new Mage_Core_Model_Translate_Expr($args[0]);
                array_unshift($args, $expr);
                return $translateAdapter->translate($args);
            };
            $translator = new Magento_Translate_Adapter(array(
                'translator' => $translatorCallback
            ));
            Magento_Validator_ValidatorAbstract::setDefaultTranslator($translator);
        }

        return new Magento_Validator_Config($this->_validatorConfigFiles);
    }

    /**
     * Get translator instance.
     *
     * @return Mage_Core_Model_Translate
     */
    protected function _getTranslator()
    {
        return Mage::app()->getTranslator();
    }
}
