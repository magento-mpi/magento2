<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *  Container for theme grid
 */
class Magento_Theme_Block_Adminhtml_System_Design_Theme extends Magento_Backend_Block_Widget_Grid_Container
{
    /**
     * Initialize grid container and prepare controls
     */
    public function _construct()
    {
        parent::_construct();
        $this->_blockGroup = 'Magento_Theme';
        $this->_controller = 'Adminhtml_System_Design_Theme';
        if (is_object($this->getLayout()->getBlock('page-title'))) {
            $this->getLayout()->getBlock('page-title')->setPageTitle('Themes');
        }
        
        $this->_updateButton('add', 'label', __('Add New Theme'));
    }

    /**
     * Prepare header for container
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Themes');
    }
}
