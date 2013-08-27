<?php
/**
 * Configuration objects invalidator. Invalidates all required configuration objects for total config reinitialisation
 *
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Invalidator implements Magento_Core_Model_Config_InvalidatorInterface
{
    /**
     * Primary configuration
     *
     * @var Magento_Core_Model_ConfigInterface
     */
    protected $_primaryConfig;

    /**
     * Modules configuration
     *
     * @var Magento_Core_Model_ConfigInterface
     */
    protected $_modulesConfig;

    /**
     * Locales configuration
     *
     * @var Magento_Core_Model_ConfigInterface
     */
    protected $_localesConfig;

    /**
     * @param Magento_Core_Model_ConfigInterface $primaryConfig
     * @param Magento_Core_Model_ConfigInterface $modulesConfig
     * @param Magento_Core_Model_ConfigInterface $localesConfig
     */
    public function __construct(
        Magento_Core_Model_ConfigInterface $primaryConfig,
        Magento_Core_Model_ConfigInterface $modulesConfig,
        Magento_Core_Model_ConfigInterface $localesConfig
    ) {
        $this->_primaryConfig = $primaryConfig;
        $this->_modulesConfig = $modulesConfig;
        $this->_localesConfig = $localesConfig;
    }

    /**
     * Invalidate config objects
     */
    public function invalidate()
    {
        $this->_primaryConfig->reinit();
        $this->_modulesConfig->reinit();
        $this->_localesConfig->reinit();
    }
}
