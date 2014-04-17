<?php
/**
 * Magento validator config factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Validator;

class Factory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * Validator config files
     *
     * @var array|null
     */
    protected $_configFiles = null;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param \Magento\Module\Dir\Reader $moduleReader
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        \Magento\Module\Dir\Reader $moduleReader
    ) {
        $this->_objectManager = $objectManager;
        $this->_configFiles = $moduleReader->getConfigurationFiles('validation.xml');
        $this->_initializeDefaultTranslator();
    }

    /**
     * Create and set default translator to \Magento\Validator\AbstractValidator.
     *
     * @return void
     */
    protected function _initializeDefaultTranslator()
    {
        // Pass translations to \Magento\TranslateInterface from validators
        $translatorCallback = function () {
            $argc = func_get_args();
            return (string)new \Magento\Phrase(array_shift($argc), $argc);
        };
        /** @var \Magento\Translate\Adapter $translator */
        $translator = $this->_objectManager->create('Magento\Translate\Adapter');
        $translator->setOptions(array('translator' => $translatorCallback));
        \Magento\Validator\AbstractValidator::setDefaultTranslator($translator);
    }

    /**
     * Get validator config object.
     *
     * Will instantiate \Magento\Validator\Config
     *
     * @return \Magento\Validator\Config
     */
    public function getValidatorConfig()
    {
        return $this->_objectManager->create('Magento\Validator\Config', array('configFiles' => $this->_configFiles));
    }

    /**
     * Create validator builder instance based on entity and group.
     *
     * @param string $entityName
     * @param string $groupName
     * @param array|null $builderConfig
     * @return \Magento\Validator\Builder
     */
    public function createValidatorBuilder($entityName, $groupName, array $builderConfig = null)
    {
        return $this->getValidatorConfig()->createValidatorBuilder($entityName, $groupName, $builderConfig);
    }

    /**
     * Create validator based on entity and group.
     *
     * @param string $entityName
     * @param string $groupName
     * @param array|null $builderConfig
     * @return \Magento\Validator
     */
    public function createValidator($entityName, $groupName, array $builderConfig = null)
    {
        return $this->getValidatorConfig()->createValidator($entityName, $groupName, $builderConfig);
    }
}
