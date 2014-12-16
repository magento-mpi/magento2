<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Install\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestStep\TestStepFactory;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Assert that Secure Urls Enabled.
 */
class AssertSecureUrlEnabled extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that Secure Urls Enabled.
     *
     * @param CatalogProductSimple $productSimple
     * @param TestStepFactory $stepFactory
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $productSimple,
        TestStepFactory $stepFactory,
        Browser $browser,
        FixtureFactory $fixtureFactory
    ) {
        $config = $fixtureFactory->createByCode('configData', ['dataSet' => 'secure_url']);
        $config->persist();
        $browser->getUrl();
        \PHPUnit_Framework_Assert::assertTrue(
            strpos($browser->getUrl(), 'https://') !== false,
            'Secure Urls are not displayed on backend.'
        );

        $productSimple->persist();
        $stepFactory->create(
            'Magento\Catalog\Test\TestStep\AddProductsToTheCartStep',
            ['products' => [$productSimple]]
        )->run();
        $stepFactory->create('Magento\Catalog\Test\TestStep\ProceedToCheckoutStep')->run();
        \PHPUnit_Framework_Assert::assertTrue(
            strpos($browser->getUrl(), 'https://') !== false,
            'Secure Urls are not displayed on frontend.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Secure Urls display successful.';
    }
}
