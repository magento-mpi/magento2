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
 * Abstract state resolver for configuration based tiles
 *
 * Class that extends this state resolver must at least override its isTileComplete method
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Launcher_Model_Tile_ConfigBased_StateResolverAbstract
    extends Mage_Launcher_Model_Tile_MinimalStateResolver
{
    /**
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * Constructor
     *
     * @param Mage_Core_Model_App $app
     * @param Mage_Core_Model_Config $config
     */
    function __construct(
        Mage_Core_Model_App $app,
        Mage_Core_Model_Config $config
    ) {
        $this->_app = $app;
        $this->_config = $config;
    }
}
