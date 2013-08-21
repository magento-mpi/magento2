<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Banner_Block_Adminhtml_Banner extends Magento_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * Initialize banners manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_banner';
        $this->_blockGroup = 'Magento_Banner';
        $this->_headerText = __('Banners');
        $this->_addButtonLabel = __('Add Banner');
        parent::_construct();
    }
}
