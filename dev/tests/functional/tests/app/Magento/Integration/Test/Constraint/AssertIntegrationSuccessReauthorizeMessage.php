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
 * Class AssertIntegrationSuccessReauthorizeMessage
 * Assert that success reauthorize message is correct.
 */
class AssertIntegrationSuccessReauthorizeMessage extends AbstractConstraint
{
    /**
     * Integration success reauthorize message.
     */
    const SUCCESS_REAUTHORIZE_MESSAGE = "The integration '%s' has been re-authorized.";

    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that success reauthorize message is appeared on the Integrations page.
     *
     * @param IntegrationIndex $integrationIndexPage
     * @param Integration $integration
     * @return void
     */
    public function processAssert(
        IntegrationIndex $integrationIndexPage,
        Integration $integration
    ) {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_REAUTHORIZE_MESSAGE, $integration->getName()),
            $integrationIndexPage->getMessagesBlock()->getSuccessMessages(),
            "Wrong success message is displayed."
        );
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return 'Integration success reauthorize message is correct.';
    }
}
