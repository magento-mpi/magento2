<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *  Container for theme grid
 */
class Mage_Adminhtml_Block_System_Design_Theme extends Mage_Backend_Block_Widget_Grid_Container
{
    /**
     * Initialize grid container and prepare controls
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'Mage_Adminhtml';
        $this->_controller = 'System_Design_Theme';
        $this->_updateButton('add', 'label', $this->__('Add New Theme'));
    }

    /**
     * Prepare header for container
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('Themes');
    }
}
