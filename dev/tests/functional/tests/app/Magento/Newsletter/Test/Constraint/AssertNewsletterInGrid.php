<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Test\Constraint;

use Magento\Newsletter\Test\Fixture\NewsletterTemplate;
use Magento\Newsletter\Test\Page\Adminhtml\NewsletterTemplateIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertNewsletterInGrid
 *
 * @package Magento\Newsletter\Test\Constraint
 */
class AssertNewsletterInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that newsletter template in grid
     *
     * @param NewsletterTemplateIndex $newsletterTemplateIndex
     * @param NewsletterTemplate $newsletterTemplate
     * @return void
     */
    public function processAssert(
        NewsletterTemplateIndex $newsletterTemplateIndex,
        NewsletterTemplate $newsletterTemplate
    ) {
        $newsletterTemplateIndex->open();
        $filter = ['code' => $newsletterTemplate->getCode()];
        \PHPUnit_Framework_Assert::assertTrue(
            $newsletterTemplateIndex->getNewsletterTemplateGrid()->isRowVisible($filter),
            'Newsletter \'' . $newsletterTemplate->getCode() . '\'is absent in newsletter template grid.'
        );
    }

    /**
     * Success assert of newsletter template in grid.
     *
     * @return string
     */
    public function toString()
    {
        return 'Newsletter template in grid.';
    }
}
