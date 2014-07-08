<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Page\Product;

use Mtf\Page\FrontendPage;

/**
 * Class CatalogProductCompare
 * Frontend product compare page
 */
class CatalogProductCompare extends FrontendPage
{
    const MCA = 'catalog/product_compare/index';

    protected $_blocks = [
        'compareProductsBlock' => [
            'name' => 'compareProductsBlock',
            'class' => 'Magento\Catalog\Test\Block\Product\Compare\ListCompare\Interceptor',
            'locator' => '.column.main',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * Get compare products block
     *
     * @return \Magento\Catalog\Test\Block\Product\Compare\ListCompare\Interceptor
     */
    public function getCompareProductsBlock()
    {
        return $this->getBlockInstance('compareProductsBlock');
    }
}
