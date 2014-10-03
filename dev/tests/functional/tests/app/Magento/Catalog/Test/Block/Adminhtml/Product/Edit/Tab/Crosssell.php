<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab;

use Mtf\Client\Element;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Crosssell\Grid as CrosssellGrid;

/**
 * Class Crosssell
 * Cross-sells Tab
 */
class Crosssell extends AbstractRelated
{
    /**
     * Related products type
     *
     * @var string
     */
    protected $relatedType = 'cross_sell_products';

    /**
     * Locator for cross sell products grid
     *
     * @var string
     */
    protected $crossSellGrid = '#cross_sell_product_grid';

    /**
     * Return cross sell products grid
     *
     * @param Element $element
     * @return CrosssellGrid
     */
    protected function getRelatedGrid(Element $element = null)
    {
        return $this->blockFactory->create(
            '\Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Crosssell\Grid',
            ['element' => $element->find($this->crossSellGrid)]
        );
    }
}
