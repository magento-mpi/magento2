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
 * Tile Save Handler factory
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Tile_SaveHandlerFactory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Tile helper
     *
     * @var Mage_Launcher_Helper_Tile
     */
    protected $_tileHelper;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Launcher_Helper_Tile $tileHelper
     */
    public function __construct(Magento_ObjectManager $objectManager, Mage_Launcher_Helper_Tile $tileHelper)
    {
        $this->_objectManager = $objectManager;
        $this->_tileHelper = $tileHelper;
    }

    /**
     * Create new tile save handler model
     *
     * @param string $tileCode
     * @param array $arguments
     * @return Mage_Launcher_Model_Tile_SaveHandler
     */
    public function create($tileCode, array $arguments = array())
    {
        $className = $this->_tileHelper->getSaveHandlerClassNameByTileCode($tileCode);
        return $this->_objectManager->create($className, $arguments, false);
    }
}
