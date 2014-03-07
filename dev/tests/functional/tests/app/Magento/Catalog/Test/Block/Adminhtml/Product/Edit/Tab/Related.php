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
use Mtf\Factory\Factory;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Related Tab
 *
 * @package Magento\Catalog\Test\Block\Product
 */
class Related extends Tab
{
    const GROUP = 'related-products';

    /**
     * Select related products
     *
     * @param array $products
     * @param Element $context
     * @return $this
     */
    public function fillFormTab(array $products, Element $context = null)
    {
        if (!isset($products['related_products'])) {
            return $this;
        }
        $element = $context ? : $this->_rootElement;
        $relatedBlock = Factory::getBlockFactory()->getMagentoCatalogAdminhtmlProductEditTabRelatedGrid(
            $element->find('#related_product_grid')
        );
        foreach ($products['related_products']['value'] as $product) {
            $relatedBlock->searchAndSelect($product);
        }

        return $this;
    }
}