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
 */
class AssertIntegrationInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert Integration availability in integration grid
     *
     * @param IntegrationIndex $integrationIndexPage
     * @param Integration $integration
     * @return void
     */
    public function processAssert(
        IntegrationIndex $integrationIndexPage,
        Integration $integration
    ) {
        $filter = [
            'name' => $integration->getName(),
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
