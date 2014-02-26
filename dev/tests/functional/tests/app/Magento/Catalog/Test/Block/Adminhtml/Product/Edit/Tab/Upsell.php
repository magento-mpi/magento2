<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Factory\Factory;

/**
 * Upsell Tab
 *
 * @package Magento\Catalog\Test\Block\Product
 */
class Upsell extends Tab
{
    const GROUP = 'upsells';

    /**
     * Select up-sell products
     *
     * @param array $products
     * @param Element $context
     * @return $this
     */
    public function fillFormTab(array $products, Element $context = null)
    {
        if (!isset($products['upsell_products'])) {
            return $this;
        }
        $element = $context ? : $this->_rootElement;
        $upSellBlock = Factory::getBlockFactory()->getMagentoCatalogAdminhtmlProductEditTabUpsellGrid(
            $element->find('#up_sell_product_grid')
        );
        foreach ($products['upsell_products']['value'] as $product) {
            $upSellBlock->searchAndSelect($product);
        }

        return $this;
    }
}