<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Page\Product;

use Mtf\Page\FrontendPage;

/**
 * Class CatalogProductView
 */
class CatalogProductView extends FrontendPage
{
    const MCA = 'bundle/catalog/product/view';

    protected $_blocks = [
        'bundleViewBlock' => [
            'name' => 'bundleViewBlock',
            'class' => 'Magento\Bundle\Test\Block\Catalog\Product\View',
            'locator' => '.bundle-options-container',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Bundle\Test\Block\Catalog\Product\View
     */
    public function getBundleViewBlock()
    {
        return $this->getBlockInstance('bundleViewBlock');
    }
}
