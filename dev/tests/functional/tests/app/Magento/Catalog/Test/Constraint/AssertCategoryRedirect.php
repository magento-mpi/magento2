<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogCategory;

/**
 * Class AssertCategoryRedirect
 * Assert that old Category URL lead to appropriate Category in frontend
 */
class AssertCategoryRedirect extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that old Category URL lead to appropriate Category in frontend
     *
     * @param CatalogCategory $category
     * @param Browser $browser
     * @param CatalogCategory $initialCategory
     * @return void
     */
    public function processAssert(CatalogCategory $category, Browser $browser, CatalogCategory $initialCategory)
    {
        $browser->open($_ENV['app_frontend_url'] . $initialCategory->getUrlKey() . '.html');

        \PHPUnit_Framework_Assert::assertEquals(
            $browser->getUrl(),
            $_ENV['app_frontend_url'] . strtolower($category->getUrlKey()) . '.html',
            'URL rewrite category redirect false.'
        );
    }

    /**
     * URL rewrite category redirect success
     *
     * @return string
     */
    public function toString()
    {
        return 'URL rewrite category redirect success.';
    }
}
