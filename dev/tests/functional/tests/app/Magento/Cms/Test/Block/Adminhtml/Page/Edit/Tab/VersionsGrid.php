<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\Adminhtml\Page\Edit\Tab;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class VersionsGrid
 * Cms page versions grid block
 */
class VersionsGrid extends Grid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'label' => [
            'selector' => 'select[name="label"]',
            'input' => 'select',
        ],
        'owner' => [
            'selector' => 'select[name="owner"]',
            'input' => 'select',
        ],
        'access_level' => [
            'selector' => 'select[name="access_level"]',
            'input' => 'select',
        ],
        'quantity' => [
            'selector' => 'input[name="revisions[from]"]',
        ],
    ];
}
