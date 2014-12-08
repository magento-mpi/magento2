<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Test\Constraint;

use Magento\Newsletter\Test\Fixture\Template;
use Magento\Newsletter\Test\Page\Adminhtml\TemplatePreview;
use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertNewsletterPreview
 * Assert that newsletter preview opened in new window and template content correct
 */
class AssertNewsletterPreview extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that newsletter preview opened in new window and template content correct
     *
     * @param Browser $browser
     * @param TemplatePreview $templatePreview
     * @param Template $newsletter
     * @return void
     */
    public function processAssert(Browser $browser, TemplatePreview $templatePreview, Template $newsletter)
    {
        $browser->selectWindow();
        $content = $templatePreview->getContent()->getPageContent();
        $browser->closeWindow();
        \PHPUnit_Framework_Assert::assertEquals(
            $newsletter->getText(),
            $content,
            'Template content not correct information.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Newsletter preview opened in new window and has valid content.';
    }
}
