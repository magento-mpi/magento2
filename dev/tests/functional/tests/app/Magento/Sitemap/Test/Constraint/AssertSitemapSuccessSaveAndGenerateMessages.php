<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sitemap\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sitemap\Test\Page\Adminhtml\SitemapIndex;
use Magento\Sitemap\Test\Fixture\Sitemap;

/**
 * Class AssertSitemapSuccessSaveAndGenerateMessages
 */
class AssertSitemapSuccessSaveAndGenerateMessages extends AbstractConstraint
{
    const SUCCESS_GENERATE_MESSAGE = 'The sitemap "%s" has been generated.';

    const SUCCESS_SAVE_MESSAGE = 'The sitemap has been saved.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success messages is displayed after sitemap generate
     *
     * @param SitemapIndex $sitemapIndex
     * @param Sitemap $sitemap
     * @return void
     */
    public function processAssert(SitemapIndex $sitemapIndex, Sitemap $sitemap)
    {
        $actualMessages = $sitemapIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertTrue(
            in_array(self::SUCCESS_SAVE_MESSAGE, $actualMessages) &&
            in_array(sprintf(self::SUCCESS_GENERATE_MESSAGE, $sitemap->getSitemapFilename()), $actualMessages),
            'Wrong success messages is displayed.'
            . "\nExpected: " . sprintf(self::SUCCESS_GENERATE_MESSAGE, $sitemap->getSitemapFilename())
            . "\nExpected: " . self::SUCCESS_SAVE_MESSAGE
            . "\nActual messages: " . $this->allMessages($actualMessages)
        );
    }

    /**
     * Combines all messages in string
     *
     * @param array $actualMessages
     * @return string
     */
    protected function allMessages(array $actualMessages)
    {
        $messages = '';
        foreach ($actualMessages as $message) {
            $messages .= "\n" . $message;
        }

        return $messages;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Sitemap success generate and save messages is present.';
    }
}
