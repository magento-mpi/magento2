<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Block\Catalog\Product\View\Type\Option;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Radio
 * Bundle option radiobutton type
 *
 */
class Radio extends Form
{
    /**
     * Set data in bundle option
     *
     * @param array $data
     */
    public function fillOption(array $data)
    {
        $this->_rootElement->find('//*[contains(text(), ' . $data['value'] . ')]', Locator::SELECTOR_XPATH)->click();
        $this->_rootElement->find($this->mapping['qty']['selector'])->setValue($data['qty']);
    }
}
