<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Test\Block\Adminhtml\Invitation;

/**
 * Class Grid
 * Invitations grid on backend
 */
class Grid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'email' => [
            'selector' => 'input[name="email"]',
        ],
        'status' => [
            'selector' => 'select[name="status"]',
            'input' => 'strictselect',
        ],
        'invitee_group' => [
            'selector' => 'select[name="group_id"]',
            'input' => 'select',
        ],
    ];
}
