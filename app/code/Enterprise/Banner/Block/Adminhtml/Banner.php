<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Banner_Block_Adminhtml_Banner extends Magento_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * Initialize banners manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_banner';
        $this->_blockGroup = 'Enterprise_Banner';
        $this->_headerText = __('Banners');
        $this->_addButtonLabel = __('Add Banner');
        parent::_construct();
    }
}
