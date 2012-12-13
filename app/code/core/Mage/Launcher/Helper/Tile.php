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
     * Pattern of XML path to <save_handler> node
     */
    const XML_PATH_TILE_SAVE_HANDLER_PATTERN = 'adminhtml/launcher/tiles/%s/save_handler';

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
        return $this->_getClassNameByTileCode('State Resolver', $tileCode, self::XML_PATH_TILE_STATE_RESOLVER_PATTERN);
    }

    /**
     * Retrieve class name of save handler associated with tile that has given code
     *
     * @param string $tileCode
     * @return string
     * @throws Mage_Launcher_Exception
     */
    public function getSaveHandlerClassNameByTileCode($tileCode)
    {
        return $this->_getClassNameByTileCode('Save Handler', $tileCode, self::XML_PATH_TILE_SAVE_HANDLER_PATTERN);
    }

    /**
     * Retrieve class name by given tile code and section path
     *
     * @param string $entity name of the entity whose class is retrieved
     * @param string $tileCode
     * @param string $sectionPath
     * @return string
     * @throws Mage_Launcher_Exception
     */
    protected function _getClassNameByTileCode($entity, $tileCode, $sectionPath)
    {
        $className = (string)$this->_applicationConfig
            ->getNode(sprintf($sectionPath, $tileCode));

        if (empty($className)) {
            throw new Mage_Launcher_Exception($entity. ' is not defined for tile with code "' . $tileCode . '".');
        }

        return $className;
    }
}
