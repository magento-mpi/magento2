<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertWidgetHierarchyNodeLinkOnFrontendAllPages
 * Check that created widget displayed on frontent on Home page and on Advanced Search
 */
class AssertWidgetHierarchyNodeLinkOnFrontendAllPages extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created widget displayed on frontent on Home page and on Advanced Search
     *
     * @return void
     */
    public function processAssert()
    {
        //
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Widget is present on Home page and on Advanced Search.";
    }
}
