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
 * Base Tile Block
 *
 * @method Mage_Launcher_Model_Tile getTile()
 * @method Mage_Launcher_Block_Adminhtml_Tile setTile(Mage_Launcher_Model_Tile $value)
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Adminhtml_Tile extends Mage_Backend_Block_Abstract
{
    /**
     * Get Tile Code
     *
     * @throws Mage_Launcher_Exception
     * @return string
     */
    public function getTileCode()
    {
        $tile = $this->getTile();
        if (!isset($tile)) {
            throw new Mage_Launcher_Exception('Tile was not set.');
        }
        return $tile->getCode();
    }

    /**
     * Get Tile State
     *
     * @throws Mage_Launcher_Exception
     * @return int
     */
    public function getTileState()
    {
        $tile = $this->getTile();
        if (!isset($tile)) {
            throw new Mage_Launcher_Exception('Tile was not set.');
        }
        return $tile->getState();
    }

    /**
     * Get CSS style based on current Tile's state
     *
     * @return string
     */
    public function getTileStateStyle()
    {
        $tileStyle = '';
        switch ($this->getTileState()) {
            case Mage_Launcher_Model_Tile::STATE_TODO :
                $tileStyle = 'sl-step-todo';
                break;
            case Mage_Launcher_Model_Tile::STATE_COMPLETE :
                $tileStyle = 'sl-step-complete';
                break;
            case Mage_Launcher_Model_Tile::STATE_SKIPPED :
                $tileStyle = 'sl-step-skipped';
                break;
            case Mage_Launcher_Model_Tile::STATE_DISMISSED :
                $tileStyle = 'sl-step-dismissed';
                break;
            default:
                $tileStyle = 'sl-step-todo';
        }
        return $tileStyle;
    }

    /**
     * Get Response Content
     *
     * @return array
     */
    public function getResponseContent()
    {
        $responseContent = array(
            'success' => true,
            'error_message' => '',
            'tile_code' => $this->getTileCode(),
            'tile_state' => $this->getTileState(),
            'tile_content' => $this->toHtml()
        );
        return $responseContent;
    }
}
