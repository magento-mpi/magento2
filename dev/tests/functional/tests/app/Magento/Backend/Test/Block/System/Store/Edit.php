<?php
/**
 * Store edit form
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Test\Block\System\Store;

class Edit extends \Magento\Backend\Test\Block\Widget\Form
{
    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->_mapping = array(
            'group' => '#store_group_id',
            'name' => '#store_name',
            'code' => '#store_code',
            'is_active' => '#store_is_active',
            'sort_order' => '#store_sort_order',
        );
    }
}