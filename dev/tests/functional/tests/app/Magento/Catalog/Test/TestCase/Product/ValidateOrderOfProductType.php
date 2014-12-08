<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

class ValidateOrderOfProductType extends Functional
{
    /**
     * @var array
     */
    protected $menu = [
        'Simple Product',
        'Configurable Product',
        'Grouped Product',
        'Virtual Product',
        'Bundle Product',
        'Downloadable Product',
        'Gift Card',
    ];

    /**
     * Check order and filling of types on product page
     */
    public function testOrderType()
    {
        Factory::getApp()->magentoBackendLoginUser();
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $productGridPage->open();
        $this->assertEquals(
            implode("\n", $this->menu),
            $productGridPage->getAddNewSpliteButtonBlock()->getTypeList(),
            'This test should be used for EE edition only'
        );
    }
}
