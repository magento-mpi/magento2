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

namespace Magento\Bundle\Test\Block\Catalog\Product\View\Type\Option;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Select
 * Bundle option dropdown type
 *
 * @package Magento\Bundle\Test\Block\Catalog\Product\View\Type\Option
 */
class Select extends Form
{
    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->_mapping = array(
            'value' => '.input-box select',
            'qty' => '.qty-holder input'
        );
    }

    /**
     * Set data in bundle option
     *
     * @param array $data
     */
    public function fillOption(array $data)
    {
        $rootElement = $this->_rootElement;
        $rootElement->find($this->_mapping['value'], Locator::SELECTOR_CSS, $rootElement::TYPIFIED_ELEMENT_SELECT)
            ->setValue($data['value']);
        $rootElement->find($this->_mapping['qty'], Locator::SELECTOR_CSS)->setValue($data['qty']);
    }
}
