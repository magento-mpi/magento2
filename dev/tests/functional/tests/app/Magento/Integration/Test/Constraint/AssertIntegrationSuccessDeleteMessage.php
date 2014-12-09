<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Integration\Test\Page\Adminhtml\IntegrationIndex;
use Magento\Integration\Test\Fixture\Integration;

/**
 * Class AssertIntegrationSuccessDeleteMessage
 * Assert that success delete message is appeared on the Integrations page
 */
class AssertIntegrationSuccessDeleteMessage extends AbstractConstraint
{
    const SUCCESS_DELETE_MESSAGE = "The integration '%s' has been deleted.";

    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert that success delete message is appeared on the Integrations page
     *
     * @param IntegrationIndex $integrationIndexPage
     * @param Integration $integration
     * @return void
     */
    public function processAssert(IntegrationIndex $integrationIndexPage, Integration $integration)
    {
        $expectedMessage = sprintf(self::SUCCESS_DELETE_MESSAGE, $integration->getName());
        $actualMessage = $integrationIndexPage->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            $expectedMessage,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . $expectedMessage
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Returns a string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return 'Integrations success delete message is correct.';
    }
}
