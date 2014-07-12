<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\Adminhtml\Page\Widget;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class Chooser
 * Backend select page, block grid
 */
class Chooser extends Grid
{
    protected $filters = [
        'chooser_identifier' => [
            'selector' => 'input[name="chooser_identifier"]'
        ],
    ];

    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = 'tr[title="#"] td';
}
