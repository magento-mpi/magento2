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
 * Class VariationsForm
 * Configurable variations
 *
 * @package Magento\Catalog\Test\Block\Product\Configurable\Tab\Variations
 */
class VariationsForm extends Form
{
    /**
     * Fill data to variations rows
     */
    public function fillFormPrice(array $items)
    {
        $i = 0;
        foreach ($items as $item) {
            ++$i;
            $input = $this->_rootElement->
                find('[data-role="option-container"]:nth-child(' . $i . ') [name*="[pricing_value]"]');
            $input->setValue($item['product_price']);
        }
    }
}
