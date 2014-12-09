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
 * Class AssertIntegrationNotInGrid
 * Assert that Integration is not presented in grid and cannot be found using name
 */
class AssertIntegrationNotInGrid extends AbstractConstraint
{
    /* tags */
     const SEVERITY = 'high';
     /* end tags */

    /**
     * Assert that Integration is not presented in grid and cannot be found using name
     *
     * @param IntegrationIndex $integrationIndexPage
     * @param Integration $integration
     * @return void
     */
    public function processAssert(IntegrationIndex $integrationIndexPage, Integration $integration)
    {
        $filter = ['name' => $integration->getName()];

        $integrationIndexPage->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $integrationIndexPage->getIntegrationGrid()->isRowVisible($filter),
            'Integration \'' . $filter['name'] . '\' is present in Integration grid.'
        );
    }

    /**
     * Returns a string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return 'Integration is absent in grid.';
    }
}
