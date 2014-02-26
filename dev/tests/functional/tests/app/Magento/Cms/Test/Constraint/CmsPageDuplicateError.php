<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\AdminHtml\CmsPageGrid;

/**
 * Class CmsPageDuplicateError
 *
 * @package Magento\Backend\Test\Constraint
 */
class CmsPageDuplicateError extends AbstractConstraint
{
    /**
     * @inheritdoc
     */
    protected $severeness = 'low';

    /**
     * Verify that page has not been created
     *
     * @param CmsPageGrid $cmsPageGrid
     * @return void
     */
    public function processAssert(CmsPageGrid $cmsPageGrid)
    {
        $message = $cmsPageGrid->getMessageBlock();
        $this->result = $message->assertErrorMessage();
    }

    /**
     * @inheritdoc
     */
    public function toString()
    {
        return 'Assert that page with duplicated identifier has not been created.';
    }
}
