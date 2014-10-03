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
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Upsell\Grid as UpsellGrid;

/**
 * Class Upsell
 * Up-sells Tab
 */
class Upsell extends AbstractRelated
{
    /**
     * Related products type
     *
     * @var string
     */
    protected $relatedType = 'up_sell_products';

    /**
     * Locator for cross sell products grid
     *
     * @var string
     */
    protected $crossSellGrid = '#up_sell_product_grid';

    /**
     * Return related products grid
     *
     * @param Element|null $element [optional]
     * @return UpsellGrid
     */
    protected function getRelatedGrid(Element $element = null)
    {
        $element = $element ? $element : $this->_rootElement;
        return $this->blockFactory->create(
            '\Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Upsell\Grid',
            ['element' => $element->find($this->crossSellGrid)]
        );
    }
}
