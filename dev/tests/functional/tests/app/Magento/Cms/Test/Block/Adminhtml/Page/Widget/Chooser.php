<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Cms\Test\Block\Adminhtml\Page\Widget;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class Chooser
 * Backend select page, block grid
 */
class Chooser extends Grid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'chooser_identifier' => [
            'selector' => 'input[name="chooser_identifier"]',
        ],
    ];

    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = 'tbody tr .col-chooser_title';
}
