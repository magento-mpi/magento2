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
 * Related Tab
 *
 * @package Magento\Catalog\Test\Block\Product
 */
class Related extends Tab
{
    const GROUP = 'product_info_tabs_related';

    /**
     * @param array $products
     * @param Element $context
     */
    public function fillFormTab(array $products, Element $context = null)
    {
        $element = $context ? : $this->_rootElement;
        $relatedBlock = Factory::getBlockFactory()->getMagentoCatalogAdminhtmlProductEditTabRelatedGrid(
            $element->find('#related_product_grid')
        );
        foreach ($products['related_products']['value'] as $product) {
            $relatedBlock->searchAndSelect($product);
        }
    }
}