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
 * Cross-sell Tab
 *
 * @package Magento\Catalog\Test\Block\Product
 */
class Crosssell extends Tab
{
    const GROUP = 'crosssells';

    /**
     * Select cross-sells products
     *
     * @param array $products
     * @param Element $context
     * @return $this
     */
    public function fillFormTab(array $products, Element $context = null)
    {
        if (!isset($products['crosssell_products'])) {
            return $this;
        }
        $element = $context ? : $this->_rootElement;
        $crossSellBlock = Factory::getBlockFactory()->getMagentoCatalogAdminhtmlProductEditTabCrosssellGrid(
            $element->find('#cross_sell_product_grid')
        );
        foreach ($products['crosssell_products']['value'] as $product) {
            $crossSellBlock->searchAndSelect($product);
        }

        return $this;
    }
}
