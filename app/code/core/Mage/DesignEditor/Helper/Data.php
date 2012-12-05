<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design Editor main helper
 */
class Mage_DesignEditor_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * XML path to VDE front name prefix
     */
    const XML_PATH_FRONT_NAME = 'frontend/vde/frontName';

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_configuration;

    /**
     * @param Mage_Core_Model_Config $configuration
     */
    public function __construct(Mage_Core_Model_Config $configuration)
    {
        $this->_configuration = $configuration;
    }

    /**
     * Get VDE front name prefix
     *
     * @return string
     */
    public function getFrontName()
    {
        return (string)$this->_configuration->getNode(self::XML_PATH_FRONT_NAME);
    }
}
