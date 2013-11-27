<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Block\Backend;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class UserGrid
 * Grid on System ->Permissions -> All Users page
 * @package Magento\User\Test\Block\Backend
 */
class UserGrid extends Grid
{
    /**
     * Link to click on Email cell for user
     *
     * @var string
     */
    protected $editLink = '//td[@data-column="email"]';

    /**
     * Initialize grid elements
     */
    protected function _init()
    {
        parent::_init();
        $this->filters = array(
            'id' => array(
                'selector' => '#permissionsUserGrid_filter_user_id'
            ),
            'user_name' => array(
                'selector' => '#permissionsUserGrid_filter_username'
            ),
            'first_name' => array(
                'selector' => '#permissionsUserGrid_filter_firstname'
            ),
            'last_name' => array(
                'selector' => '#permissionsUserGrid_filter_lastname'
            ),
            'email' => array(
                'selector' => '#permissionsUserGrid_filter_email'
            ),
            'status' => array(
                'selector' => '#permissionsUserGrid_filter_is_active',
                'input' => 'select'
            )
        );
    }
}

