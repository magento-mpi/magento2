<?php
/**
 * Created by PhpStorm.
 * User: orykh
 * Date: 14.04.14
 * Time: 13:44
 */

namespace Magento\Customer\Test\Block\Adminhtml\Group;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class CustomerGroupGrid
 * Adminhtml customer group grid
 *
 * @package Magento\Catalog\Test\Block
 */
class CustomerGroupGrid extends Grid
{
    /**
     * Initialize block elements
     */
    protected $filters = array(
        'type' => array(
            'selector' => '#customerGroupGrid_filter_type'
        )
    );

    /**
     * Update attributes for selected items
     *
     * @param array $items
     */
    public function updateAttributes(array $items = array())
    {
        $this->massaction('Update Attributes', $items);
    }
}
