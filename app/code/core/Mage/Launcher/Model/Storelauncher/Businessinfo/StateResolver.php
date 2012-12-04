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
 * State resolver for BusinessInfo Tile
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Businessinfo_StateResolver implements Mage_Launcher_Model_Tile_StateResolver
{
    /**
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * Constructor
     *
     * @param Mage_Core_Model_App $app
     */
    function __construct(Mage_Core_Model_App $app)
    {
        $this->_app = $app;
    }

    /**
     * Resolve state
     *
     * @return bool
     */
    public function isTileComplete()
    {
        return $this->_app->getStore()->getConfig('trans_email/ident_general/email') != null;
    }

    /**
     * Handle System Configuration change (handle related event) and return new state
     *
     * @param string $sectionName
     * @param int $currentState current state of the tile
     * @return int result state
     */
    public function handleSystemConfigChange($sectionName, $currentState)
    {
        if (in_array($sectionName, array('trans_email')) && $this->isTileComplete()) {
            return Mage_Launcher_Model_Tile::STATE_COMPLETE;
        }
        return $currentState;
    }
}
