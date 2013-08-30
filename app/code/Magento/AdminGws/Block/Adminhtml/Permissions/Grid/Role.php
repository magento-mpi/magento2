<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminGws
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin roles grid
 *
 */
class Magento_AdminGws_Block_Adminhtml_Permissions_Grid_Role extends Magento_Backend_Block_Widget_Grid
{
    /**
     * Add allowed websites/stores column
     *
     * @return Magento_AdminGws_Block_Adminhtml_Permissions_Grid_Role
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('gws', array(
            'header'   => __('Allowed Scopes'),
            'width'    => '200',
            'sortable' => false,
            'filter'   => false,
            'renderer' => 'Magento_AdminGws_Block_Adminhtml_Permissions_Grid_Renderer_Gws'
        ));

        return $this;
    }
}
