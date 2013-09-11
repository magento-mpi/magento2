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
namespace Magento\AdminGws\Block\Adminhtml\Permissions\Grid;

class Role extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * Add allowed websites/stores column
     *
     * @return \Magento\AdminGws\Block\Adminhtml\Permissions\Grid\Role
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('gws', array(
            'header'   => __('Allowed Scopes'),
            'width'    => '200',
            'sortable' => false,
            'filter'   => false,
            'renderer' => '\Magento\AdminGws\Block\Adminhtml\Permissions\Grid\Renderer\Gws'
        ));

        return $this;
    }
}
