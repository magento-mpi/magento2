<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page tile helper
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Helper_Tile extends Mage_Core_Helper_Data
{
    /**
     * Pattern of XML path to <state_resolver> node
     */
    const XML_PATH_TILE_STATE_RESOLVER_PATTERN = 'adminhtml/launcher/tiles/%s/state_resolver';

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_applicationConfig;

    /**
     * Class constructor
     *
     * @param Mage_Core_Model_Config $applicationConfig
     */
    public function __construct(Mage_Core_Model_Config $applicationConfig)
    {
        $this->_applicationConfig = $applicationConfig;
    }

    /**
     * Retrieve class name of state resolver associated with tile that has given code
     *
     * @param string $tileCode
     * @return string
     * @throws Mage_Launcher_Exception
     */
    public function getStateResolverClassNameByTileCode($tileCode)
    {
        $className = (string)$this->_applicationConfig
            ->getNode(sprintf(self::XML_PATH_TILE_STATE_RESOLVER_PATTERN, $tileCode));

        if (empty($className)) {
            throw new Mage_Launcher_Exception('State Resolver is not defined for tile with code "' . $tileCode . '".');
        }

        return $className;
    }
}
