<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Constraint;

use Magento\Integration\Test\Fixture\Integration;
use Magento\Integration\Test\Page\Adminhtml\IntegrationIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertIntegrationSuccessActivationMessage
 * Assert that success activation message is correct
 */
class AssertIntegrationSuccessActivationMessage extends AbstractConstraint
{
    const SUCCESS_ACTIVATION_MESSAGE = "The integration '%s' has been activated.";

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that success activation message is appeared on the Integrations page
     *
     * @param IntegrationIndex $integrationIndexPage
     * @param Integration $integration
     * @return void
     */
    public function processAssert(
        IntegrationIndex $integrationIndexPage,
        Integration $integration
    ) {
        $expectedMessage = sprintf(self::SUCCESS_ACTIVATION_MESSAGE, $integration->getName());
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
        return 'Integration success activation message is correct.';
    }
}
