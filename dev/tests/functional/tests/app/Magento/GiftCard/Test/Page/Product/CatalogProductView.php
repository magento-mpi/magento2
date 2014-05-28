<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Page\Product;

use Magento\Catalog\Test\Page\Product\CatalogProductView as ParentCatalogProductView;

/**
 * Class CatalogProductView
 * Frontend product view page
 */
class CatalogProductView extends ParentCatalogProductView
{
    const MCA = 'gift_card/catalog/product/view';

    /**
     * Custom constructor
     *
     * @return void
     */
    protected function _init()
    {
        $this->_blocks['giftCardBlock'] = [
            'name' => 'giftCardBlock',
            'class' => 'Magento\GiftCard\Test\Block\Catalog\Product\View\Type\GiftCard',
            'locator' => '.product.info.main',
            'strategy' => 'css selector',
        ];
        parent::_init();
    }

    /**
     * @return \Magento\GiftCard\Test\Block\Catalog\Product\View\Type\GiftCard
     */
    public function getGiftCardBlock()
    {
        return $this->getBlockInstance('giftCardBlock');
    }
}
