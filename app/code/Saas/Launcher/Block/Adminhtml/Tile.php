<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base Tile Block
 *
 * @method Saas_Launcher_Model_Tile getTile()
 * @method Saas_Launcher_Block_Adminhtml_Tile setTile(Saas_Launcher_Model_Tile $value)
 *
 * @category   Magento
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Saas_Launcher_Block_Adminhtml_Tile extends Magento_Backend_Block_Template
{
    /**
     * Get Tile Code
     *
     * @throws Saas_Launcher_Exception
     * @return string
     */
    public function getTileCode()
    {
        $tile = $this->getTile();
        if (!isset($tile)) {
            throw new Saas_Launcher_Exception('Tile was not set.');
        }
        return $tile->getTileCode();
    }

    /**
     * Get Tile State
     *
     * @throws Saas_Launcher_Exception
     * @return int
     */
    public function getTileState()
    {
        $tile = $this->getTile();
        if (!isset($tile)) {
            throw new Saas_Launcher_Exception('Tile was not set.');
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
            case Saas_Launcher_Model_Tile::STATE_TODO :
                $tileStyle = 'tile-todo';
                break;
            case Saas_Launcher_Model_Tile::STATE_COMPLETE :
                $tileStyle = 'tile-complete';
                break;
            case Saas_Launcher_Model_Tile::STATE_SKIPPED :
                $tileStyle = 'tile-skipped';
                break;
            case Saas_Launcher_Model_Tile::STATE_DISMISSED :
                $tileStyle = 'tile-dismissed';
                break;
            default:
                $tileStyle = 'tile-todo';
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
