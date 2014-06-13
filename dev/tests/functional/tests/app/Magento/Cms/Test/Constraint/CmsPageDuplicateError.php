<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\Adminhtml\CmsIndex;

/**
 * Class CmsPageDuplicateError
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
     * @param CmsIndex $cmsIndex
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex)
    {
        $message = $cmsIndex->getMessagesBlock();
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
