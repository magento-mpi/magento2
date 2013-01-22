<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Invalidator implements Mage_Core_Model_Config_InvalidatorInterface
{
    /**
     * @var Mage_Core_Model_ConfigInterface
     */
    protected $_primaryConfig;

    /**
     * @var Mage_Core_Model_ConfigInterface
     */
    protected $_modulesConfig;

    /**
     * @var Mage_Core_Model_ConfigInterface
     */
    protected $_localesConfig;

    public function __construct(
        Mage_Core_Model_ConfigInterface $primaryConfig,
        Mage_Core_Model_ConfigInterface $modulesConfig,
        Mage_Core_Model_ConfigInterface $localesConfig
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
