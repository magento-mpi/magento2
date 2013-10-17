<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Product\Configurable\Tab\Variations;

use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Form;

/**
 * Class CurrentVariations
 * Configurable variations
 *
 * @package Magento\Catalog\Test\Block\Product\Configurable\Tab\Variations
 */
class CurrentVariations extends Form
{
    /**
     * Fill qty to current variations
     */
    public function fillFormQty(array $items)
    {
        //@TODO
        sleep(2);

        $i = 0;
        foreach ($items as $item) {
            ++$i;
            $input = $this->_rootElement->find('#product-variations-matrix tr:nth-child(' . $i . ') .col-qty input');
            $input->setValue($item['product_quantity']);
        }
    }
}
