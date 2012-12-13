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
 * Base Drawer Block
 *
 * @method Mage_Launcher_Model_Tile getTile()
 * @method Mage_Launcher_Block_Adminhtml_Drawer setTile(Mage_Launcher_Model_Tile $value)
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Adminhtml_Drawer extends Mage_Backend_Block_Widget_Form
{
    /**
     * Path to template file
     *
     * @todo Default template specified, but it should be changed to custom one
     * @var string
     */
    protected $_template = 'Mage_Backend::widget/form.phtml';

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
     * Get Translated Tile Header
     *
     * @todo This function should get data from Tile model
     * @return string
     */
    public function getTileHeader()
    {
        //@TODO: This function should get data from Tile model
        return '';
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
            'tile_content' => $this->toHtml(),
            'tile_header' => $this->getTileHeader(),
        );
        return $responseContent;
    }
}
