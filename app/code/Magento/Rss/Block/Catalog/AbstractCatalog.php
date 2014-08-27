<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Block\Catalog;

class AbstractCatalog extends \Magento\Rss\Block\AbstractBlock
{
    /**
     * Block alias fallback
     */
    const DEFAULT_TYPE = 'default';

    /**
     * Stored price block instances
     * @var array
     */
    protected $_priceBlock = array();

    /**
     * Whether to show "As low as" as a link
     * @var bool
     */
    protected $_useLinkForAsLowAs = true;

    /**
     * Get rendered price html
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param bool $displayMinimalPrice
     * @return string
     */
    public function renderPriceHtml(\Magento\Catalog\Model\Product $product, $displayMinimalPrice = false)
    {
        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                [
                    'display_minimal_price'  => $displayMinimalPrice,
                    'use_link_for_as_low_as' => $this->_useLinkForAsLowAs,
                    'zone'                   => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST
                ]
            );
        }

        return $price;
    }
}
