<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Integration\Test\Fixture\Integration;
use Magento\Integration\Test\Page\Adminhtml\IntegrationIndex;

/**
 * Class AssertIntegrationInGrid
 * Assert Integration availability in integration grid
 */
class AssertIntegrationInGrid extends AbstractConstraint
{
    /* tags */
     const SEVERITY = 'high';
     /* end tags */

    /**
     * Assert that data in grid on Integrations page according to fixture by name field
     *
     * @param IntegrationIndex $integrationIndexPage
     * @param Integration $integration
     * @param Integration|null $initialIntegration
     * @return void
     */
    public function processAssert(
        IntegrationIndex $integrationIndexPage,
        Integration $integration,
        Integration $initialIntegration = null
    ) {
        $filter = [
            'name' => ($initialIntegration !== null && !$integration->hasData('name'))
                ? $initialIntegration->getName()
                : $integration->getName(),
        ];

        $integrationIndexPage->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $integrationIndexPage->getIntegrationGrid()->isRowVisible($filter),
            'Integration \'' . $filter['name'] . '\' is absent in Integration grid.'
        );
    }

    /**
     * Returns a string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return 'Integration is present in grid.';
    }
}
