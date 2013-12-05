<?php
/**
 * Integration grid.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Block\Adminhtml\Integration;

use Magento\Backend\Block\Widget\Grid as BackendGrid;

class Grid extends BackendGrid
{
    /**
     * Disable javascript callback on row clicking.
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        return '';
    }

    /**
     * Disable javascript callback on row init.
     *
     * @return string
     */
    public function getRowInitCallback()
    {
        return '';
    }
}
