<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Test\TestCase;

use Magento\Newsletter\Test\Fixture\NewsletterTemplate;
use Magento\Newsletter\Test\Page\Adminhtml\NewsletterTemplateIndex;
use Magento\Newsletter\Test\Page\Adminhtml\NewsletterTemplateNewIndex;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for Create Newsletter Template
 *
 * Test Flow:
 * 1.Login to backend
 * 2.Navigate to MARKETING-> Newsletter Template
 * 3.Add New Template
 * 4.Fill in all data according to data set
 * 5.Save
 * 6.Perform asserts
 *
 * @group Newsletters_(MX)
 * @ZephyrId MAGETWO-23302
 */
class CreateNewsletterTemplate extends Injectable
{
    /** @var NewsletterTemplateNewIndex $newsletterTemplateNewIndex */
    private $newsletterTemplateNewIndex;

    /** @var NewsletterTemplateIndex $newsletterTemplateIndex */
    private $newsletterTemplateIndex;

    /**
     * Inject newsletter page
     *
     * @param NewsletterTemplateIndex $newsletterTemplateIndex
     * @param NewsletterTemplateNewIndex $newsletterTemplateNewIndex
     */
    public function __inject(
        NewsletterTemplateIndex $newsletterTemplateIndex,
        NewsletterTemplateNewIndex $newsletterTemplateNewIndex
    ) {
        $this->newsletterTemplateIndex = $newsletterTemplateIndex;
        $this->newsletterTemplateNewIndex = $newsletterTemplateNewIndex;
    }

    /**
     * Create newsletter template
     *
     * @param NewsletterTemplate $newsletterTemplate
     */
    public function testCreateNewsletterTemplate(NewsletterTemplate $newsletterTemplate)
    {
        // Steps
        $this->newsletterTemplateIndex->open();
        $this->newsletterTemplateIndex->getGridPageActions()->addNew();
        $this->newsletterTemplateNewIndex->getPageMainForm()->fill($newsletterTemplate);
        $this->newsletterTemplateNewIndex->getPageMainActions()->save();
    }
}
