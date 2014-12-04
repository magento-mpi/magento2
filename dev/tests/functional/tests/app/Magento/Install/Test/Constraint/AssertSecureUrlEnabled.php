<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Install\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\TestStep\TestStepFactory;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Assert that Secure Urls Enabled.
 */
class AssertSecureUrlEnabled extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that Secure Urls Enabled.
     *
     * @param CatalogProductSimple $productSimple
     * @param TestStepFactory $stepFactory
     * @param Browser $browser
     * @return void
     */
    public function processAssert(CatalogProductSimple $productSimple, TestStepFactory $stepFactory, Browser $browser)
    {
        $browser->getUrl();
        \PHPUnit_Framework_Assert::assertTrue(
            strpos($browser->getUrl(), 'https://') !== false,
            'Assert that Secure Urls not displayed on backend.'
        );

        $productSimple->persist();
        $stepFactory->create(
            'Magento\Catalog\Test\TestStep\AddProductsToTheCartStep',
            ['products' => [$productSimple]]
        )->run();
        $stepFactory->create('Magento\Catalog\Test\TestStep\ProceedToCheckoutStep')->run();
        \PHPUnit_Framework_Assert::assertTrue(
            strpos($browser->getUrl(), 'https://') !== false,
            'Assert that Secure Urls not displayed on frontend.'
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
