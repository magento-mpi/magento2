<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Block\Adminhtml\Cms\Page\Version\Edit;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class RevisionsGrid
 * Adminhtml Version Revisions management grid
 */
class RevisionsGrid extends Grid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'revision_number_from' => [
            'selector' => '[name="revision_number[from]"]',
        ],
        'revision_number_to' => [
            'selector' => '[name="revision_number[to]"]',
        ],
        'author' => [
            'selector' => 'select[name="author"]',
            'input' => 'select',
        ],
    ];

    /**
     * An element locator which allows to select first entity in grid
     *
     * @var string
     */
    protected $editLink = 'td[class*=col-number]';
}
