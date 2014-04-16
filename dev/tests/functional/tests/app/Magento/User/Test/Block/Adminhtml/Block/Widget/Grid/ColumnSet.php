<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Block\Adminhtml\Block\Widget\Grid;


use Magento\Backend\Test\Block\Widget\Grid;

class ColumnSet extends Grid{

    protected $filters = array(
        'id' => array(
            'selector' => '#roleGrid_filter_role_id'
        ),
        'role_name' => array(
            'selector' => '#roleGrid_filter_role_name'
        )
    );
} 