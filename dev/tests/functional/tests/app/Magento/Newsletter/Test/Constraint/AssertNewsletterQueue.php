<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Test\Constraint;

use Mtf\Constraint\AbstractAssertForm;
use Magento\Newsletter\Test\Fixture\Template;
use Magento\Newsletter\Test\Page\Adminhtml\TemplateQueue;

/**
 * Class AssertNewsletterQueue
 * Assert that "Edit Queue" page opened and subject, sender name, sender email and template content correct
 */
class AssertNewsletterQueue extends AbstractAssertForm
{
    /**
     * Skipped fields for verify data
     *
     * @var array
     */
    protected $skippedFields = ['code'];

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that "Edit Queue" page opened and subject, sender name, sender email and template content correct
     *
     * @param TemplateQueue $templateQueue
     * @param Template $newsletter
     * @return void
     */
    public function processAssert(TemplateQueue $templateQueue, Template $newsletter)
    {
        $dataDiff = $this->verifyData($newsletter->getData(), $templateQueue->getEditForm()->getData($newsletter));
        \PHPUnit_Framework_Assert::assertEmpty($dataDiff, 'Edit Queue content not correct information.');
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Edit Queue content equals to passed from fixture.';
    }
}
