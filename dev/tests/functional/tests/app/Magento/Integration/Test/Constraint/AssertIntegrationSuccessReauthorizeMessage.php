<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Integration\Test\Constraint;

use Magento\Integration\Test\Fixture\Integration;
use Magento\Integration\Test\Page\Adminhtml\IntegrationIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertIntegrationSuccessReauthorizeMessage
 * Assert that success reauthorize message is correct.
 */
class AssertIntegrationSuccessReauthorizeMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Integration success reauthorize message.
     */
    const SUCCESS_REAUTHORIZE_MESSAGE = "The integration '%s' has been re-authorized.";

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
