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
class Mage_Core_Model_Config_Invalidator implements Mage_Core_Model_Config_InvalidatorInterface
{
    /**
     * Primary configuration
     *
     * @var Mage_Core_Model_ConfigInterface
     */
    protected $_primaryConfig;

    /**
     * @param Mage_Core_Model_ConfigInterface $primaryConfig
     */
    public function __construct(
        Mage_Core_Model_ConfigInterface $primaryConfig
    ) {
        $this->_primaryConfig = $primaryConfig;
    }

    /**
     * Invalidate config objects
     */
    public function invalidate()
    {
        $this->_primaryConfig->reinit();
    }
}
